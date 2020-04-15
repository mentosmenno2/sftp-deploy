<?php

namespace Mentosmenno2\SFTPDeploy;

class App
{
	const EXIT_CODE_SUCCESS = 0;
	const EXIT_CODE_WARNING = 0;
	const EXIT_CODE_ERROR = 0;

	public function run(): int
	{
		return self::EXIT_CODE_SUCCESS;
	}
}
