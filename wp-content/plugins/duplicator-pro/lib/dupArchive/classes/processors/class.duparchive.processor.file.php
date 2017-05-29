<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once(dirname(__FILE__).'/../headers/class.duparchive.header.file.php');
require_once(dirname(__FILE__).'/../headers/class.duparchive.header.glob.php');


class DupArchiveFileProcessor
{
    public static function writeFilePortionToArchive($createState, $archiveHandle, $sourceFilepath, $relativeFilePath)
    {
        /* @var $createState DupArchiveCreateState */

        DupArchiveUtil::tlog("writeFileToArchive for {$sourceFilepath}");

        DupArchiveUtil::profileEvent('writeFilePortionToArchive > source open', true);

        $sourceHandle = DupArchiveUtil::fopen($sourceFilepath, 'r');

        DupArchiveUtil::profileEvent('writeFilePortionToArchive > source open', false);

        if ($createState->currentFileOffset > 0) {
            DupArchiveUtil::tlog("Continuing {$sourceFilepath} so seeking to {$createState->currentFileOffset}");


            DupArchiveUtil::profileEvent('writeFilePortionToArchive > continue seek', true);
            DupArchiveUtil::fseek($sourceHandle, $createState->currentFileOffset);
            DupArchiveUtil::profileEvent('writeFilePortionToArchive > continue seek', false);

        } else {
            DupArchiveUtil::tlog("Starting new file entry for {$sourceFilepath}");


            DupArchiveUtil::profileEvent('writeFilePortionToArchive > create fheader', true);
            $fileHeader = DupArchiveFileHeader::createFromFile($sourceFilepath, $relativeFilePath);
            DupArchiveUtil::profileEvent('writeFilePortionToArchive > create fheader', false);
            DupArchiveUtil::profileEvent('writeFilePortionToArchive > header->archive', true);
            $fileHeader->writeToArchive($archiveHandle);
            DupArchiveUtil::profileEvent('writeFilePortionToArchive > header->archive', false);
        }

          //DupArchiveUtil::writeToPLog("writefileportiontoarchive before filesize");

        DupArchiveUtil::profileEvent('writeFilePortionToArchive > get source fsize', true);
        $sourceFileSize = filesize($sourceFilepath);
        DupArchiveUtil::profileEvent('writeFilePortionToArchive > get source fsize', false);

        DupArchiveUtil::tlog("writeFileToArchive for {$sourceFilepath}, size {$sourceFileSize}");

        $moreFileDataToProcess = true;

          //DupArchiveUtil::writeToPLog("writefileportiontoarchive after filesize");

        while ((!$createState->timedOut()) && $moreFileDataToProcess) {

            DupArchiveUtil::tlog("Writing offset={$createState->currentFileOffset}");


             //DupArchiveUtil::writeToPLog("before append glob to archive");            
            $moreFileDataToProcess = self::appendGlobToArchive($createState, $archiveHandle, $sourceHandle, $sourceFilepath, $sourceFileSize);


             //DupArchiveUtil::writeToPLog("after append glob to archive");

            DupArchiveUtil::profileEvent('writeFilePortionToArchive > done+ftell', true);
            if ($moreFileDataToProcess) {

                DupArchiveUtil::tlog("Need to keep writing {$sourceFilepath} to archive");
                $createState->currentFileOffset += $createState->globSize;
                $createState->archiveOffset = DupArchiveUtil::ftell($archiveHandle); //??
            } else {

                $createState->archiveOffset     = DupArchiveUtil::ftell($archiveHandle);
                $createState->currentFileIndex++;
                $createState->currentFileOffset = 0;
            }

            DupArchiveUtil::profileEvent('writeFilePortionToArchive > done+ftell', false);

            if ($createState->currentFileIndex % 100 == 0) {
                DupArchiveUtil::log("Archive Offset={$createState->archiveOffset}; Current File Index={$createState->currentFileIndex}; Current File Offset={$createState->currentFileOffset}");
            }

            // Only writing state after full group of files have been written - less reliable but more efficient
           // $createState->save();

      //      DupArchiveUtil::tlog("Successfully saved create state");
        }

        DupArchiveUtil::profileEvent('writeFilePortionToArchive > fclose source', true);
        DupArchiveUtil::fclose($sourceHandle);
        DupArchiveUtil::profileEvent('writeFilePortionToArchive > fclose source', false);
         //DupArchiveUtil::writeToPLog("writefileportiontoarchive end");
    }

    // Assumption is that this is called at the beginning of a glob header since file header already writtern
    public static function writeToFile($expandState, $archiveHandle)
    {
        DupArchiveUtil::profileEvent('writeToFile', true);

        /* @var $expandState DupArchiveExpandState */
        $destFilepath = "{$expandState->basePath}/{$expandState->currentFileHeader->relativePath}";

        $parentDir = dirname($destFilepath);

        if (!file_exists($parentDir)) {
            DupArchiveUtil::profileEvent('writeToFile > mkparentdir', true);
            DupArchiveUtil::mkdir($parentDir, 0755, true);
            DupArchiveUtil::profileEvent('writeToFile > mkparentdir', false);
        }

        if ($expandState->currentFileOffset > 0) {
            DupArchiveUtil::profileEvent('writeToFile > reopen file', true);
            $destFileHandle = DupArchiveUtil::fopen($destFilepath, 'r+');

            DupArchiveUtil::tlog("Continuing {$destFilepath} so seeking to {$expandState->currentFileOffset}");

            DupArchiveUtil::fseek($destFileHandle, $expandState->currentFileOffset);
            DupArchiveUtil::profileEvent('writeToFile > reopen file', false);
        } else {
            DupArchiveUtil::tlog("Starting to write new file {$destFilepath}");
            DupArchiveUtil::profileEvent('writeToFile > new open file', true);
            $destFileHandle = DupArchiveUtil::fopen($destFilepath, 'w+');
            DupArchiveUtil::profileEvent('writeToFile > new open file', false);
        }

        DupArchiveUtil::tlog("writeToFile for {$destFilepath}, size {$expandState->currentFileHeader->fileSize}");

        $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

        if (!$moreGlobstoProcess) {

            if($expandState->validationType == DupArchiveValidationTypes::Full)
            {
                self::validateExpandedFile($expandState);
            }

            //    echo "{$expandState->fileWriteCount}<br/>";
            //is this required ? implies the header is set but nothing more to write
            $expandState->fileWriteCount++;
              DupArchiveUtil::tlog("FILE WRITE COUNT2={$expandState->fileWriteCount}");
              DupArchiveUtil::profileEvent('writeToFile > reset expand state for file', true);
            $expandState->resetForFile();
            $expandState->save();
            DupArchiveUtil::profileEvent('writeToFile > reset expand state for file', false);
        } else {
            //$c = 0;
            while ((!$expandState->timedOut()) && $moreGlobstoProcess) {

                usleep($expandState->throttleDelayInUs);

                DupArchiveUtil::tlog("Writing offset={$expandState->currentFileOffset}");

                DupArchiveUtil::profileEvent('writeToFile > appendglobtofile', true);
                self::appendGlobToFile($expandState, $archiveHandle, $destFileHandle, $destFilepath);
                DupArchiveUtil::profileEvent('writeToFile > appendglobtofile', false);

                $expandState->currentFileOffset = ftell($destFileHandle);
                $expandState->archiveOffset     = DupArchiveUtil::ftell($archiveHandle);

                $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

                if ($moreGlobstoProcess) {
                    DupArchiveUtil::tlog("Need to keep writing to {$destFilepath} because current file offset={$expandState->currentFileOffset} and file size={$expandState->currentFileHeader->fileSize}");
                } else {
                    // Reset the expand state here to ensure it stays consistent
                    DupArchiveUtil::tlog("Writing of {$destFilepath} to archive is done");

                    DupArchiveUtil::profileEvent('writeToFile > close file', true);
                  //  DupArchiveUtil::tlog("closing file handle for $destFilepath");
                    DupArchiveUtil::fclose($destFileHandle);
                    $destFileHandle = null;
                    DupArchiveUtil::profileEvent('writeToFile > close file', false);

                    if($expandState->validationType == DupArchiveValidationTypes::Full)
                    {
                        self::validateExpandedFile($expandState);
                    }

                    //    echo "{$expandState->fileWriteCount}<br/>";
                    $expandState->fileWriteCount++;

                    DupArchiveUtil::tlog("FILE WRITE COUNT2={$expandState->fileWriteCount}");

                    $expandState->resetForFile();
                }

                // Crash before the write offset is recorded
                //$this->try_crash($source_filepath, $file_offset);
                if (rand(0, 1000) > 990) {
                    DupArchiveUtil::log("Archive Offset={$expandState->archiveOffset}; Current File={$destFilepath}; Current File Offset={$expandState->currentFileOffset}");
                }

            //    DupArchiveUtil::profileEvent('writeToFile > expandstatesave', true);
            //    $expandState->save();
             //   DupArchiveUtil::profileEvent('writeToFile > expandstatesave', false);
                //      Util::tlog("Successfully saved expand state");
            }
        }

        if($destFileHandle != null)
        {
            DupArchiveUtil::profileEvent('writeToFile > close file', true);
          //  DupArchiveUtil::tlog("closing file handle for $destFilepath");
            DupArchiveUtil::fclose($destFileHandle);
            $destFileHandle = null;
            DupArchiveUtil::profileEvent('writeToFile > close file', false);
        }

        if (!$moreGlobstoProcess && $expandState->validateOnly && ($expandState->validationType == DupArchiveValidationTypes::Full)) {
            DupArchiveUtil::profileEvent('writeToFile > delete file', true);
            @unlink($destFilepath);
            DupArchiveUtil::profileEvent('writeToFile > delete file', false);
        }

        DupArchiveUtil::profileEvent('writeToFile', false);
        return !$moreGlobstoProcess;
    }

    public static function standardValidateFileEntry($expandState, $archiveHandle)
    {
        DupArchiveUtil::profileEvent('quickValidateFileEntry', true);

        /* @var $expandState DupArchiveExpandState */

        $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

        if (!$moreGlobstoProcess) {

            $expandState->fileWriteCount++;
        } else {

            while ((!$expandState->timedOut()) && $moreGlobstoProcess) {

                // Read in the glob header but leave the pointer at the payload
                $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, false);

                $globContents = fread($archiveHandle, $globHeader->storedSize);

                if ($globContents === false) {
                    throw new Exception("Error reading glob from $destFilePath");
                }

                $md5 = md5($globContents);

                if($md5 != $globHeader->md5)
                {
                    $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, 'MD5 mismatch on DupArchive file entry');
                    DupArchiveUtil::tlog("Glob MD5 fails");
                }
                else
                {
                //    DupArchiveUtil::tlog("Glob MD5 passes");
                }

                $expandState->currentFileOffset += $globHeader->originalSize;
                $expandState->archiveOffset     = DupArchiveUtil::ftell($archiveHandle);

                $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

                if ($moreGlobstoProcess) {
              //      DupArchiveUtil::tlog("Need to keep validating {$expandState->currentFileHeader->relativePath} because current file offset={$expandState->currentFileOffset} and file size={$expandState->currentFileHeader->fileSize}");
                } else {
                    // Reset the expand state here to ensure it stays consistent
               //     DupArchiveUtil::tlog("Validating of {$expandState->currentFileHeader->relativePath} to archive is done");

                    $expandState->fileWriteCount++;

                    $expandState->resetForFile();
                }
            }
        }

        DupArchiveUtil::profileEvent('quickValidateFileEntry', false);

        return !$moreGlobstoProcess;
    }

//    public static function writeToFile($expandState, $archiveHandle)
//    {
//        DupArchiveUtil::profileEvent('writeToFile', true);
//
//        /* @var $expandState DupArchiveExpandState */
//        $destFilepath = "{$expandState->basePath}/{$expandState->currentFileHeader->relativePath}";
//
//        $parentDir = dirname($destFilepath);
//
//
//        DupArchiveUtil::tlog("writeToFile for {$destFilepath}, size {$expandState->currentFileHeader->fileSize}");
//
//        $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;
//
//        if (!$moreGlobstoProcess) {
//
//            self::validateExpandedFile($expandState);
//
//            //    echo "{$expandState->fileWriteCount}<br/>";
//            //is this required ? implies the header is set but nothing more to write
//            $expandState->fileWriteCount++;
//              DupArchiveUtil::tlog("FILE WRITE COUNT2={$expandState->fileWriteCount}");
//              DupArchiveUtil::profileEvent('writeToFile > reset expand state for file', true);
//            $expandState->resetForFile();
//            $expandState->save();
//            DupArchiveUtil::profileEvent('writeToFile > reset expand state for file', false);
//        } else {
//            if (!file_exists($parentDir)) {
//                DupArchiveUtil::profileEvent('writeToFile > mkparentdir', true);
//                DupArchiveUtil::mkdir($parentDir, 0755, true);
//                DupArchiveUtil::profileEvent('writeToFile > mkparentdir', false);
//            }
//
//            if ($expandState->currentFileOffset > 0) {
//                DupArchiveUtil::profileEvent('writeToFile > reopen file', true);
//                $destFileHandle = DupArchiveUtil::fopen($destFilepath, 'r+');
//
//                DupArchiveUtil::tlog("Continuing {$destFilepath} so seeking to {$expandState->currentFileOffset}");
//
//                DupArchiveUtil::fseek($destFileHandle, $expandState->currentFileOffset);
//                DupArchiveUtil::profileEvent('writeToFile > reopen file', false);
//            } else {
//                DupArchiveUtil::tlog("Starting to write new file {$destFilepath}");
//                DupArchiveUtil::profileEvent('writeToFile > new open file', true);
//                $destFileHandle = DupArchiveUtil::fopen($destFilepath, 'w+');
//                DupArchiveUtil::profileEvent('writeToFile > new open file', false);
//            }
//
//            //$c = 0;
//            while ((!$expandState->timedOut()) && $moreGlobstoProcess) {
//
//                DupArchiveUtil::tlog("Writing offset={$expandState->currentFileOffset}");
//
//                DupArchiveUtil::profileEvent('writeToFile > appendglobtofile', true);
//                self::appendGlobToFile($expandState, $archiveHandle, $destFileHandle, $destFilepath);
//                DupArchiveUtil::profileEvent('writeToFile > appendglobtofile', false);
//
//                $expandState->currentFileOffset = ftell($destFileHandle);
//                $expandState->archiveOffset     = DupArchiveUtil::ftell($archiveHandle);
//
//                $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;
//
//                if ($moreGlobstoProcess) {
//                    DupArchiveUtil::tlog("Need to keep writing to {$destFilepath} because current file offset={$expandState->currentFileOffset} and file size={$expandState->currentFileHeader->fileSize}");
//                } else {
//                    // Reset the expand state here to ensure it stays consistent
//                    DupArchiveUtil::tlog("Writing of {$destFilepath} to archive is done");
//
//
//                    DupArchiveUtil::profileEvent('writeToFile > close file', true);
//                    DupArchiveUtil::tlog("closing file handle for $destFilepath");
//                    DupArchiveUtil::fclose($destFileHandle, false);
//                    $destFileHandle = null;
//
//                    DupArchiveUtil::profileEvent('writeToFile > close file', false);
//
//                    self::validateExpandedFile($expandState);
//
//                    //    echo "{$expandState->fileWriteCount}<br/>";
//                    $expandState->fileWriteCount++;
//
//                    DupArchiveUtil::tlog("FILE WRITE COUNT2={$expandState->fileWriteCount}");
//
//                    $expandState->resetForFile();
//                }
//
//                // Crash before the write offset is recorded
//                //$this->try_crash($source_filepath, $file_offset);
//                if (rand(0, 1000) > 990) {
//                    DupArchiveUtil::log("Archive Offset={$expandState->archiveOffset}; Current File={$destFilepath}; Current File Offset={$expandState->currentFileOffset}");
//                }
//
//                DupArchiveUtil::profileEvent('writeToFile > expandstatesave', true);
//                $expandState->save();
//                DupArchiveUtil::profileEvent('writeToFile > expandstatesave', false);
//                //      Util::tlog("Successfully saved expand state");
//            }
//
//            if($destFileHandle != null)
//            {
//                DupArchiveUtil::profileEvent('writeToFile > close file', true);
//                DupArchiveUtil::tlog("closing file handle for $destFilepath");
//                DupArchiveUtil::fclose($destFileHandle, false);
//                DupArchiveUtil::profileEvent('writeToFile > close file', false);
//            }
//        }
//
//        if (!$moreGlobstoProcess && $expandState->validateOnly) {
//            DupArchiveUtil::profileEvent('writeToFile > delete file', true);
//            @unlink($destFilepath);
//            DupArchiveUtil::profileEvent('writeToFile > delete file', false);
//        }
//
//        DupArchiveUtil::profileEvent('writeToFile', false);
//        return !$moreGlobstoProcess;
//    }

    private static function validateExpandedFile(&$expandState)
    {
        /* @var $expandState DupArchiveExpandState */
        DupArchiveUtil::profileEvent('validateExpandedFile', true);


        $destFilepath = "{$expandState->basePath}/{$expandState->currentFileHeader->relativePath}";

        if ($expandState->currentFileHeader->md5 !== '00000000000000000000000000000000') {
            DupArchiveUtil::profileEvent('validateExpandedFile > md5_file', true);
            $md5 = md5_file($destFilepath);
            DupArchiveUtil::profileEvent('validateExpandedFile > md5_file', false);

            if ($md5 !== $expandState->currentFileHeader->md5) {
                $expandState->addFailure(DupArchiveFailureTypes::File, $destFilepath, 'MD5 mismatch');
            } else {
                DupArchiveUtil::tlog("MD5 Match for $destFilepath");
            }
        } else {
            DupArchiveUtil::tlog("MD5 non match is 0's");
        }

        DupArchiveUtil::profileEvent('validateExpandedFile', false);
    }

    private static function appendGlobToArchive($createState, $archiveHandle, $sourceFilehandle, $sourceFilepath, $fileSize)
    {
        DupArchiveUtil::profileEvent('appendGlobToArchive', true);

     //   DupArchiveUtil::tlog("Appending file glob to archive for file {$sourceFilepath} at file offset {$createState->currentFileOffset}");

        if ($fileSize > 0) {

            $fileSize -= $createState->currentFileOffset;

            DupArchiveUtil::profileEvent('appendGlobToArchive > read glob', true);
            $globContents = fread($sourceFilehandle, $createState->globSize);
            DupArchiveUtil::profileEvent('appendGlobToArchive > read glob', false);

            if ($globContents === false) {
                throw new Exception("Error reading $sourceFilepath");
            }

            DupArchiveUtil::profileEvent('appendGlobToArchive > strlen', true);
            $originalSize = strlen($globContents);
            DupArchiveUtil::profileEvent('appendGlobToArchive > strlen', false);

            if ($createState->isCompressed) {
                DupArchiveUtil::profileEvent('appendGlobToArchive > deflate', true);
                $globContents = gzdeflate($globContents);
                $storeSize    = strlen($globContents);
                DupArchiveUtil::profileEvent('appendGlobToArchive > deflate', false);
            } else {
                $storeSize = $originalSize;
            }

            DupArchiveUtil::profileEvent('appendGlobToArchive > md5', true);
            $md5 = md5($globContents);
            DupArchiveUtil::profileEvent('appendGlobToArchive > md5', false);

            // RSR TODO: What kind of issues will encounter when using strlen on binary data?
            DupArchiveUtil::profileEvent('appendGlobToArchive > create glob header', true);
            $globHeader = DupArchiveGlobHeader::createFromFile($originalSize, $storeSize, $md5);
            DupArchiveUtil::profileEvent('appendGlobToArchive > create glob header', false);

            DupArchiveUtil::profileEvent('appendGlobToArchive > write glob header to archive', true);
            $globHeader->writeToArchive($archiveHandle);
            DupArchiveUtil::profileEvent('appendGlobToArchive > write glob header to archive', false);

            DupArchiveUtil::profileEvent('appendGlobToArchive > write glob contents to archive', true);
            if (fwrite($archiveHandle, $globContents) === false) {
                throw new Exception("Error writing $sourceFilepath to archive");
            }
            DupArchiveUtil::profileEvent('appendGlobToArchive > write glob contents to archive', false);


            //$endLocation = DupArchiveUtil::ftell($archiveHandle);

            //DupArchiveUtil::tlog("destination location after write ".$endLocation);

            $fileSizeRemaining = $fileSize - $createState->globSize;

            $moreFileRemaining = $fileSizeRemaining > 0;

            DupArchiveUtil::profileEvent('appendGlobToArchive', false);
            return $moreFileRemaining;
        } else {
            // 0 Length file
            DupArchiveUtil::profileEvent('appendGlobToArchive', false);
            return false;
        }
    }

    // Assumption is that archive handle points to a glob header on this call
    private static function appendGlobToFile($expandState, $archiveHandle, $destFileHandle, $destFilePath)
    {
        /* @var $expandState DupArchiveExpandState */
        DupArchiveUtil::tlogObject('Expand State', $expandState);
        DupArchiveUtil::tlog("Appending file glob to file {$destFilePath} at file offset {$expandState->currentFileOffset}");

        // Read in the glob header but leave the pointer at the payload
        $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, false);

        $globContents = fread($archiveHandle, $globHeader->storedSize);

        if ($globContents === false) {
            throw new Exception("Error reading glob from $destFilePath");
        }

        //  Util::tlog("about to write contents to $destFilePath: " . $globContents);

        if ($expandState->isCompressed) {
            $globContents = gzinflate($globContents);
        }

        if (fwrite($destFileHandle, $globContents) === false) {
            throw new Exception("Error writing glob to $destFilePath");
        } else {
            DupArchiveUtil::tlog("Successfully wrote glob");
        }
    }
}
