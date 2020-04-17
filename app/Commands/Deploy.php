<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;

class Deploy
{
	/**
	 * The configuration object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Config
	 */
	private $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function run(): CommandResponse
	{
		$response = new CommandResponse();
		return $response;
	}
}
