<?php

namespace App\Importer;

abstract class Importer
{
	use Logger;

	/**
	 * API Path
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * API Response
	 *
	 * @var mixed
	 */
	protected $source_data;

	/**
	 * MIME Type of API return
	 *
	 * @var string
	 */
	protected $mime_type = 'application/xml';

	/**
	 * Importer constructor.
	 *
	 * @param string $resource
	 * @throws \Exception
	 */
	public function __construct($resource)
	{
		$this->initLogger();

		// check resource is valid
		if(empty($resource)){
			$this->logger->error('Bad import resource', [$resource]);
			throw new \Exception("[$this->name importer] Bad import resource [$resource]");
		}

		// resource valid
		$this->resource = $resource;

		$this->loadSource();
	}

	/**
	 * Load source from API
	 *
	 * @return void
	 */
	protected function loadSource()
	{
		if(function_exists('curl_init')){

			// trying to load by cURL
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_HEADER                  => 1,
				CURLOPT_RETURNTRANSFER          => 1,
				CURLOPT_VERBOSE                 => 1,
				CURLOPT_HTTPGET                 => 1,
				CURLOPT_URL                     => $this->resource,
				CURLOPT_HTTPHEADER              => ['Content-Type: '.$this->mime_type],
			]);

			$this->source_data = curl_exec($curl);

			if($this->source_data === false){
				$error = curl_error($curl);
				curl_close($curl);

				$this->logger->error('Cannot load API response by path', [$this->resource, $error]);
				throw new \Exception("[$this->name importer] Cannot load API response from path [$this->resource]. Error: ".$error);
			}

			// get body of response
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$this->source_data = substr($this->source_data, $header_size);
			curl_close($curl);
		}else{

			// trying to load by php native method
			$this->logger->warning('CURL PHP extension not found. Please install cURL. Trying to use native library.');

			$this->source_data = file_get_contents($this->resource);
			if($this->source_data === false){
				$this->logger->error('Cannot load API response by path', [$this->resource]);
				throw new \Exception("[$this->name importer] Cannot load API response from path [$this->resource]");
			}
		}
		$this->logger->info('Source data from API loaded.');
	}
}