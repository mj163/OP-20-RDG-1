<?php
if (!defined('DUPLICATOR_PRO_VERSION'))
    exit; // Exit if accessed directly

require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/class.pack.archive.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/duparchive/class.pack.archive.duparchive.state.expand.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/duparchive/class.pack.archive.duparchive.state.create.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.system.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'lib/dupArchive/classes/class.duparchive.engine.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'lib/dupArchive/classes/states/class.duparchive.state.create.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'lib/dupArchive/classes/states/class.duparchive.state.expand.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.duparchive.expandstate.entity.php');

/**
 *  DUP_PRO_ZIP
 *  Creates a zip file using the built in PHP ZipArchive class
 */
class DUP_PRO_Dup_Archive extends DUP_PRO_Archive
{

    /**
     *  CREATE
     *  Creates the zip file and adds the SQL file to the archive
     */
    public static function create(DUP_PRO_Archive $archive, $buildProgress)
    {     
        if ($buildProgress->retries > DUP_PRO_Constants::MAX_BUILD_RETRIES) {
            $error_msg              = DUP_PRO_U::__('Package build appears stuck so marking package as failed. Is the Max Worker Time set too high?.');
            DUP_PRO_Log::error(DUP_PRO_U::__('Build Failure'), $error_msg, false);
            $build_progress->failed = true;
            return true;
        } else {
            // If all goes well retries will be reset to 0 at the end of this function.
            $buildProgress->retries++;
            $archive->Package->update();
        }
                
        /* @var $archive DUP_PRO_Archive */
        /* @var $buildProgress DUP_PRO_Build_Progress */
        $done = false;

        $profileEventFunction = null;

        if (get_option(DUP_PRO_Constants::PROFILING_OPTION_NAME)) {
            $profileEventFunction = 'DUP_PRO_LOG::profileEvent';
        }

        DupArchiveEngine::init('DUP_PRO_LOG::trace', $profileEventFunction);

        $archive->Package->safe_tmp_cleanup(true);

        /* @var $global DUP_PRO_Global_Entity */
        $global = DUP_PRO_Global_Entity::get_instance();

        $compressDir = rtrim(DUP_PRO_U::safePath($archive->PackDir), '/');
        $sqlPath = DUP_PRO_U::safePath("{$archive->Package->StorePath}/{$archive->Package->Database->File}");
        $archivePath = DUP_PRO_U::safePath("{$archive->Package->StorePath}/{$archive->File}");

        $filterDirs = empty($archive->FilterDirs) ? 'not set' : $archive->FilterDirs;
        $filterExts = empty($archive->FilterExts) ? 'not set' : $archive->FilterExts;
        $filterFiles = empty($archive->FilterFiles) ? 'not set' : $archive->FilterFiles;
        $filterOn = ($archive->FilterOn) ? 'ON' : 'OFF';

        $scanFilepath = DUPLICATOR_PRO_SSDIR_PATH_TMP . "/{$archive->Package->NameHash}_scan.json";

        $skipArchiveFinalization = false;
        $json = '';

        if (file_exists($scanFilepath)) {

            $json = file_get_contents($scanFilepath);

            if (empty($json)) {
                $errorText = DUP_PRO_U::__("Scan file $scanFilepath is empty!");
                $fixText = DUP_PRO_U::__("Go to: Settings > Packages Tab > JSON to Custom.");

                DUP_PRO_LOG::trace($errorText);
                DUP_PRO_Log::error("$errorText **RECOMMENDATION:  $fixText.", '', false);

                $systemGlobal = DUP_PRO_System_Global_Entity::get_instance();

                $systemGlobal->add_recommended_text_fix($errorText, $fixText);

                $systemGlobal->save();

                $buildProgress->failed = true;
                return true;
            }
        } else {
            DUP_PRO_LOG::trace("**** scan file $scanFilepath doesn't exist!!");
            $errorMessage = sprintf(DUP_PRO_U::__("ERROR: Can't find Scanfile %s. Please ensure there no non-English characters in the package or schedule name."), $scanFilepath);

            DUP_PRO_Log::error($errorMessage, '', false);

            $buildProgress->failed = true;
            return true;
        }

        $scanReport = json_decode($json);

        if ($buildProgress->archive_started == false) {

            DUP_PRO_Log::info("\n********************************************************************************");
            DUP_PRO_Log::info("ARCHIVE Type=DUP Mode=DupArchive");
            DUP_PRO_Log::info("********************************************************************************");
            DUP_PRO_Log::info("ARCHIVE DIR:  " . $compressDir);
            DUP_PRO_Log::info("ARCHIVE FILE: " . basename($archivePath));
            DUP_PRO_Log::info("FILTERS: *{$filterOn}*");
            DUP_PRO_Log::info("DIRS:  {$filterDirs}");
            DUP_PRO_Log::info("EXTS:  {$filterExts}");
            DUP_PRO_Log::info("FILES:  {$filterFiles}");

            DUP_PRO_Log::info("----------------------------------------");
            DUP_PRO_Log::info("COMPRESSING");
            DUP_PRO_Log::info("SIZE:\t" . $scanReport->ARC->Size);
            DUP_PRO_Log::info("STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " . $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount);

            if (($scanReport->ARC->DirCount == '') || ($scanReport->ARC->FileCount == '') || ($scanReport->ARC->FullCount == '')) {
                DUP_PRO_Log::error('Invalid Scan Report Detected', 'Invalid Scan Report Detected', false);
                $buildProgress->failed = true;
                return true;
            }

            try
            {
                DupArchiveEngine::createArchive($archivePath, $global->archive_compression);
                DupArchiveEngine::addFileToArchiveUsingBaseDirST($archivePath, $compressDir, $sqlPath);
            } catch (Exception $ex) {
                DUP_PRO_Log::error('Error initializing archive', $ex->getMessage(), false);
                $buildProgress->failed = true;
                return true;
            }

            $buildProgress->archive_started = true;

            $archive->Package->Update();
        }

        try {
            if ($buildProgress->custom_data == null) {
                $createState = DUP_PRO_Dup_Archive_Create_State::createNew($archive->Package, $archivePath, $compressDir, $global->php_max_worker_time_in_sec, $global->archive_compression, true);
                $createState->throttleDelayInUs = DUP_PRO_Server_Load_Reduction::microseconds_from_reduction($global->server_load_reduction);
            } else {
                DUP_PRO_LOG::traceObject('Resumed build_progress', $archive->Package->build_progress);
                $createState = DUP_PRO_Dup_Archive_Create_State::createFromPackage($archive->Package);
            }

            if ($createState->working) {
                DupArchiveEngine::addItemsToArchive($createState, $scanReport->ARC);

                if(count($createState->failures) > 0)
                {
                    throw new Exception($createState->getFailureMessage());
                }

                $totalFileCount = count($scanReport->ARC->Files);

                $adjusted_percent = floor(DUP_PRO_PackageStatus::ARCSTART + ((DUP_PRO_PackageStatus::ARCDONE - DUP_PRO_PackageStatus::ARCSTART) * ($createState->currentFileIndex / (float) $totalFileCount)));

                $archive->Package->Status = $adjusted_percent;

                $createState->save();

                DUP_PRO_LOG::traceObject("Stored Create State", $createState);
                DUP_PRO_LOG::traceObject('Stored build_progress', $archive->Package->build_progress);

                if ($createState->working == false) {
                    // Want it to do the final cleanup work in an entirely new thread so return immediately
                    $skipArchiveFinalization = true;
                }
            }
        } catch (Exception $ex) {
            $message = DUP_PRO_U::__('Problem adding items to archive.') . ' ' . $ex->getMessage();

            DUP_PRO_Log::error(DUP_PRO_U::__('Problems adding items to archive.'), $message, false);
            DUP_PRO_LOG::traceObject($message . " EXCEPTION:", $ex);
            $buildProgress->failed = true;
            return true;
        }


        //-- Final Wrapup of the Archive
        if ((!$skipArchiveFinalization) && ($createState->working == false)) {

            if ($buildProgress->retries > DUP_PRO_Constants::MAX_BUILD_RETRIES) {
                $error_msg = DUP_PRO_U::__('Package build appears stuck so marking package as failed. Is the Max Worker Time set too high?.');
                DUP_PRO_Log::error(DUP_PRO_U::__('Build Failure'), $error_msg, false);
                $buildProgress->failed = true;
                return true;
            }
  
            $expandStateEntity = DUP_PRO_DupArchive_Expand_State_Entity::get_by_package_id($archive->Package->ID);

            if ($expandStateEntity == null) {

                $expandStateEntity = new DUP_PRO_DupArchive_Expand_State_Entity();

                $expandStateEntity->package_id = $archive->Package->ID;

                $expandStateEntity->archivePath = $archivePath;
                $expandStateEntity->working = true;
                $expandStateEntity->timeSliceInSecs = $global->php_max_worker_time_in_sec;
                $expandStateEntity->basePath = DUPLICATOR_PRO_SSDIR_PATH_TMP . '/validate';
                $expandStateEntity->throttleDelayInUs = DUP_PRO_Server_Load_Reduction::microseconds_from_reduction($global->server_load_reduction);
                $expandStateEntity->validateOnly = true;
                $expandStateEntity->validationType = DupArchiveValidationTypes::Standard;
                $expandStateEntity->working = true;
                $expandStateEntity->expectedDirectoryCount = count($scanReport->ARC->Dirs) - 1; // Since we never create the root
                $expandStateEntity->expectedFileCount = count($scanReport->ARC->Files) + 1;    // database.sql will be in there
            }

            $expandState = new DUP_PRO_DupArchive_Expand_State($expandStateEntity);

            try {
                DUP_PRO_LOG::profileEvent('Validate archive', true);
                DupArchiveEngine::expandArchive($expandState);
                DUP_PRO_LOG::profileEvent('Validate archive', false);
            } catch (Exception $ex) {
                DUP_PRO_LOG::traceError('Exception:' . $ex->getMessage() . ':' . $ex->getTraceAsString());
                $buildProgress->failed = true;
                return true;
            }


            if (count($expandState->failures) > 0) {
                // Fail immediately if anything found - even if havent completed processing the entire archive.

                DUP_PRO_Log::error(DUP_PRO_U::__('Build Failure'), $expandState->getFailureMessage(), false);

                $buildProgress->failed = true;
                return true;

            } else if (!$expandState->working) {
                $buildProgress->archive_built = true;
                $buildProgress->retries = 0;

                $archive->Package->update();

                $timerAllEnd = DUP_PRO_U::getMicrotime();
                $timerAllSum = DUP_PRO_U::elapsedTime($timerAllEnd, $archive->Package->timer_start);

                $archiveFileSize = @filesize($archivePath);
                DUP_PRO_Log::info("COMPRESSED SIZE: " . DUP_PRO_U::byteSize($archiveFileSize));
                DUP_PRO_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");
                DUP_PRO_Log::info("MEMORY STACK: " . DUP_PRO_Server::getPHPMemory());

                $archive->file_count = $expandState->fileWriteCount + $expandState->directoryWriteCount;

                $archive->Package->update();

                $done = true;
            } else {
                $expandState->save();
            }
        }

        $buildProgress->retries = 0;;
                   
        return $done;
    }
}
