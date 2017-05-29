<?php
require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');

// Format
// #F#{$file_size}#{$mtime}#{$file_perms}#{$md5}#{$relative_filepath_length}#{$relative_filepath}!
class DupArchiveDirectoryHeader// extends HeaderBase
{
    public $mtime;
    public $permissions;
    public $relativePathLength;
    public $relativePath;

    const MaxHeaderSize                = 8192;
    const MaxPathLength                = 4100;
    const MaxStandardHeaderFieldLength = 128;

    private function __construct()
    {
        // Prevent direct instantiation
    }

    static function createFromDirectory($directoryPath, $relativePath)
    {
        DupArchiveUtil::tlog("createfromDirectory");
        $instance = new DupArchiveDirectoryHeader();

        $instance->permissions        = substr(sprintf('%o', fileperms($directoryPath)), -4);
        $instance->mtime              = DupArchiveUtil::filemtime($directoryPath);
        $instance->relativePath       = $relativePath;
        $instance->relativePathLength = strlen($instance->relativePath);

        return $instance;
    }

    static function readFromArchive($archiveHandle, $skipMarker = false)
    {
        $instance = new DupArchiveDirectoryHeader();

        if(!$skipMarker)
        {
            $marker = fgets($archiveHandle, 4);

            if ($marker === false) {
                if (feof($archiveHandle)) {
                    return false;
                } else {
                    throw new Exception('Error reading directory header');
                }
            }

            if ($marker != '?D#') {
                throw new Exception("Invalid directory header marker found [{$marker}] : location ".ftell($archiveHandle));
            }
        }

        // ?D#{mtime}#{permissions}#{$this->relativePathLength}#{$relativePath}#D!";

        $instance->mtime              = self::readStandardHeaderField($archiveHandle);
        $instance->permissions        = self::readStandardHeaderField($archiveHandle);
        $instance->relativePathLength = self::readStandardHeaderField($archiveHandle);
        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip the #D!
        fread($archiveHandle, 3);

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        if($this->relativePathLength == 0)
        {
            // Don't allow a base path to be written to the archive
            return;
        }
        
        $headerString = "?D#{$this->mtime}#{$this->permissions}#{$this->relativePathLength}#{$this->relativePath}#D!";

        DupArchiveUtil::fwrite($archiveHandle, $headerString);
    }

    private static function readStandardHeaderField($archiveHandle)
    {
        return DupArchiveUtil::streamGetLine($archiveHandle, self::MaxStandardHeaderFieldLength, '#');
    }
}