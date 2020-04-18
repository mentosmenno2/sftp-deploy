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
		return rtrim($path, DIRECTORY_SEPARATOR);
	}
}
