<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Filesystem as FlysystemFilesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Mentosmenno2\SFTPDeploy\Config;

class Sftp
{
	private const AVAILABLE_ADAPTERS = [
		'ftp' => FtpAdapter::class,
		'sftp' => SftpAdapter::class,
	];
	private $adapterType;
	private $adapterOptions;
	private $adapter;
	private $fileSystem;

	public function __construct(string $adapterType = 'sftp', array $adapterOptions = null)
	{
		$this->adapterType = $adapterType;
		$this->adapterOptions = $adapterOptions;
	}

	public function getFileSystem(): ?FlysystemFilesystem
	{
		if (! $this->fileSystem) {
			$adapter = $this->getAdapter();
			if ($adapter) {
				$this->fileSystem = new FlysystemFilesystem($adapter);
			}
		}
		return $this->fileSystem;
	}

	private function getAdapter(): ?AbstractAdapter
	{
		if (! $this->adapter) {
			if (array_key_exists($this->adapterType, self::AVAILABLE_ADAPTERS)) {
				$adapterClass = self::AVAILABLE_ADAPTERS[$this->adapterType];
				$adapterOptions = $this->getAdapterOptions();
				if ($adapterOptions) {
					$this->adapter = new $adapterClass($adapterOptions);
				}
			}
		}
		return $this->adapter;
	}

	private function getAdapterOptions(): ?array
	{
		if (! $this->adapterOptions) {
			$config = new Config();

			// Default options
			$options = [
				'ftp' => [
					'host' => $config->getItem('sftp_host'),
					'username' => $config->getItem('sftp_username'),
					'password' => $config->getItem('sftp_password'),

					'port' => $config->getItem('sftp_port') ?: 21,
					'passive' => true,
					'ssl' => true,
					'timeout' => 30,
					'ignorePassiveAddress' => false,
				],
				'sftp' => [
					'host' => $config->getItem('sftp_host'),
					'port' => $config->getItem('sftp_port') ?: 22,
					'username' => $config->getItem('sftp_username'),
					'password' => $config->getItem('sftp_password'),
					'timeout' => 30,
					'directoryPerm' => 0755
				],
			];

			$sftpRoot = $config->getItem('sftp_root');
			if (null !== $sftpRoot) {
				$options['ftp']['root'] = $sftpRoot;
				$options['sftp']['root'] = $sftpRoot;
			}

			$sftpPassive = $config->getItem('sftp_passive');
			if (null !== $sftpPassive) {
				$options['ftp']['passive'] = $sftpPassive;
			}

			$sftpSsl = $config->getItem('sftp_ssl');
			if (null !== $sftpSsl) {
				$options['ftp']['ssl'] = $sftpSsl;
			}

			$sftpIngorePassiveAddress = $config->getItem('sftp_ignore_passive_address');
			if (null !== $sftpIngorePassiveAddress) {
				$options['ftp']['ignorePassiveAddress'] = $sftpIngorePassiveAddress;
			}

			$sftpPkFile = $config->getItem('sftp_private_key_file');
			if (null !== $sftpPkFile) {
				$options['sftp']['privateKey'] = $sftpPkFile;
			}

			$sftpPkPassword = $config->getItem('sftp_private_key_password');
			if (null !== $sftpPkPassword) {
				$options['sftp']['passphrase'] = $sftpPkPassword;
			}

			$sftpDirPerm = $config->getItem('sftp_directory_permission');
			if (null !== $sftpDirPerm) {
				$options['sftp']['directoryPerm'] = $sftpDirPerm;
			}

			if (array_key_exists($this->adapterType, $options)) {
				$this->adapterOptions = $options[$this->adapterType];
			}
		}
		return $this->adapterOptions;
	}
}
