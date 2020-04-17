<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use DateTime;
use Mentosmenno2\SFTPDeploy\Config;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;

class Deploy extends BaseCommand
{
	private $deploymentPath;

	public function run(): CommandResponse
	{
		$response = parent::run();
		$outputUtil = new OutputUtil();

		$outputUtil->printLine('Creating deployment path.');
		$deploymentPathCreated = $this->createDeploymentPath();
		if (! $deploymentPathCreated) {
			$response->addError('Could not create deployment path.');
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

	private function createDeploymentPath(): bool
	{
		$outputUtil = new OutputUtil();
		$path = $this->getDeploymentPath();
		if (is_dir($path)) {
			$outputUtil->printLine('Path exists.');
			return true;
		}

		$created = mkdir($path, 0777, true);
		if(!$created) {
			$outputUtil->printLine('Could not create path.');
			return false;
		}
		return true;
	}

	private function getDeploymentPath(): string
	{
		if (! $this->deploymentPath) {
			$pathUtil = new PathUtil();
			$deploymentPath = $pathUtil->trailingSlashSystemPath($this->config->getBasePath());
			$deploymentPath .= $pathUtil->trailingSlashSystemPath($this->config->getItem('deployments_directory'));

			if ($this->config->getItem('use_deployment_subdirectory')) {
				$subDirName = ( new DateTime() )->format('YmdHis');
				$deploymentPath .= $pathUtil->trailingSlashSystemPath($subDirName);
			}
			$this->deploymentPath = $deploymentPath;
		}


		return $this->deploymentPath;
	}
}
