<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author Bob
 */

//class ProfileCallInfo
//{
//    public $numCalls = 0;
//    public $prevTS = -1;
//    public $culmulativeTime = 0;
//}

class DupArchiveUtil
{
    public static $TRACE_ON = true;
    public static $loggingFunction = null;
    public static $profilingFunction = null;

    public static function boolToString($b)
    {
        return ($b ? 'true' : 'false');
    }

    public static function expandFiles($base_dir, $recurse)
    {
        $files = array();

        foreach (scandir($base_dir) as $file) {
            if (($file == '.') || ($file == '..')) {
                continue;
            }

            $file = $base_dir.DIRECTORY_SEPARATOR.$file;

            if (is_file($file)) {
                $files [] = $file;
            } else if (is_dir($file) && $recurse) {
                $files = array_merge($files, self::expandFiles($file, $recurse));
            }
        }

        return $files;
    }

    public static function expandDirectories($base_dir, $recurse)
    {
        $directories = array();

        foreach (scandir($base_dir) as $candidate) {

            if (($candidate == '.') || ($candidate == '..')) {
                continue;
            }

            $candidate = $base_dir.DIRECTORY_SEPARATOR.$candidate;

            // if (is_file($file)) {
            //     $directories [] = $file;
            if (is_dir($candidate)) {

                $directories[] = $candidate;

                if ($recurse) {

                    $directories = array_merge($directories, self::expandDirectories($candidate, $recurse));
                }
            }
        }

        return $directories;
    }

    public static function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/').'/' : $from;
        $to   = is_dir($to) ? rtrim($to, '\/').'/' : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    //$relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }

    public static function fopen($filepath, $mode)
    {
        if (($mode === 'w') || ($mode === 'w+') || ($mode === 'c+') || file_exists($filepath)) {
            DupArchiveUtil::tlog("Attempting to open with $filepath , $mode");
            $file_handle = @fopen($filepath, $mode);
        } else {
            throw new Exception("$filepath doesn't exist");
        }

        if ($file_handle === false) {
            throw new Exception("Error opening $filepath");
        } else {
            return $file_handle;
        }
    }

    public static function fwrite($handle, $string)
    {
        $bytes_written = fwrite($handle, $string);

        if ($bytes_written === false) {
            throw new Exception('Error writing to file.');
        } else {
            return $bytes_written;
        }
    }

    public static function fclose($handle, $exception_on_fail = true)
    {
        if ((@fclose($handle) === false) && $exception_on_fail) {
            throw new Exception("Error closing file");
        }
    }

    public static function flock($handle, $operation)
    {
        if (@flock($handle, $operation) === false) {
            throw new Exception("Error locking file");
        }
    }

    public static function ftell($file_handle)
    {
        $position = @ftell($file_handle);

        if ($position === false) {
            throw new Exception("Couldn't retrieve file offset for $filepath");
        } else {
            return $position;
        }
    }

    public static function log($s, $flush = false)
    {
      //  echo 'logging functinon' . self::$loggingFunction;
       // self::$loggingFunction($s, $flush);
        if(self::$loggingFunction != null)
        {
            call_user_func(self::$loggingFunction, $s, $flush);
        }
        else
        {
            throw new Exception('Logging function not initialized');
        }
    }

    public static function tlog($s, $flush = false)
    {
        if (self::$TRACE_ON) {
       //     self::log("####{$s}", $flush);
        }
    }

    public static function profileEvent($s, $start)
    {
        if(self::$profilingFunction != null)
        {
            call_user_func(self::$profilingFunction, $s, $start);
        }
    }

//     private static $profileLogArray = null;
//    private static $prevTS = -1;
//
//    public static function initProfiling()
//    {
//        self::$profileLogArray = array();
//    }
//
//    public static function writeToPLog($s)
//    {
//        $currentTime = microtime(true);
//
//        if(array_key_exists($s, self::$profileLogArray))
//        {
//            /* @var $profileCallInfo ProfileCallInfo */
//            $profileCallInfo = &self::$profileLogArray[$s];
//
//            $dSame = $currentTime - $profileCallInfo->prevTS;
//
//            $profileCallInfo->prevTS = $currentTime;
//            $profileCallInfo->numCalls++;
//
//            $dSame = number_format($dSame, 7);
//        }
//        else
//        {
//            $dSame = 'N/A';
//
//            $profileCallInfo = new ProfileCallInfo();
//            $profileCallInfo->prevTS = $currentTime;
//            $profileCallInfo->numCalls++;
//
//            self::$profileLogArray[$s] = $profileCallInfo;
//        }
//
//        if(self::$prevTS != -1)
//        {
//            $dPrev = $currentTime - self::$prevTS;
//
//            $profileCallInfo->culmulativeTime += $dPrev;
//
//
//            $dPrev = number_format($dPrev, 7);
//        }
//        else
//        {
//            $dPrev = 'N/A';
//        }
//
//        self::$prevTS = $currentTime;
//
//        self::log("  {$dPrev}  :  {$dSame}  : {$profileCallInfo->numCalls}  : {$profileCallInfo->culmulativeTime}  :  {$s}");
//    }

    static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object)) DupArchiveUtil::rrmdir($dir."/".$object);
                    else unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

    public static function tlogObject($s, $o, $flush = false)
    {
        if(is_object($o))
        {
            $o = get_object_vars($o);
        }

        $ostring = print_r($o, true);

        self::tlog($s, $flush);
        self::tlog($ostring, $flush);
    }

    public static function logObject($s, $o, $flush = false)
    {
        $ostring = print_r($o, true);

        self::log($s, $flush);
        self::log($ostring, $flush);
    }

    public static function filesize($filename)
    {
        $file_size = @filesize($filename);

        if ($file_size === false) {
            throw new Exception("Error retrieving file size of $filename");
        }

        return $file_size;
    }

    public static function fseek($handle, $offset, $whence = SEEK_SET)
    {
        $ret_val = @fseek($handle, $offset, $whence);

        if ($ret_val !== 0) {
            if ($ret_val === false) {
                throw new Exception("Trying to fseek($offset, $whence) and came back false");
            } else {
                throw new Exception("Error seeking to file offset $offset. Retval = $ret_val");
            }
        }
    }

    public static function filemtime($filename)
    {
        $mtime = filemtime($filename);

        if ($mtime === E_WARNING) {
            throw new Exception("Cannot retrieve last modified time of $filename");
        }

        return $mtime;
    }

    public static function streamGetLine($handle, $length, $ending)
    {
        $line = stream_get_line($handle, $length, $ending);

        if ($line === false) {
            throw new Exception('Error reading line.');
        }

        return $line;
    }

    public static function fgets($handle, $length)
    {
        $line = fgets($handle, $length);

        if ($line === false) {
            throw new Exception('Error reading line.');
        }

        return $line;
    }

    public static function mkdir($pathname, $mode, $recursive)
    {
        if (@mkdir($pathname, $mode, $recursive) === false) {
            throw new Exception("Error creating directory {$pathname}");
        }
    }

    public static function postWithoutWait($url, $params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            {
                $post_params[] = $key.'='.urlencode($val);
            }
        }

        $post_string = implode('&', $post_params);

        $parts = parse_url($url);
        
        $fp    = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 60);
        
        $out   = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";

        if (isset($post_string)) {
            $out.= $post_string;
        }
        
        fwrite($fp, $out);
        
        fclose($fp);
    }
}
