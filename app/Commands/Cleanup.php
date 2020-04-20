<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;

class Cleanup extends BaseCommand
{
	public function run(): CommandResponse
	{
		// Run parent
		$outputUtil = new OutputUtil();
		$response = parent::run();
		if ($response->hasErrors()) {
			return $response;
		}

		// Deploy project
		$outputUtil->printLine('Cleaning up builds.');
		$projectDeployed = $this->cleanupBuilds();
		if (! $projectDeployed) {
			$response->addError('Cleaning up buils failed.');
			return $response;
		}

		$outputUtil->printNotification('Builds successfully cleaned up.');
		return $response;
	}

	private function cleanupBuilds(): bool
	{
		return true;
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
