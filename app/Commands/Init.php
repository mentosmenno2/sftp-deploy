<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;

class Init
{
	/**
	 * The configuration object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Config
	 */
	protected $config;

	/**
	 * The command response object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Models\CommandResponse
	 */
	protected $response;

	public function construct(Config $config)
	{
		$this->config = $config;
		$this->response = new CommandResponse();
	}

	public function run(): CommandResponse
	{
		return $this->response;
	}
}
