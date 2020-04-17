<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;

class Init extends BaseCommand
{
	public function run(): CommandResponse
	{
		$response = parent::run();
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
