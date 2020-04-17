<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

class Path
{
	public function trailingSlashUrl(string $string): string
	{
		$string = $this->unTrailingSlashUrl($string);
		return $string . '/';
	}

	public function trailingSlashSystemPath(string $string): string
	{
		$string = $this->unTrailingSlashSystemPath($string);
		return $string . DIRECTORY_SEPARATOR;
	}

	public function unTrailingSlashUrl(string $string): string
	{
		return rtrim($string, '/');
	}

	public function unTrailingSlashSystemPath(string $string): string
	{
		return rtrim($string, DIRECTORY_SEPARATOR);
	}
}
