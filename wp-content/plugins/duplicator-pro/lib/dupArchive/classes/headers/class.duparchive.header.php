<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');

//require_once(dirname(__FILE__).'/class.HeaderBase.php');
// Format: #A#{version:5}#{isCompressed}!
class DupArchiveHeader// extends HeaderBase
{
    public $version;
    public $isCompressed;

    //   public $directoryCount;
    // public $fileCount;

    const LatestVersion = 1;
    const MaxHeaderSize = 50;

    private function __construct()
    {
        // Prevent instantiation
    }

  //  public static function create($isCompressed, $directoryCount, $fileCount, $version = self::LatestVersion)
    public static function create($isCompressed, $version = self::LatestVersion)
    {
        $instance = new DupArchiveHeader();

   //     $instance->directoryCount = $directoryCount;
        //  $instance->fileCount      = $fileCount;
        $instance->version        = $version;
        $instance->isCompressed   = $isCompressed;

        return $instance;
    }

    public static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveHeader();

        // $temp = Util::fgets($archiveHandle, 100);

        $headerString = DupArchiveUtil::streamGetLine($archiveHandle, self::MaxHeaderSize, '!');

        //      echo "first header string $headerString <br/>";
        $marker = substr($headerString, 0, 3);

//        echo "subsr header string $marker <br/>";
        if ($marker != '?A#') {
            throw new Exception("Invalid archive header marker found {$marker}");
        }

        $headerString = substr($headerString, 3);

        // list($instance->version, $isCompressedString, $instance->directoryCount, $instance->fileCount) = explode('#', $headerString);

        list($instance->version, $isCompressedString) = explode('#', $headerString);


        //   $instance->directoryCount = (int)$instance->directoryCount;
        $instance->version      = (int) $instance->version;
        //  $instance->fileCount = (int)$instance->fileCount;
        $instance->isCompressed = (($isCompressedString == 'true') ? true : false);

        //     print_r($instance);
        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        $isCompressedString = DupArchiveUtil::boolToString($this->isCompressed);

        $paddedVersion = sprintf("%04d", $this->version);
        //$paddedFileCount = sprintf("%09d", $this->fileCount);
        //$paddedDirectoryCount = sprintf("%09d", $this->directoryCount);
        //    DupArchiveUtil::fwrite($archiveHandle, "?A#{$paddedVersion}#{$isCompressedString}#{$paddedDirectoryCount}#{$paddedFileCount}#A!");

        DupArchiveUtil::fwrite($archiveHandle, "?A#{$paddedVersion}#{$isCompressedString}#A!");
    }
}