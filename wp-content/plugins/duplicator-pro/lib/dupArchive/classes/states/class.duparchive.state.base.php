<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../class.duparchive.processing.failure.php');

abstract class DupArchiveStateBase
{
    public $basePath          = '';
    public $archivePath       = '';
    public $isCompressed      = false;
    public $currentFileOffset = -1;
    public $archiveOffset     = -1;
    public $timeSliceInSecs   = -1;
    public $working           = false;
    public $failures          = null;
    public $startTimestamp    = -1;
    public $throttleDelayInUs  = 0;
    public $timeoutTimestamp  = -1;
    public $timerEnabled      = true;

    public function __construct()
    {
        $this->failures = array();
    }

    public function getFailureMessage()
    {
        if(count($this->failures) > 0)
        {
            $c = 0;
            $message = "FAILURES\n";
            foreach($this->failures as $failure)
            {

                $message .= "{$failure->subject} : {$failure->description}\n";
                $c++;
                if($c > 5)
                {
                    break;
                }
            }

            return $message;
        }
        else
        {
            return 'No errors.';
        }
    }

    public function addFailure($type, $subject, $description)
    {
        DupArchiveUtil::tlog("addfailure");
        $failure = new DupArchiveProcessingFailure();

        $failure->type        = $type;
        $failure->subject     = $subject;
        $failure->description = $description;

        $this->failures[] = $failure;
        DupArchiveUtil::tlog("add failure done");
    }

    public function startTimer()
    {
        if ($this->timerEnabled) {
            $this->timeoutTimestamp = time() + $this->timeSliceInSecs;
        }
    }

    public function timedOut()
    {
        if ($this->timerEnabled) {
            if ($this->timeoutTimestamp != -1) {
                return time() >= $this->timeoutTimestamp;
            }
        } else {
            return false;
        }
    }
    //   abstract public function save();
}