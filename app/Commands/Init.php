<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;

class Init extends BaseCommand
{
	public function run(): CommandResponse
	{
		$response = parent::run();
		$outputUtil = new OutputUtil();

		$outputUtil->printLine('Creating configuration file.');
		$generated = $this->config->generate();
		if (! $generated) {
			$response->addError('Could not create config file.');
			return $response;
		}

		$outputUtil->printNotification('Config file created.');
		return $response;
	}
}
