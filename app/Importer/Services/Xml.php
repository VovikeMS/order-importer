<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Importer;

class Xml extends Importer implements Importable
{
	protected $name = 'Xml';
	protected $type = 'xml';
	protected $mime_type = 'application/xml';

	/**
	 * XmlImporter constructor.
	 *
	 * @param string $resource
	 */
	public function __construct($resource)
	{
		parent::__construct($resource);

		dd($this->source_data);
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