<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;

class Init
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
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$generated = $this->config->generate();
		if (! $generated) {
			$response->addError('Could not create config file.');
			return $response;
		}

		$outputUtil->printNotification('Config file created.');
		return $response;
	}
}
