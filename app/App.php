<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Commands\Init as InitCommand;
use Mentosmenno2\SFTPDeploy\Commands\Build as BuildCommand;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;

class App
{
	public const EXIT_CODE_SUCCESS = 0;
	public const EXIT_CODE_WARNING = 1;
	public const EXIT_CODE_ERROR = 2;

	private const COMMANDS = [
		'init' => InitCommand::class,
		'build' => BuildCommand::class,
	];

	/**
	 * The configuration object.
	 *
	 * @var Mentosmenno2\SFTPDeploy\Config
	 */
	private $config;

	public function run(): int
	{
		// Set config
		$this->config = new Config();
		$cliArgs = $this->config->getCliArgs();

		$command_response = new CommandResponse();

		// Check if command is given
		if (!isset($cliArgs[0])) {
			$command_response->addError('No command chosen.');
			return $this->handleResponse($command_response);
		}

		// Check if command exists
		if (! array_key_exists($cliArgs[0], self::COMMANDS)) {
			$command_response->addError('Command doesn\'t exist.');
			return $this->handleResponse($command_response);
		}

		// Run command
		$commandClassName = self::COMMANDS[$cliArgs[0]];
		$command = new $commandClassName($this->config);
		$command_response = $command->run();

		return $this->handleResponse($command_response);
	}

	private function handleResponse(CommandResponse $response): int
	{
		$outputUtil = new OutputUtil();

		$errors = $response->getErrors();
		if ($errors) {
			foreach ($errors as $error) {
				$outputUtil->printError($error);
			}
			return self::EXIT_CODE_ERROR;
		}

		$warnings = $response->getWarnings();
		if ($warnings) {
			foreach ($warnings as $warning) {
				$outputUtil->printWarning($warning);
			}
			return self::EXIT_CODE_WARNING;
		}

		return self::EXIT_CODE_SUCCESS;
	}
}
