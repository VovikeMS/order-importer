<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Importer\Factory as Importer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Exception;

class ImportCsvFile extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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
     * @return bool
     */
    public function handle()
    {
	    $csv_importer = null;

	    Log::info('Start import of orders from CSV API. Trying '.$this->attempts());
	    if($this->attempts() > 3){
		    Log::error('Overload max attempts of trying to run job ImportCsvFile.');
		    $this->release(60);
		    return false;
	    }

	    // run csv importer
	    try{

		    $csv_importer = Importer::create('csv', env('CSV_API_SOURCE'));
		    if($csv_importer->validate())
			    $csv_importer->import();

	    }catch(Exception $e){
		    Log::error($e->getMessage());
		    $this->failed();
		    return false;
	    }

	    Log::info('Stop import of orders from CSV API');

	    return true;
    }
}
