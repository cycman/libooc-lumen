<?php

namespace App\Jobs;

class ExampleJob extends Job
{
    public $queue = 'test';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        var_dump('i am a job');
    }

}
