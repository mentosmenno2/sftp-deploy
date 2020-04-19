<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

class Path
{
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

	public function getContents($path): array
	{
		$result = array();
		$path = $this->trailingSlash($path);

		$recursive = $this->getContentsRecursive($path);
		foreach ($recursive as $subdir => $contents) {
			if (is_array($contents)) {
				// Directory
				$subdirPath = $this->trailingSlash($subdir);
				$result = array_merge($result, $this->getContents($path . $subdirPath));
			} else {
				// File
				$result[] = $path . $contents;
			}
		}

		return $result;
	}

	public function getContentsRecursive($path): array
	{
		$result = array();
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
		rmdir($path);
	}

	public function deleteContents(string $path)
	{
		$files = array_diff(scandir($path), array('.','..'));
		foreach ($files as $file) {
			$filePath = $path . DIRECTORY_SEPARATOR . $file;
			(is_dir($filePath)) ? $this->deleteWithContents($filePath) : unlink($filePath);
		}
	}

	public function deleteWithContents(string $path)
	{
		$this->deleteContents($path);
		$this->delete($path);
	}
}
