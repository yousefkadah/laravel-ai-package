<?php

namespace App\Services;

class LogService
{
    /**
     * Log events to the system
     *
     * @param array $events
     * @return void
     */
    public function logEvents(array $events): void
    {
        foreach ($events as $event) {
            $date = app('carbon')->parse($event['date'])->toString();
            
            app('logger')->log('Event ' . $date . ' :: ' . $event['type']);
        }
    }
}
