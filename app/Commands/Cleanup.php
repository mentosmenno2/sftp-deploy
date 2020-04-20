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
		$buildsCleaned = $this->cleanupBuilds();
		if (! $buildsCleaned) {
			$response->addError('Cleaning up buils failed.');
			return $response;
		}

		$outputUtil->printNotification('Builds successfully cleaned up.');
		return $response;
	}

	private function cleanupBuilds(): bool
	{
		$outputUtil = new OutputUtil();
		if (! $this->config->getItem('builds_use_subdirectory')) {
			$outputUtil->printLine('Skipping cleanup because you are not using buildpath subdirectories.');
			return true;
		}

		$success = $this->cleanupSubdirectoryBuilds();
		if (! $success) {
			$outputUtil->printLine('Could not cleanup builds in buildpath subdirectories.');
			return false;
		}
		return true;
	}

	private function cleanupSubdirectoryBuilds(): bool
	{
		$outputUtil = new OutputUtil();
		$pathUtil = new PathUtil();
		$basebuildPath = $this->config->getBaseBuildPath();
		$basebuildPath = $pathUtil->realPath($basebuildPath);
		$basebuildPath = $pathUtil->trailingSlash($basebuildPath);

		$builds = $pathUtil->getContentPaths($basebuildPath);
		$revisions = $this->config->getItem('builds_keep_revisions');
		$buildsCount = count($builds);
		$buildsToDelete = $buildsCount - min($revisions, $buildsCount);

		if ($buildsToDelete > 0) {
			for ($buildIndex = 0; $buildIndex < $buildsToDelete; $buildIndex++) {
				$buildToDelete = $pathUtil->realPath($builds[$buildIndex]);
				$outputUtil->printLine('Cleaning up: ' . $buildToDelete);
				$success = $pathUtil->deleteWithContents($buildToDelete);
				if (! $success) {
					$outputUtil->printLine('Could not cleanup build in buildpath subdirectory: ' . $buildToDelete);
					return false;
				}
			}
		} else {
			$outputUtil->printLine('No builds to cleanup.');
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
