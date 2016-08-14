<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Importer;
use App\Models\Order;

class Xml extends Importer implements Importable
{
	protected $name = 'Xml';
	protected $type = 'xml';
	protected $mime_type = 'application/xml';

	/**
	 * @var \SimpleXMLElement
	 */
	private $import_data;

	/**
	 * @var array
	 */
	private $required_fields = [
		'advcampaign_id',
		'order_id',
		'status',
		'cart',
		'currency',
		'action_date'
	];

	/**
	 * XmlImporter constructor.
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
		libxml_use_internal_errors(true);
		$this->import_data = simplexml_load_string($this->source_data);

		// free memory
		$this->source_data = null;

		if($this->import_data === false){
			$errors = libxml_get_errors();
			libxml_clear_errors();
			$this->logger->error('Bad response format. Validation error.', [$errors]);
			throw new \Exception("[$this->name importer] Validation error. Bad response format.");
		}

		$this->logger->info('API response validation success.');

		if(!$this->import_data->count()){
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
		$this->logger->info('Start import orders into DB.');

		$count_of_success_created = 0;
		$count_of_success_updated = 0;
		$count_of_errors = 0;
		foreach($this->import_data->children() as $index=>$elem){
			if(!$this->validateOrder($elem)){
				$this->logger->warning("Order validation error. Order index [$index]", [$elem]);
				$count_of_errors++;
				continue;
			}

			$now = date('Y-m-d H:i:s');

			//$order = Order::firstOrNew(['order_id'=>(string)$elem->order_id]);
			$order = Order::where('order_id', '=', (string)$elem->order_id)->first();
			if($order == null){
				// new order
				$order->fill([
					'shop_id'       => (string)$elem->advcampaign_id,
					'status'        => (string)$elem->status,
					'cost'          => (string)$elem->cart,
					'currency'      => (string)$elem->currency,
					'created_at'    => (string)$elem->action_date,
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
				$order->fill([
					'shop_id'       => (string)$elem->advcampaign_id,
					'status'        => (string)$elem->status,
					'cost'          => (string)$elem->cart,
					'currency'      => (string)$elem->currency,
					'created_at'    => (string)$elem->action_date
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

		$this->logger->info("Import was done.");
		$this->logger->info("Orders [created, updated, error]", [$count_of_success_created, $count_of_success_updated, $count_of_errors]);

		return true;
	}

	/**
	 * Validate order object for required fields
	 *
	 * @param \SimpleXMLElement $xmlOrderObject
	 * @return bool
	 */
	private function validateOrder($xmlOrderObject)
	{
		foreach($this->required_fields as $field){
			if(!isset($xmlOrderObject->{$field}) && (string)$xmlOrderObject->{$field} != null)
				return false;

			// additional validation
		}

		return true;
	}

}