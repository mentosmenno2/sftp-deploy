<?php

namespace Mentosmenno2\SFTPDeploy\Models;

class CommandResponse
{
	private $errors = [];
	private $warnings = [];

	public function getErrors(): array
	{
		return $this->errors;
	}

	public function addError(string $message): void
	{
		$this->errors[] = $message;
	}

	public function getWarnings(): array
	{
		return $this->warnings;
	}

	public function addWarning(string $message): void
	{
		$this->warnings[] = $message;
	}
}
