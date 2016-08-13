<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Importer;

class Csv extends Importer implements Importable
{
	protected $name = 'Csv';
	protected $type = 'csv';
	protected $mime_type = 'text/csv';

	/**
	 * @var array
	 */
	private $import_data;
	private $delimiter = "\t";
	private $end_of_line = "\n\r";

	/**
	 * @var array
	 */
	private $data_format_fields = [
		'event_date',
		'posting_date',
		'event_type',
		'amount',
		'program_id',
		'program_name',
		'campaign_id',
		'campaign_name',
		'tool_id',
		'tool_name',
		'custom_id',
		'click_timestamp',
		'ebay_item_id',
		'ebay_leaf_category_id',
		'ebay_quantity_sold',
		'ebay_total_sale_amount',
		'item_site_id',
		'meta_category_id',
		'unique_transaction_id',
		'user_frequency_id',
		'earnings',
		'traffic_type',
		'item_name'
	];

	private $required_fields = [
		'program_id',
		'unique_transaction_id',
	];

	/**
	 * CsvImporter constructor.
	 *
	 * @param string $resource
	 */
	public function __construct($resource)
	{
		parent::__construct($resource);
	}

	/**
	 * Validate input data
	 *
	 * @return boolean
	 */
	public function validate()
	{
		$orders = str_getcsv($this->source_data, $this->end_of_line);

		// free memory
		$this->source_data = null;

		if(!is_array($orders) || count($orders)<=0){
			$this->logger->error('Bad format of response. Cannot parse lines. Check response end of lines character.');
			throw new \Exception("[$this->name importer] Validation error. Bad response format.");
		}

		$count_of_fields = count($this->data_format_fields);
		$this->import_data = [];
		foreach($orders as $index=>$order){
			if($index == 0) continue;

			$fields_value = str_getcsv($order, $this->delimiter);
			if(!is_array($fields_value) || count($fields_value) != $count_of_fields){
				$this->logger->warning("Validation error of line {$index}.", [$order, $fields_value]);
				continue;
			}

			$formatted_order = [];
			foreach($this->data_format_fields as $i=>$key)
				$formatted_order[$key] = $fields_value[$i];

			array_push($this->import_data, $formatted_order);
		}

		// free memory
		unset($orders);

		if(!count($this->import_data)){
			$this->logger->info('API was return no orders.');
			return false;
		}

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