<?php

namespace App\Importer;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as FrameworkLogger;

trait Logger
{
	/**
	 * @var FrameworkLogger
	 */
	protected $logger;

	/**
	 * @var StreamHandler
	 */
	private $handler;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * Create logger instance and set up log file path
	 *
	 * @return void
	 */
	private function initLogger()
	{
		$this->logger = new FrameworkLogger("$this->name import log");
		$this->handler = new StreamHandler(storage_path("logs/{$this->type}_importer.log"));
		$this->logger->pushHandler($this->handler);
	}
}