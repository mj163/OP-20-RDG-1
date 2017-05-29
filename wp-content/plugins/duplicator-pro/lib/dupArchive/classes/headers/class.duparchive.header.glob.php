<?php
require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');

// Format
// #C#{$originalSize}#{$storedSize}!
class DupArchiveGlobHeader //extends HeaderBase
{
    //	public $marker;
    public $originalSize;
    public $storedSize;
    public $md5;

    const MaxHeaderSize = 255;

    private function __construct()
    {

    }

    public static function createFromFile($originalSize, $storedSize, $md5)
    {
        $instance = new DupArchiveGlobHeader;

        $instance->originalSize = $originalSize;
        $instance->storedSize   = $storedSize;
        $instance->md5 = $md5;

        return $instance;
    }

    public static function readFromArchive($archiveHandle, $skipGlob)
    {
        $instance = new DupArchiveGlobHeader();

        $headerString = DupArchiveUtil::streamGetLine($archiveHandle, self::MaxHeaderSize, '!');

        $marker = substr($headerString, 0, 3);
        
        if ($marker != '?G#') {
            throw new Exception("Invalid glob header marker found {$marker}. {$headerString} location:" . ftell($archiveHandle));
        }

        $headerString = substr($headerString, 3);

        list($instance->originalSize, $instance->storedSize, $instance->md5) = explode('#', $headerString);

        //print_r($instance);
        
        if ($skipGlob) {
            DupArchiveUtil::fseek($archiveHandle, $instance->storedSize, SEEK_CUR);
        }

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        $headerString = "?G#{$this->originalSize}#{$this->storedSize}#{$this->md5}#G!";

        DupArchiveUtil::fwrite($archiveHandle, $headerString);
    }
}