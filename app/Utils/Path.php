<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

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

	public function getContents($path): array
	{
		$result = array();
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);

		$recursive = $this->getContentsRecursive($path);
		foreach ($recursive as $subdir => $contents) {
			if (is_array($contents)) {
				// Directory
				$subdirPath = $this->trailingSlash($subdir);
				$result = array_merge($result, $this->getContents($path . $subdirPath));
			} else {
				// File
				$result[] = $this->realPath($path . $contents);
			}
		}

		return $result;
	}

	public function getContentsRecursive($path): array
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
					$result[$value] = $this->getContentsRecursive($path . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	public function delete(string $path)
	{
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);
		rmdir($path);
	}

	public function deleteContents(string $path)
	{
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);
		$files = array_diff(scandir($path), array('.','..'));
		foreach ($files as $file) {
			$filePath = $path . DIRECTORY_SEPARATOR . $file;
			(is_dir($filePath)) ? $this->deleteWithContents($filePath) : unlink($filePath);
		}
	}

	public function deleteWithContents(string $path)
	{
		$path = $this->realPath($path);
		$path = $this->trailingSlash($path);
		$this->deleteContents($path);
		$this->delete($path);
	}
}
