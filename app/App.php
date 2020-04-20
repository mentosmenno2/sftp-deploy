<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Commands\Init as InitCommand;
use Mentosmenno2\SFTPDeploy\Commands\Build as BuildCommand;
use Mentosmenno2\SFTPDeploy\Commands\Deploy as DeployCommand;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Shell as ShellUtil;

class App
{
	private const COMMANDS = [
		'init' => InitCommand::class,
		'build' => BuildCommand::class,
		'deploy' => DeployCommand::class,
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
		$cliCommandArg = $this->config->getCliArg(0);

		$command_response = new CommandResponse();

		// Check if command is given
		if (!isset($cliCommandArg)) {
			$command_response->addError('No command chosen.');
			return $this->handleResponse($command_response);
		}

		// Check if command exists
		if (! array_key_exists($cliCommandArg, self::COMMANDS)) {
			$command_response->addError('Command doesn\'t exist.');
			return $this->handleResponse($command_response);
		}

		// Run command
		$commandClassName = self::COMMANDS[$cliCommandArg];
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
			return ShellUtil::EXIT_CODE_ERROR;
		}

		$warnings = $response->getWarnings();
		if ($warnings) {
			foreach ($warnings as $warning) {
				$outputUtil->printWarning($warning);
			}
			return ShellUtil::EXIT_CODE_WARNING;
		}

		return ShellUtil::EXIT_CODE_SUCCESS;
	}
}
