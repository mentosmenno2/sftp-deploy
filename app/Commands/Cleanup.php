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
		$outputUtil = new OutputUtil();
		$pathUtil = new PathUtil();

		$basebuildPath = $pathUtil->realPath($this->config->getBaseBuildPath());
		if ($this->config->getItem('builds_use_subdirectory')) {
			$success = $this->cleanupSubdirectoryBuilds();
			if (! $success) {
				$outputUtil->printLine('Could not cleanup builds in buildpath subdirectories.');
				return false;
			}
		} else {
			$success = $pathUtil->deleteContents($basebuildPath);
			if (! $success) {
				$outputUtil->printLine('Could not cleanup build in buildpath root.');
				return false;
			}
		}
		return true;
	}

	private function cleanupSubdirectoryBuilds(): bool
	{
		$outputUtil = new OutputUtil();
		$pathUtil = new PathUtil();
		$basebuildPath = $pathUtil->realPath($this->config->getBaseBuildPath());

		$builds = $pathUtil->getContentPaths($basebuildPath);
		$revisions = $this->config->getItem('builds_revisions');
		$buildsCount = count($builds);
		$buildsToDelete = $buildsCount - min($revisions, $buildsCount);

		if ($buildsToDelete > 0) {
			for ($buildIndex = 0; $buildIndex < $buildsToDelete; $buildIndex++) {
				$buildToDelete = $pathUtil->realPath($builds[$buildIndex]);
				$success = $pathUtil->deleteWithContents($buildToDelete);
				if (! $success) {
					$outputUtil->printLine('Could not cleanup build in buildpath subdirectory: ' . $buildToDelete);
					return false;
				}
			}
		}
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
