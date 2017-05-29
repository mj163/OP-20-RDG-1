<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/headers/class.duparchive.header.php');
require_once(dirname(__FILE__).'/states/class.duparchive.state.create.php');
require_once(dirname(__FILE__).'/states/class.duparchive.state.simplecreate.php');
require_once(dirname(__FILE__).'/states/class.duparchive.state.expand.php');
require_once(dirname(__FILE__).'/processors/class.duparchive.processor.file.php');
require_once(dirname(__FILE__).'/processors/class.duparchive.processor.directory.php');
require_once(dirname(__FILE__).'/class.duparchive.processing.failure.php');
require_once(dirname(__FILE__).'/util/class.duparchive.util.php');
require_once(dirname(__FILE__).'/util/class.duparchive.util.scan.php');

class DupArchiveInfo
{
    public $archiveHeader;
    public $fileHeaders;
    public $directoryHeaders;

    public function __construct()
    {
        $this->fileHeaders = array();
        $this->directoryHeaders = array();
    }
}

class DupArchiveItemAlias
{
    public $oldName;
    public $newName;

}

class DupArchiveItemHeaderType
{
    const None      = 0;
    const File      = 1;
    const Directory = 2;
    const Glob      = 3;

}

class DupArchiveEngine
{
    public static function init($loggingFunction, $profilingFunction = null)
    {
        DupArchiveUtil::$loggingFunction = $loggingFunction;
        DupArchiveUtil::$profilingFunction = $profilingFunction;
    }

    public static function getNextHeaderType($archiveHandle)
    {
        $retVal = DupArchiveItemHeaderType::None;
        $marker = fgets($archiveHandle, 4);

        if (feof($archiveHandle) === false) {
            switch ($marker) {
                case '?D#':
                    $retVal = DupArchiveItemHeaderType::Directory;
                    break;

                case '?F#':
                    $retVal = DupArchiveItemHeaderType::File;
                    break;

                case '?G#':
                    $retVal = DupArchiveItemHeaderType::Glob;
                    break;

                default:
                    throw new Exception("Invalid header marker {$marker}. Location:" . ftell($archiveHandle));
            }
        }

        return $retVal;
    }

    public static function getArchiveInfo($filepath)
    {
        $archiveInfo = new DupArchiveInfo();

        DupArchiveUtil::profileEvent('getArchiveInfo > open archive', true);
        $archiveHandle = DupArchiveUtil::fopen($filepath, 'r');
        DupArchiveUtil::profileEvent('getArchiveInfo > open archive', false);
        $moreFiles     = true;

        DupArchiveUtil::profileEvent('getArchiveInfo > read archive header', true);
        $archiveInfo->archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);
        DupArchiveUtil::profileEvent('getArchiveInfo > read archive header', false);

        $moreToRead = true;
        while ($moreToRead) {

            $headerType = self::getNextHeaderType($archiveHandle);

            switch ($headerType) {
                case DupArchiveItemHeaderType::File:

                    DupArchiveUtil::profileEvent('getArchiveInfo > read file header', true);
                    $fileHeader                 = DupArchiveFileHeader::readFromArchive($archiveHandle, true, true);
                    DupArchiveUtil::profileEvent('getArchiveInfo > read file header', false);
                    $archiveInfo->fileHeaders[] = $fileHeader;
                    break;

                case DupArchiveItemHeaderType::Directory:
//                    DUP_PRO_LOG::trace("Archive offset for directory" . ftell($archiveHandle));
                    DupArchiveUtil::profileEvent('getArchiveInfo > read directory header', true);
                    $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);
                    DupArchiveUtil::profileEvent('getArchiveInfo > read directory header', false);

                    $archiveInfo->directoryHeaders[] = $directoryHeader;
                    break;

                case DupArchiveItemHeaderType::None:
                    $moreToRead = false;
            }
        }

        return $archiveInfo;
    }

    // can't span requests since create state can't store list of files
    public static function addDirectoryToArchiveST($archiveFilepath, $directory, $basepath, $includeFiles = false, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        if($includeFiles)
        {
            $scan = DupArchiveScanUtil::createScanObject($directory);
        }
        else
        {
            $scan->Files = array();
            $scan->Dirs = array();
        }

        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath   = $archiveFilepath;
        $createState->basePath      = $basepath;
        $createState->timerEnabled  = false;
        $createState->globSize      = $globSize;

        self::addItemsToArchive($createState, $scan);
    }

    public static function addRelativeFileToArchiveST($archiveFilepath, $filepath, $relativePath, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath   = $archiveFilepath;
        $createState->basePath      = null;
        $createState->timerEnabled  = false;
        $createState->globSize      = $globSize;

        $scan = new stdClass();

        $scan->Files = array();
        $scan->Dirs  = array();

        $scan->Files[] = $filepath;

        if($relativePath != null) {

            $scan->FileAliases = array();
            $scan->FileAliases[$filepath] = $relativePath;
        }

        self::addItemsToArchive($createState, $scan);
    }

    public static function addFileToArchiveUsingBaseDirST($archiveFilepath, $basePath, $filepath, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath   = $archiveFilepath;
        $createState->basePath      = $basePath;
        $createState->timerEnabled  = false;
        $createState->globSize      = $globSize;

        $scan = new stdClass();

        $scan->Files = array();
        $scan->Dirs  = array();
        
        $scan->Files[] = $filepath;

        self::addItemsToArchive($createState, $scan);
    }

    public static function createArchive($archivePath, $isCompressed)
    {
        $archiveHandle = DupArchiveUtil::fopen($archivePath, 'w+');

        /* @var $archiveHeader DupArchiveHeader */
        $archiveHeader = DupArchiveHeader::create($isCompressed);

        DupArchiveUtil::tlogObject('archive header', $archiveHeader);
        $archiveHeader->writeToArchive($archiveHandle);

        DupArchiveUtil::tlog("Wrote archive header.  New ftell=".DupArchiveUtil::ftell($archiveHandle));
        // Intentionally do not write build state since if something goes wrong we went it to start over on the archive

        DupArchiveUtil::fclose($archiveHandle);
    }

    public static function addItemsToArchive($createState, $scanFSInfo)
    {         
        if ($createState->globSize == -1) {

            $createState->globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE;
        }
        /* @var $createState DupArchiveCreateState */
        DupArchiveUtil::tlogObject("addItemsToArchive start", $createState);
        DupArchiveUtil::tlog("Scan file count:" . count($scanFSInfo->Files));

        $directoryCount = count($scanFSInfo->Dirs);
        $fileCount      = count($scanFSInfo->Files);

        if($fileCount > 1)
        {
//            DupArchiveUtil::profileEvent("addItemsToArchive (multiple)", true);
//            DupArchiveUtil::profileEvent("addItemsToArchive (multiple) before file add", true);
        }
        else
        {
           // DupArchiveUtil::profileEvent("addItemsToArchive", true);
        }

        $createState->startTimer();

        /* @var $createState DupArchiveCreateState */
        $basepath = dirname(__FILE__);

        $archiveHandle = DupArchiveUtil::fopen($createState->archivePath, 'r+');

        DupArchiveUtil::tlog("Archive size=", filesize($createState->archivePath));
        DupArchiveUtil::tlog("Archive location is now ".DupArchiveUtil::ftell($archiveHandle));

        $archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);

        $createState->isCompressed = $archiveHeader->isCompressed;

        if ($createState->archiveOffset == filesize($createState->archivePath)) {
            DupArchiveUtil::tlog("Seeking to end of archive location because of offset {$createState->archiveOffset} for file size ".filesize($createState->archivePath));
            DupArchiveUtil::fseek($archiveHandle, 0, SEEK_END);
        } else {
            DupArchiveUtil::tlog("Seeking archive offset {$createState->archiveOffset} for file size ".filesize($createState->archivePath));
            DupArchiveUtil::fseek($archiveHandle, $createState->archiveOffset);
        }
        
        while (($createState->currentDirectoryIndex < $directoryCount) && (!$createState->timedOut())) {

            usleep($createState->throttleDelayInUs);

            $directory = $scanFSInfo->Dirs[$createState->currentDirectoryIndex];

            DupArchiveUtil::tlog("Creating directory ".$directory);
            try {
                $relativeDirectoryPath = null;

                if(isset($scanFSInfo->DirectoryAliases) && array_key_exists($directory, $scanFSInfo->DirectoryAliases))
                {
                    $relativeDirectoryPath = $scanFSInfo->DirectoryAliases[$directory];
                }
                else
                {
                    $relativeDirectoryPath =DupArchiveUtil::getRelativePath($createState->basePath, $directory);
                }
                
                DupArchiveUtil::tlog("RELATIVE DIR PATH {$relativeDirectoryPath}");

                DupArchiveDirectoryProcessor::writeDirectoryToArchive($createState, $archiveHandle, $directory, $relativeDirectoryPath);
            } catch (Exception $ex) {
                DupArchiveUtil::log("Failed to add {$directory} to archive. Error: ".$ex->getMessage(), true);

                $createState->addFailure(DupArchiveFailureTypes::Directory, $directory, $ex->getMessage());
                $createState->save();
            }
        }

        $createState->archiveOffset = DupArchiveUtil::ftell($archiveHandle);

        if($fileCount > 1)
        {
           // DupArchiveUtil::profileEvent("addItemsToArchive (multiple) before file add", false);
        }

        while (($createState->currentFileIndex < $fileCount) && (!$createState->timedOut())) {

            usleep($createState->throttleDelayInUs);

            DupArchiveUtil::tlog("!!!!FILE INDEX = {$createState->currentFileIndex} out of $fileCount files ");
            $filepath = $scanFSInfo->Files[$createState->currentFileIndex];

            DupArchiveUtil::tlog("Writing filepath ".$filepath);
            try {

                $relativeFilePath = null;

                if(isset($scanFSInfo->FileAliases) && array_key_exists($filepath, $scanFSInfo->FileAliases))
                {
                    $relativeFilePath = $scanFSInfo->FileAliases[$filepath];
                }
                else
                {
                    $relativeFilePath = DupArchiveUtil::getRelativePath($createState->basePath, $filepath);
                }

                $fileWritten = DupArchiveFileProcessor::writeFilePortionToArchive($createState, $archiveHandle, $filepath, $relativeFilePath);

            } catch (Exception $ex) {
                DupArchiveUtil::log("Failed to add {$filepath} to archive. Error: ".$ex->getMessage() . $ex->getTraceAsString(), true);
                $createState->currentFileIndex++;
                DupArchiveUtil::tlog("before add failure", true);
                $createState->addFailure(DupArchiveFailureTypes::File, $filepath, $ex->getMessage());
                DupArchiveUtil::tlog("after add failure", true);
                $createState->save();
                DupArchiveUtil::tlog("state save", true);
            }
        }

        if($fileCount > 1)
        {
         //   DupArchiveUtil::profileEvent("addItemsToArchive (multiple) after file add", true);
         //   DupArchiveUtil::profileEvent("addItemsToArchive (multiple) createstate save", true);
        }

        $createState->working = ($createState->currentDirectoryIndex < $directoryCount) || ($createState->currentFileIndex < $fileCount);

        $createState->save();

        if($fileCount > 1)
        {
     //       DupArchiveUtil::profileEvent("addItemsToArchive (multiple) createstate save", false);
     //       DupArchiveUtil::profileEvent("addItemsToArchive (multiple) closing archive", true);
        }


        DupArchiveUtil::fclose($archiveHandle);

        if($fileCount > 1)
        {
        //    DupArchiveUtil::profileEvent("addItemsToArchive (multiple) closing archive", false);
        }


        if (!$createState->working) {
            DupArchiveUtil::log("compress done");
        } else {
            DupArchiveUtil::tlog("compress not done so continuing later");
        }

        if($fileCount > 1)
        {
          //  DupArchiveUtil::profileEvent("addItemsToArchive (multiple)", false);
        //    DupArchiveUtil::profileEvent("addItemsToArchive (multiple) after file add", false);
        }
        else
        {
         //   DupArchiveUtil::profileEvent("addItemsToArchive", false);
        }
    }

    public static function expandArchive($expandState)
    {

        DupArchiveUtil::profileEvent('expandArchive', true);
        /* @var $expandState DupArchiveExpandState */
      //  DupArchiveUtil::tlogObject("Expand Archive Start", $expandState);

        $expandState->startTimer();

        $archiveHandle = DupArchiveUtil::fopen($expandState->archivePath, 'r');

        DupArchiveUtil::fseek($archiveHandle, $expandState->archiveOffset);

        if ($expandState->archiveOffset == 0) {
            
            DupArchiveUtil::log("#### seeking to start of archive");


            $expandState->archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);
            $expandState->isCompressed  = $expandState->archiveHeader->isCompressed;
            $expandState->archiveOffset = DupArchiveUtil::ftell($archiveHandle);

            $expandState->save();


            DupArchiveUtil::tlog("Ftell after archive header read".ftell($archiveHandle));

        } else {

            DupArchiveUtil::log("#### seeking archive offset {$expandState->archiveOffset}");

        }

        if($expandState->validationType == DupArchiveValidationTypes::Full)
        {
            $moreItems = self::expandItems($expandState, $archiveHandle);
        }
        else
        {
            $moreItems = self::standardValidateItems($expandState, $archiveHandle);
        }
        
        $expandState->working = $moreItems;
        $expandState->save();        

        DupArchiveUtil::fclose($archiveHandle);

        if (!$expandState->working) {

            DupArchiveUtil::log("expand done");

            // RSR TODO: Only do the file count checks against the scan during create
            if (($expandState->expectedFileCount != -1) && ($expandState->expectedFileCount != $expandState->fileWriteCount)) {

                $expandState->addFailure(DupArchiveFailureTypes::File, 'Archive',
                    "Number of files expected ({$expandState->expectedFileCount}) doesn't equal number written ({$expandState->fileWriteCount}).");
            }

            if (($expandState->expectedDirectoryCount != -1) && ($expandState->expectedDirectoryCount != $expandState->directoryWriteCount)) {
                $expandState->addFailure(DupArchiveFailureTypes::Directory, 'Archive',
                    "Number of directories expected ({$expandState->expectedDirectoryCount}) doesn't equal number written ({$expandState->directoryWriteCount}).");
            }

            //RSR TODO: Verify # of directories and files within archive
        } else {
            DupArchiveUtil::tlog("expand not done so continuing later");
        }

        DupArchiveUtil::profileEvent('expandArchive', false);
    }

    private static function expandItems(&$expandState, $archiveHandle)
    {
        DupArchiveUtil::profileEvent('expandItems', true);

        $moreToRead = true;


        while ($moreToRead && (!$expandState->timedOut())) {

            usleep($expandState->throttleDelayInUs);

            //DupArchiveUtil::tlogObject('expandstate', $expandState);

            if ($expandState->currentFileHeader != null) {

                DupArchiveUtil::tlog("Writing file {$expandState->currentFileHeader->relativePath}");

                try {

                    
                    $fileCompleted = DupArchiveFileProcessor::writeToFile($expandState, $archiveHandle);

                    if ($fileCompleted) {
                        $expandState->currentFileHeader = null;
                    }

                  //  DupArchiveUtil::profileEvent('expandItems > expandstatesave1', true);
                  //  $expandState->save();
                  //  DupArchiveUtil::profileEvent('expandItems > expandstatesave1', false);

                    // Expand state taken care of within the write to file to ensure consistency
                } catch (Exception $ex) {

                    DupArchiveUtil::profileEvent('expandItems > exception1', true);
                    DupArchiveUtil::log("Failed to write to {$expandState->currentFileHeader->relativePath} to archive. Error: ".$ex->getMessage(), true);
                    //   $expandState->currentFileIndex++;
                    // RSR TODO: Need way to skip past that file

                    $expandState->addFailure(DupArchiveFailureTypes::File, $filepath, $ex->getMessage());
                    $expandState->save();
                    DupArchiveUtil::profileEvent('expandItems > exception1', false);
                }
            } else {

                DupArchiveUtil::profileEvent('expandItems > getnextheadertype', true);
                $headerType = self::getNextHeaderType($archiveHandle);
                DupArchiveUtil::profileEvent('expandItems > getnextheadertype', false);

                DupArchiveUtil::tlog("header type $headerType");
                switch ($headerType) {
                    case DupArchiveItemHeaderType::File:
                        DupArchiveUtil::tlog("File header");
                        DupArchiveUtil::profileEvent('expandItems > fileheader read from archive', true);
                        $expandState->currentFileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false, true);

                        $expandState->archiveOffset = ftell($archiveHandle);
                       // $expandState->save();
                        DupArchiveUtil::profileEvent('expandItems > fileheader read from archive',false);
                        DupArchiveUtil::tlog("Just read file header from archive");

                        break;

                    case DupArchiveItemHeaderType::Directory:
                        DupArchiveUtil::tlog("Directory Header");

                        $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                        if (!$expandState->validateOnly) {
                            $directory = "{$expandState->basePath}/{$directoryHeader->relativePath}";

                            DupArchiveUtil::mkdir($directory, $directoryHeader->permissions, true);
                        }

                        $expandState->directoryWriteCount++;
                        $expandState->archiveOffset = ftell($archiveHandle);
                      //  $expandState->save();
                        DupArchiveUtil::tlog("Just read directory header {$directoryHeader->relativePath} from archive");
                        break;

                    case DupArchiveItemHeaderType::None:
                        $moreToRead = false;
                }
            }
        }

         $expandState->save();

        DupArchiveUtil::profileEvent('expandItems', false);
        return $moreToRead;
    }

    private static function standardValidateItems(&$expandState, $archiveHandle)
    {
        DupArchiveUtil::profileEvent('quickValidateItems', true);

        $moreToRead = true;

        while ($moreToRead && (!$expandState->timedOut())) {

            usleep($expandState->throttleDelayInUs);
            
            if ($expandState->currentFileHeader != null) {

                try {

                    DupArchiveUtil::profileEvent('quickValidateItems > file', true);
                    $fileCompleted = DupArchiveFileProcessor::standardValidateFileEntry($expandState, $archiveHandle);

                    if ($fileCompleted) {
                        $expandState->currentFileHeader = null;
                    }

                    DupArchiveUtil::profileEvent('quickValidateItems > file', false);

                    // Expand state taken care of within the write to file to ensure consistency
                } catch (Exception $ex) {

                    DupArchiveUtil::profileEvent('quickValidateItems > exception1', true);
                    DupArchiveUtil::log("Failed validate {$expandState->currentFileHeader->relativePath} in archive. Error: ".$ex->getMessage(), true);
                    //   $expandState->currentFileIndex++;
                    // RSR TODO: Need way to skip past that file

                    $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, $ex->getMessage());
                    $expandState->save();
                    DupArchiveUtil::profileEvent('quickValidateItems > exception1', false);

                    $moreToRead = false;
                }
            } else {

                DupArchiveUtil::profileEvent('quickValidateItems > getnextheadertype', true);
                $headerType = self::getNextHeaderType($archiveHandle);
                DupArchiveUtil::profileEvent('quickValidateItems > getnextheadertype', false);

                switch ($headerType) {
                    case DupArchiveItemHeaderType::File:

                        DupArchiveUtil::profileEvent('quickValidateItems > fileheader read from archive', true);
                        $expandState->currentFileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false, true);

                        $expandState->archiveOffset = ftell($archiveHandle);

                        DupArchiveUtil::profileEvent('quickValidateItems > fileheader read from archive',false);

                        break;

                    case DupArchiveItemHeaderType::Directory:

                        $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                        $expandState->directoryWriteCount++;
                        $expandState->archiveOffset = ftell($archiveHandle);

                        break;

                    case DupArchiveItemHeaderType::None:
                        $moreToRead = false;
                }
            }
        }

         $expandState->save();

        DupArchiveUtil::profileEvent('quickValidateItems', false);
        return $moreToRead;
    }

//    private static function expandDirectories(&$expandState, $archiveHandle)
//    {
//        $moreDirectories = true;
//
//        //RSR TODO: Indicate when the files are missing that the scan says should be there.
//        while ($moreDirectories && (!$expandState->timedOut())) {
//
//            if (DupArchiveDirectoryHeader::isDirectoryHeader($archiveHandle)) {
//
//                /* @var directoryHeader DupArchiveDirectoryHeader */
//                $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle);
//
//                if ($expandState->validateOnly) {
//                    $directory = "{$expandState->basePath}/{$directoryHeader->relativePath}";
//
//                    DupArchiveUtil::mkdir($directory, $directoryHeader->permissions, true);
//                }
//
//                $expandState->directoryWriteCount++;
//                $expandState->archiveOffset = ftell($archiveHandle);
//                $expandState->save();
//                DupArchiveUtil::tlog("Just read directory header {$directoryHeader->relativePath} from archive");
//            } else {
//                DupArchiveUtil::tlog("No more directories in the archive");
//                $moreDirectories = false;
//            }
//        }
//
//        return $moreDirectories;
//    }
//    private static function expandFiles(&$expandState, $archiveHandle)
//    {
//        $moreFiles = true;
//
//        //RSR TODO: Indicate when the files are missing that the scan says should be there.
//        while ($moreFiles && (!$expandState->timedOut())) {
//
//            if ($expandState->currentFileHeader == null) {
//
//                $expandState->currentFileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false);
//
//                $expandState->archiveOffset = ftell($archiveHandle);
//                $expandState->save();
//                DupArchiveUtil::tlog("Just read file header from archive");
//            } else {
//                DupArchiveUtil::tlog("Current file header not null");
//            }
//
//            DupArchiveUtil::tlogObject('expandstate', $expandState);
//
//            if ($expandState->currentFileHeader != null) {
//
//                DupArchiveUtil::tlog("Writing file {$expandState->currentFileHeader->relativePath}");
//
//                try {
//
//                    $fileCompleted = DupArchiveFileProcessor::writeToFile($expandState, $archiveHandle);
//
//                    // Expand state taken care of within the write to file to ensure consistency
//                } catch (Exception $ex) {
//                    DupArchiveUtil::log("Failed to write to {$expandState->currentFileHeader->relativePath} to archive. Error: ".$ex->getMessage(), true);
//                    //   $expandState->currentFileIndex++;
//                    // RSR TODO: Need way to skip past that file
//
//                    $expandState->addFailure(DupArchiveFailureTypes::File, $filepath, $ex->getMessage());
//                    $expandState->save();
//                }
//            } else {
//                // If its still null after we try to retrieve it indicates we have reached the end of the archive
//                $moreFiles = false;
//            }
//        }
//
//        return $moreFiles;
//    }
}
