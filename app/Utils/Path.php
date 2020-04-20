<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;

class Path
{
	public function realPath(string $path): string
	{
		$realpath = realpath($path);
		if (false === $realpath) {
			return $path;
		}
		return $realpath;
	}

	public function trailingSlash(string $path): string
	{
		$path = $this->unTrailingSlash($path);
		return $path . DIRECTORY_SEPARATOR;
	}

	public function unTrailingSlash(string $path): string
	{
		return rtrim($path, '/\\');
	}

	public function isRootPath(string $path): bool
	{
		if ($path === ltrim($path, '/\\')) {
			return false;
		}
		return true;
	}

	public function toWindowsPath(string $path)
	{
		$path = str_replace('/', '\\', $path);
		return $path;
	}

	public function toLinuxPath(string $path)
	{
		$path = str_replace('\\', '/', $path);
		return $path;
	}

	public function getRelative(string $path, string $rootPath): string
	{
		$path = $this->realPath($path);
		$rootPath = $this->realPath($rootPath);
		$rootPath = $this->trailingSlash($rootPath);

		$relative = '';
		if (substr($path, 0, strlen($rootPath)) == $rootPath) {
			$relative = substr($path, strlen($rootPath));
		}

		return $relative;
	}

	public function getRecursiveContentPaths($path): array
	{
		$result = array();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$recursive = $this->getRecursiveContent($path);
		foreach ($recursive as $subdir => $contents) {
			if (is_array($contents)) {
				// Directory
				$subdirPath = $this->trailingSlash($subdir);
				$result = array_merge($result, $this->getRecursiveContentPaths($path . $subdirPath));
			} else {
				// File
				$result[] = $this->realPath($path . $contents);
			}
		}

		return $result;
	}

	public function getRecursiveContent($path): array
	{
		$result = array();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$cdir = scandir($path);
		if (! $cdir) {
			return $result;
		}

		foreach ($cdir as $key => $value) {
			if (!in_array($value, array(".",".."))) {
				if (is_dir($path . $value)) {
					$result[$value] = $this->getRecursiveContent($path . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	public function getContentPaths($path): array
	{
		$result = array();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$contents = $this->getContent($path);
		foreach ($contents as $index => $content_item) {
			$resultPath = $this->realPath($path . $content_item);
			$resultPath = $this->trailingSlash($resultPath);
			$result[] = $resultPath;
		}

		return $result;
	}

	public function getContent($path): array
	{
		$result = array();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$cdir = scandir($path);
		if (! $cdir) {
			return $result;
		}

		foreach ($cdir as $key => $value) {
			if (!in_array($value, array(".",".."))) {
				$result[] = $value;
			}
		}
		return $result;
	}

	public function delete(string $path): bool
	{
		$outputUtil = new OutputUtil();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$success = rmdir($path);

		if (! $success) {
			$outputUtil->printLine('Could not delete directory: ' . $path);
			return false;
		}
		return true;
	}

	public function deleteContents(string $path): bool
	{
		$outputUtil = new OutputUtil();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);
		$files = array_diff(scandir($path), array('.','..'));
		$success = true;

		foreach ($files as $file) {
			$filePath = $path . DIRECTORY_SEPARATOR . $file;
			if (is_dir($filePath)) {
				$deleteWithContentsSuccess = $this->deleteWithContents($filePath);
				if (!$deleteWithContentsSuccess) {
					$success = false;
				}
			} else {
				$chmodSuccess = chmod($filePath, 0644);
				$unlinkSuccess = unlink($filePath);
				if (! $chmodSuccess || !$unlinkSuccess) {
					$success = false;
					$outputUtil->printLine('Could not delete file: ' . $path);
				}
			}
		}

		return $success;
	}

	public function deleteWithContents(string $path): bool
	{
		$outputUtil = new OutputUtil();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$contentsSuccess = $this->deleteContents($path);
		$directorySuccess = $this->delete($path);

		if (! $contentsSuccess || !$directorySuccess) {
			$outputUtil->printLine('Could not delete direcory with contents: ' . $path);
			return false;
		}
		return true;
	}
}
