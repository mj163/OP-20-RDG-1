<?php
if (!defined('DUPLICATOR_PRO_VERSION')) exit; // Exit if accessed directly

/**
 * Used to create package and application trace logs
 *
 * Pakcage logs: Consist of a seperate log file for each package created
 * Trace logs:   Created only when tracing is enabled see Settings > General
 *               One trace log is created and when it hits a threashold a
 *               second one is made
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.0.0
 *
 */
class DUP_PRO_Profile_Call_Info
{
    public $latestStartTS = -1;
    public $latestStopTS = -1;

    public $numCalls = 0;
    public $culmulativeTime = 0;
    
    public $eventName = '';

    public function __construct($eventName)
    {
        $this->eventName = $eventName;
    }
}

class DUP_PRO_Log
{
    /**
     * The file handle used to write to the package log file
     */
    private static $logFileHandle;

    /**
     * Get the setting which indicates if tracing is enabled
     */
    private static $traceEnabled;

    public static $profileLogs = null;
    //private static $prevTS = -1;


    /**
     * Init this static object
     */
    public static function init()
    {
        self::$traceEnabled = (bool) get_option('duplicator_pro_trace_log_enabled', false);
    }

    public static function setProfileLogs($profileLogs)
    {
        if($profileLogs == null)
        {
            self::$profileLogs = new stdClass();
        }
        else
        {
            self::$profileLogs = $profileLogs;
        }

    }

    /**
     * Open a log file connection for writing to the package log file
     *
     * @param string $nameHas The Name of the log file to create
     *
     * @return nul
     */
    public static function open($nameHash)
    {
        if (!isset($nameHash)) throw new Exception("A name value is required to open a file log.");
        self::$logFileHandle = @fopen(DUPLICATOR_PRO_SSDIR_PATH."/{$nameHash}.log", "a+");
    }

    /**
     * Close the package log file connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function close()
    {
        return @fclose(self::$logFileHandle);
    }

    /**
     *  General information send to the package log
     *
     *  @param string $msg	The message to log
     * 
     *  @return null
     */
    public static function info($msg)
    {
        @fwrite(self::$logFileHandle, "{$msg} \n");
    }

    /**
     *  Called for the package log when an error is detected and no further processing should occur
     *
     * @param string $msg       The message to log
     * @param string $details   Additional details to help resolve the issue if possible
     * @param bool   $die       Issue a die command when finished logging
     *
     * @return null
     */
    public static function error($msg, $detail = '', $die = true)
    {
        if ($detail == '') {
            $detail = '(no detail)';
        }

        DUP_PRO_LOG::traceError("Forced Error Generated: ".$msg."-$detail");
        $source = self::getStack(debug_backtrace());

        $err_msg = "\n====================================================================\n";
        $err_msg .= "!RUNTIME ERROR!\n";
        $err_msg .= "---------------------------------------------------------------------\n";
        $err_msg .= "MESSAGE:\n{$msg}\n";
        if (strlen($detail)) {
            $err_msg .= "DETAILS:\n{$detail}\n";
        }
        $err_msg .= "---------------------------------------------------------------------\n";
        $err_msg .= "TRACE:\n{$source}";
        $err_msg .= "====================================================================\n\n";
        @fwrite(self::$logFileHandle, "\n{$err_msg}");

        if ($die) {
            //Output to browser
            $browser_msg = "RUNTIME ERROR:<br/>An error has occured. Please try again!<br/>";
            $browser_msg .= "See the duplicator log file for full details: Duplicator Pro &gt; Tools &gt; Logging<br/><br/>";
            $browser_msg .= "MESSAGE:<br/> {$msg} <br/><br/>";
            if (strlen($detail)) {
                $browser_msg .= "DETAILS: {$detail} <br/>";
            }
            die($browser_msg);
        }
    }

    /**
     * The current strack trace of a PHP call
     *
     * @param $stacktrace   The current debug stack
     *
     * @return string       A log friend stacktrace view of info
     */
    public static function getStack($stacktrace)
    {
        $output = "";
        $i      = 1;

        foreach ($stacktrace as $node) {
            $file_output     = isset($node['file']) ? basename($node['file']) : '';
            $function_output = isset($node['function']) ? basename($node['function']) : '';
            $line_output     = isset($node['line']) ? basename($node['line']) : '';

            $output .= "$i. ".$file_output." : ".$function_output." (".$line_output.")\n";
            $i++;
        }

        return $output;
    }



   /** ========================================================
	* TRACE SPECIFIC CALLS
    * =====================================================  */

    /**
     * Writes a message to the trace log
     *
     * @param $message   The message to write
     *
     * @return null
     */
    public static function ddebug($message)
    {
        self::trace($message, true);
    }

    /**
     * Deletes the trace log and backup trace log files
     *
     * @return null
     */
    public static function deleteTraceLog()
    {
        $file_path   = self::getTraceFilepath();
        $backup_path = self::getBackupTraceFilepath();

        self::trace("deleting $file_path");
        @unlink($file_path);
        self::trace("deleting $backup_path");
        @unlink($backup_path);
    }

    /**
     * Gets the backup trace file path
     *
     * @return string   Returns the full path to the backup trace file (i.e. dup-pro_hash.log1)
     */
    public static function getBackupTraceFilepath()
    {
        $default_key = DUP_PRO_Crypt_Blowfish::getDefaultKey();
        $backup_log_filename = "dup_pro_$default_key.log1";
        $backup_path = DUPLICATOR_PRO_SSDIR_PATH."/".$backup_log_filename;

        return $backup_path;
    }

    /**
     * Gets the active trace file path
     *
     * @return string   Returns the full path to the active trace file (i.e. dup-pro_hash.log)
     */
    public static function getTraceFilepath()
    {
        $default_key  = DUP_PRO_Crypt_Blowfish::getDefaultKey();
        $log_filename = "dup_pro_$default_key.log";
        $file_path    = DUPLICATOR_PRO_SSDIR_PATH."/".$log_filename;

        return $file_path;
    }

    /**
     * Gets the current file size of the active trace file
     *
     * @return string   Returns a human readable file size of the active trace file
     */
    public static function getTraceStatus()
    {
        $file_path   = DUP_PRO_LOG::getTraceFilepath();
        $backup_path = DUP_PRO_LOG::getBackupTraceFilepath();

        if (file_exists($file_path)) {
            $filesize = filesize($file_path);

            if (file_exists($backup_path)) {
                $filesize += filesize($backup_path);
            }

            $message = sprintf(DUP_PRO_U::__('%1$s'), DUP_PRO_U::byteSize($filesize));
        } else {
            $message = DUP_PRO_U::__('No Log');
        }

        return $message;
    }

    /**
     * Gets the active trace file URL path
     *
     * @return string   Returns the URL to the active trace file
     */
    public static function getTraceURL()
    {
        $default_key  = DUP_PRO_Crypt_Blowfish::getDefaultKey();
        $log_filename = "dup_pro_$default_key.log";
        $url          = DUPLICATOR_PRO_SSDIR_URL."/".$log_filename;

        return $url;
    }

    /**
     * Adds a message to the active trace log
     *
     * @param string $message The message to add to the active trace
     * @param bool $audit Add the trace message to the PHP error log
     *                    additional contraints are required
     *
     * @return null
     */
    public static function trace($message, $audit = true, $calling_function_override = null)
    {
        if (self::$traceEnabled) {
            $send_trace_to_error_log = (bool) get_option('duplicator_pro_send_trace_to_error_log', false);
            $unique_id               = sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'].$_SERVER['REQUEST_TIME'].$_SERVER['REMOTE_PORT'])));

            if($calling_function_override == null)
            {
                $calling_function       = DUP_PRO_U::getCallingFunctionName() . '()';
            }
            else
            {
                $calling_function       = $calling_function_override . '()';
            }
            
            if(is_object($message)) {

                $ov = get_object_vars($message);

                $message = print_r($ov, true);
            } else if (is_array($message)) {

                $message = print_r($message, true);
            }

            $logging_message           = 'DUP_PRO | '.$unique_id." | $calling_function | ".$message;
            $ticks                     = time() + ((int) get_option('gmt_offset') * 3600);
            $formatted_time            = date('d M H:i:s', $ticks);
            $formatted_logging_message = "[$formatted_time] $logging_message \r\n";

            // Write to error log if warranted - if either it's a non audit(error) or tracing has been piped to the error log
            if (($audit == false) || ($send_trace_to_error_log) && WP_DEBUG && WP_DEBUG_LOG) {
                DUP_PRO_Low_U::errLog($logging_message);
            }

            // Everything goes to the plugin log, whether it's part of package generation or not.
            self::writeToTrace($formatted_logging_message);
        }
    }

    /**
     * Adds a message to the active trace log with ***ERROR*** prepended
     *
     * @param string $message The error message to add to the active trace
     *
     * @return null
     */
    public static function traceError($message)
    {
        self::trace("***ERROR***: $message", false);
    }

    /**
     * Adds a message followed by an object dump to the message trace
     *
     * @param string $message The message to add to the active trace
     * @param object $object  A valid object types such as a class or array
     *
     * @return null
     */
    public static function traceObject($message, $object)
    {
        self::trace($message.'<br\>', true, DUP_PRO_U::getCallingFunctionName());
        self::trace($object, true, DUP_PRO_U::getCallingFunctionName());
    }

    public static function profileEvent($eventName, $start)
    {
        if(self::$profileLogs !== null)
        {
            $currentTime = microtime(true);

            //if(array_key_exists($eventName, self::$profileLogs))
            if(property_exists(self::$profileLogs, $eventName))
            {
                /* @var $profileCallInfo DUP_PRO_Profile_Call_Info */
                $profileCallInfo = &self::$profileLogs->$eventName;

                if($start)
                {
                    if(($profileCallInfo->latestStartTS != -1) && ($profileCallInfo->latestStopTS == -1))
                    {
                        DUP_PRO_LOG::trace("p5");
                        throw new Exception("Overwriting a start for {$eventName} when stop hasn’t occurred yet");
                    }

                    $profileCallInfo->latestStartTS = $currentTime;
                    $profileCallInfo->latestStopTS = -1;

             //       self::trace("PROFILE: {$eventName} START.");
                }
                else {
                    $profileCallInfo->latestStopTS = $currentTime;

                    if($profileCallInfo->latestStartTS == -1)
                    {
                        throw new Exception("Attempting to stop event $eventName when start didn't occur yet");
                    }

                    $deltaTime = ($profileCallInfo->latestStopTS - $profileCallInfo->latestStartTS);
                    $profileCallInfo->numCalls++;
                    $profileCallInfo->culmulativeTime += $deltaTime;

                    $deltaTime = number_format($deltaTime, 7);
                    $culmulativeTime = number_format($profileCallInfo->culmulativeTime, 7);
                    $averageTime = number_format($profileCallInfo->culmulativeTime / $profileCallInfo->numCalls, 7);

                 //   self::trace("PROFILE: {$eventName} STOP.  Delta={$deltaTime} | Culm={$culmulativeTime} | Avg={$averageTime} | NumCalls={$profileCallInfo->numCalls}");
                }

            }
            else
            {
                if(!$start)
                {
                    throw new Exception("Trying to stop an event that never started");
                }

                $profileCallInfo = new DUP_PRO_Profile_Call_Info($eventName);
                $profileCallInfo->latestStartTS = $currentTime;
                $profileCallInfo->latestStopTS = -1;

                self::$profileLogs->$eventName = $profileCallInfo;

            //    self::trace("PROFILE: {$eventName} START.");
            }
           

            //self::$prevTS = $currentTime;

            
        }else
        {
         //   self::trace('profile log array IS null');
        }
    }

    public static function logProfileReport()
    {
        self::trace('====PROFILE REPORT====');
        self::trace('');
        $header = sprintf('%-48s | %-7s | %-6s | %9s', 'EVENT', '# CALLS', 'AVG(T)', 'TOTAL T');
        self::trace($header);

        $profileLogArray = get_object_vars(self::$profileLogs);

        usort($profileLogArray, create_function('$a,$b', 'return -strcmp($a->culmulativeTime, $b->culmulativeTime);'));

        foreach($profileLogArray as $profileLog)
        {
            /* @var $profileLog DUP_PRO_Profile_Call_Info */

            if($profileLog->numCalls != 0)
            {
                $avgTime = $profileLog->culmulativeTime / $profileLog->numCalls;
            }
            else {
                $avgTime = -1;
            }

            $entry = sprintf('%-48s | %-7d | %-6.3f | %9.3f', $profileLog->eventName, $profileLog->numCalls, $avgTime, $profileLog->culmulativeTime);
            self::trace($entry);
        }
    }

    /**
     * Does the trace file exisit
     *
     * @return bool Returns true if an active trace file exists
     */
    public static function traceFileExists()
    {
        $file_path = DUP_PRO_LOG::getTraceFilepath();

        return file_exists($file_path);
    }

    /**
     * Manages writing the active or backup log based on the size setting
     *
     * @return null
     */
    private static function writeToTrace($formatted_logging_message)
    {
        $log_filepath = DUP_PRO_LOG::getTraceFilepath();

        if (@filesize($log_filepath) > DUP_PRO_Constants::MAX_LOG_SIZE) {
            $backup_log_filepath = DUP_PRO_LOG::getBackupTraceFilepath();

            if (file_exists($backup_log_filepath)) {
                if (@unlink($backup_log_filepath) === false) {
                    DUP_PRO_Low_U::errLog("Couldn't delete backup log $backup_log_filepath");
                }
            }

            if (@rename($log_filepath, $backup_log_filepath) === false) {
                DUP_PRO_Low_U::errLog("Couldn't rename log $log_filepath to $backup_log_filepath");
            }
        }

        if (@file_put_contents($log_filepath, $formatted_logging_message, FILE_APPEND) === false) {
            // Not en error worth reporting
        }
    }
}

DUP_PRO_LOG::init();