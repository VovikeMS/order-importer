<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Queue;
use App\Jobs\ImportCsvFile;
use App\Jobs\ImportXmlFile;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	// add queues every 10 minute
	    \Log::info('Scheduler called. '.date('Y-m-d H:i:s'));

	    $schedule->call(function () {

	    	\Log::info('Add import jobs to scheduler. '.date('Y-m-d H:i:s'));

	    	Queue::bulk([
			    new ImportXmlFile(),
			    new ImportCsvFile()
		    ]);

	    })->everyTenMinutes();
    }
}
