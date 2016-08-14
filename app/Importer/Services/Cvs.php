<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Importer;
use App\Models\Order;

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
		'ebay_total_sale_amount',
		'click_timestamp',
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
	 * @return bool
	 */
	public function import()
	{
		$this->logger->info('Start import orders into DB.');
		$count_of_success_created = 0;
		$count_of_success_updated = 0;
		$count_of_errors = 0;

		foreach($this->import_data as $index=>$elem){

			if($elem['event_type'] != 'Winning Bid (Revenue)') continue;

			if(!$this->validateOrder($elem)){
				$this->logger->warning("Order validation error. Order index [$index]", [$elem]);
				$count_of_errors++;
				continue;
			}

			$now = date('Y-m-d H:i:s');

			$order = Order::where('order_id', '=', $elem['unique_transaction_id'])->first();
			if($order == null){
				// new order
				$order = new Order();

				// FIXME. Implement saving not found field from CSV into DB
				$order->fill([
					'order_id'      => $elem['unique_transaction_id'],
					'shop_id'       => $elem['program_id'],
					//'status'        => null,
					'cost'          => $elem['ebay_total_sale_amount'],
					//'currency'      => null,
					'created_at'    => $elem['click_timestamp'],
					'imported_at'   => $now
				]);
				try{
					$order->save();
					$count_of_success_created++;
				}catch(\Exception $e){
					$count_of_errors++;
					$this->logger->error('Database exception: '.$e->getMessage());
					continue;
				}
			}else{
				// update order

				// FIXME. Implement saving not found field from CSV into DB
				$order->fill([
					'shop_id'       => $elem['program_id'],
					//'status'        => null,
					'cost'          => $elem['ebay_total_sale_amount'],
					//'currency'      => null,
					'created_at'    => $elem['click_timestamp']
				]);
				try{
					$order->save();
					$count_of_success_updated++;
				}catch(\Exception $e){
					$count_of_errors++;
					$this->logger->error('Database exception: '.$e->getMessage());
					continue;
				}
			}
		}

		// free memory
		unset($this->import_data);

		$this->logger->info('Import was done.');
		$this->logger->info("Orders [created, updated, error]", [$count_of_success_created, $count_of_success_updated, $count_of_errors]);

		return true;
	}

	/**
	 * Validate required order field
	 *
	 * @param array $order
	 * @return bool
	 */
	private function validateOrder($order)
	{
		foreach($this->required_fields as $field){
			if(!isset($order[$field]) /*|| empty($order[$field])*/)
				return false;

			// additional validation
		}

		return true;
	}

}