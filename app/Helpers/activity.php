<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (! function_exists('activity')) {
    function activity($logName = 'default')
    {
        return new class($logName) {
            protected $logName;
            protected $subject;
            protected $causer;
            protected $properties = [];

            public function __construct($logName)
            {
                $this->logName = $logName;
            }

            public function performedOn($subject)
            {
                $this->subject = $subject;
                return $this;
            }

            public function causedBy($causer)
            {
                $this->causer = $causer;
                return $this;
            }

            public function withProperties(array $properties)
            {
                $this->properties = $properties;
                return $this;
            }

            public function log(string $description)
            {
                return ActivityLog::create([
                    'log_name'    => $this->logName,
                    'subject_id'  => $this->subject?->id,
                    'subject_type'=> $this->subject ? get_class($this->subject) : null,
                    'causer_id'   => $this->causer?->id ?? Auth::id(),
                    'causer_type' => $this->causer ? get_class($this->causer) : null,
                    'description' => $description,
                    'properties'  => $this->properties,
                ]);
            }
        };
    }
}
