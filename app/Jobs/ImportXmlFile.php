<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Importer\Factory as Importer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportXmlFile extends Job implements ShouldQueue
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
	    $xml_importer = null;

	    \Log::info('Start import of orders from XML API. Trying '.$this->attempts());
	    if($this->attempts() > 3){
		    \Log::error('Overload max attempts of trying to run job ImportXmlFile.');
	    	$this->release(60);
			return false;
	    }

	    // Run xml importer
	    try{

		    $xml_importer = Importer::create('xml', env('XML_API_SOURCE'));
		    if($xml_importer->validate())
			    $xml_importer->import();

	    }catch(\Exception $e){
		    \Log::error($e->getMessage());
		    $this->failed();
		    return false;
	    }

	    \Log::info('Stop import of orders from XML API.');

	    return true;
    }
}
