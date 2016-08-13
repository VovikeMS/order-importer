<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Importer\Factory as Importer;

Route::get('/', function () {
	return view('welcome');
});

Route::get('/import', function () {

	$xml_importer = null;
	$csv_importer = null;

	Log::info('Start import of orders from APIs');

	// create instance for xml importer
	try{
		$xml_importer = Importer::create('xml', env('XML_API_SOURCE'));
	}catch(Exception $e){
		Log::error($e->getMessage());
	}

	// create instance for csv importer
	try{
		$csv_importer = Importer::create('csv', env('CSV_API_SOURCE'));
	}catch(Exception $e){
		Log::error($e->getMessage());
	}

	Log::info('Stop import of orders from APIs');

	//return response('Restricted area', 400);
});
