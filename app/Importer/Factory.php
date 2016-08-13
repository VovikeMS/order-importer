<?php

namespace App\Importer;

use App\Importer\Services\Csv;
use App\Importer\Services\Xml;

abstract class Factory
{
	/**
	 * Registered importers type
	 *
	 * @var array
	 */
	private static $importerTypes = ['csv', 'xml'];

	/**
	 * Generate importer instance according type of importer
	 *
	 * @param string $type
	 * @param string $resource
	 * @return Csv|Xml|false
	 * @throws \Exception
	 */
	public static function create($type, $resource)
	{

		if(!in_array($type, self::$importerTypes))
			throw new \Exception("Uncaught type of importer [$type]. Importer abort");

		switch($type){
			case 'csv': return new Csv($resource);
			case 'xml': return new Xml($resource);
		}

		return false;
	}
}