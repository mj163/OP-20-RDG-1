<?php
require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');

// Format
// #F#{$file_size}#{$mtime}#{$file_perms}#{$md5}#{$relative_filepath_length}#{$relative_filepath}!
class DupArchiveFileHeader// extends HeaderBase
{
    public $fileSize;
    public $mtime;
    public $permissions;
    public $md5;
    public $relativePathLength;
    public $relativePath;

    const MaxHeaderSize                = 8192;
    const MaxPathLength                = 4100;
    const MaxStandardHeaderFieldLength = 128;

    private function __construct()
    {
        // Prevent direct instantiation
    }

    static function createFromFile($filepath, $relativeFilePath)
    {
  //      DupArchiveUtil::tlog("createfromfile");
        $instance = new DupArchiveFileHeader();

        // RSR TODO Populate fields based on file already on system
        $instance->fileSize           = DupArchiveUtil::filesize($filepath);
        $instance->permissions        = substr(sprintf('%o', fileperms($filepath)), -4);
        $instance->mtime              = DupArchiveUtil::filemtime($filepath);
        $instance->md5                = md5_file($filepath);
        $instance->relativePath       = $relativeFilePath;
        $instance->relativePathLength = strlen($instance->relativePath);

      //  DupArchiveUtil::tlog("paths=$filepath, {$instance->relativePath}");
        if ($instance->md5 === false) {
            // RSR TODO: Best thing to do here?
            $instance->md5 = "00000000000000000000000000000000";
        }

      //  DupArchiveUtil::tlog("returning instance");
        return $instance;
    }
    #F#{$file_size}#{$mtime}#{$file_perms}#{$md5}#{$relative_filepath_length}#{$relative_filepath}!

    static function readFromArchive($archiveHandle, $skipContents, $skipMarker = false)
    {
        // RSR TODO Read header from archive handle and populate members
        // TODO: return null if end of archive or throw exception if can read something but its not a file header

        $instance = new DupArchiveFileHeader();

        if (!$skipMarker) {
            $marker = fgets($archiveHandle, 4);

            if ($marker === false) {
                if (feof($archiveHandle)) {
                    return false;
                } else {
                    throw new Exception('Error reading file header');
                }
            }

            if ($marker != '?F#') {
                throw new Exception("Invalid file header marker found [{$marker}] : location ".ftell($archiveHandle));
            }
        }

        //   $headerString = "?F#{$this->fileSize}#{$this->mtime}#{$this->filePermissions}#{$this->md5}#{$this->relativeFilepathLength}#{$this->relativeFilepath}!";

        $instance->fileSize           = self::readStandardHeaderField($archiveHandle);
        $instance->mtime              = self::readStandardHeaderField($archiveHandle);
        $instance->permissions        = self::readStandardHeaderField($archiveHandle);
        $instance->md5                = self::readStandardHeaderField($archiveHandle);
        $instance->relativePathLength = self::readStandardHeaderField($archiveHandle);
        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip the #F!
        fread($archiveHandle, 3);

        if ($skipContents && ($instance->fileSize > 0)) {

            $dataSize = 0;

            $moreGlobs = true;
            while ($moreGlobs) {
                //echo 'read glob<br/>';
                /* @var $globHeader DupArchiveGlobHeader */
                $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, true);

                $dataSize += $globHeader->originalSize;

                $moreGlobs = ($dataSize < $instance->fileSize);
            }
        }

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        $headerString = "?F#{$this->fileSize}#{$this->mtime}#{$this->permissions}#{$this->md5}#{$this->relativePathLength}#{$this->relativePath}#F!";

        DupArchiveUtil::fwrite($archiveHandle, $headerString);
    }

    private static function readStandardHeaderField($archiveHandle)
    {
        return DupArchiveUtil::streamGetLine($archiveHandle, self::MaxStandardHeaderFieldLength, '#');
    }
}