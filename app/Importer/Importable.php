<?php

namespace App\Importer;


interface Importable
{
	/**
	 * Importer constructor.
	 * Get source of imported data
	 *
	 * @param string $resource
	 */
	public function __construct($resource);

	/**
	 * Check resource access and resource format
	 * Check import data
	 *
	 * @return boolean
	 */
	public function validate();

	/**
	 * Start import process
	 *
	 * @return boolean
	 */
	public function import();
}