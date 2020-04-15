<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Commands\Init as InitCommand;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;

class App
{
	const EXIT_CODE_SUCCESS = 0;
	const EXIT_CODE_WARNING = 1;
	const EXIT_CODE_ERROR = 2;

	/**
	 * The base path of the project installation.
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * The configuration object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Config
	 */
	protected $config;

	public function __construct()
	{
		$this->basePath = getcwd();
	}

	public function run(): int
	{
		$this->config = new Config();
		$cliArgs = $this->config->getCliArgs();

		$command = null;
		$command_response = new CommandResponse;
		if (!isset($cliArgs[0])) {
			$command_response->addError('No command chosen.');
			return $this->handleResponse($command_response);
		}

		switch ($cliArgs[0]) {
			case 'init':
				$command = new InitCommand($this->config);
				// $command_response = $command->run();
				break;

			default:
				$command_response->addError('Command doesn\'t exist.');
				break;
		}
		return $this->handleResponse($command_response);
	}

	protected function handleResponse(CommandResponse $response): int
	{
		$errors = $response->getErrors();
		if ($errors) {
			foreach ($errors as $error) {
				echo $error . PHP_EOL;
			}
			return self::EXIT_CODE_ERROR;
		}

		$warnings = $response->getWarnings();
		if ($warnings) {
			foreach ($warnings as $warning) {
				echo $warning . PHP_EOL;
			}
			return self::EXIT_CODE_WARNING;
		}

		return self::EXIT_CODE_SUCCESS;
	}
}
