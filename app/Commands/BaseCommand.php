<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use DateTime;
use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;

class BaseCommand
{
	/**
	 * The configuration object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Config
	 */
	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function run(): CommandResponse
	{
		$response = new CommandResponse();
		if (! $this->checkRequirements()) {
			$response->addError('Requirements for this command to run are not met.');
			return $response;
		}
		return $response;
	}

	protected function checkRequirements(): bool
	{
		return true;
	}
}
