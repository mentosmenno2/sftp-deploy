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

	/**
	 * The command response object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Models\CommandResponse
	 */
	protected $response;

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->response = new CommandResponse();
	}

	public function run(): CommandResponse
	{
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$json = json_encode($this->config->getDefaultConfig(), JSON_PRETTY_PRINT);
		$fileName = $pathUtil->trailingSlashSystemPath($this->config->basePath) . Config::FILENAMME;

		if (! file_exists($fileName)) {
			$bytes = file_put_contents($fileName, $json);
			if (! $bytes) {
				$this->response->addError('Could not create configuration file');
				return $this->response;
			}
		} else {
			$this->response->addWarning('Did not create config file, file already exists.');
			return $this->response;
		}

		$outputUtil->printNotification('Configuration file created');

		return $this->response;
	}
}
