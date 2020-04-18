<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;

class Deploy extends BaseCommand
{
	private $deploymentPath;

	public function run(): CommandResponse
	{
		$outputUtil = new OutputUtil();
		$response = parent::run();
		if ($response->hasErrors()) {
			return $response;
		}

		$outputUtil->printNotification('Project successfully deployed to the SFTP server.');
		return $response;
	}

	protected function checkRequirements(): bool
	{
		$success = parent::checkRequirements();
		$outputUtil = new OutputUtil();

		if (! $this->config->fileExists()) {
			$outputUtil->printLine('Config file does not exist.');
			$success = false;
		}
		return $success;
	}
}
