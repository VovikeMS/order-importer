<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Logger;

class Csv implements Importable
{
	use Logger;

	private $name = 'Csv';
	private $type = 'csv';

	private $resource;
	private $source_data;

	/**
	 * CsvImporter constructor.
	 *
	 * @param string $resource
	 */
	public function __construct($resource)
	{
		$this->initLogger();

		//
	}

	/**
	 * Validate input data
	 *
	 * @return boolean
	 */
	public function validate()
	{
		//

		return true;
	}

	/**
	 * Start process
	 *
	 * @return boolean
	 */
	public function import()
	{
		//

		return true;
	}

}