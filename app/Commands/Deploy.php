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

		if (! $this->checkRequirements) {
			$response->addError('Requirements for this command to run are not met.');
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

	private function makeDeploymentPath(): bool
	{
		$path = $this->getDeploymentPath();
		$success = mkdir($path, 0777, true);
		return $success;
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
