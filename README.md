[![GitHub Actions status](https://github.com/mentosmenno2/sftp-deploy/workflows/Build%20%26%20test/badge.svg)](https://github.com/mentosmenno2/sftp-deploy/actions)

# Mentosmenno2 SFTP Deploy

Build and deploy PHP applications via (S)FTP.

## Installation

Install this dependency with composer as dev-dependency.

```sh
composer require mentosmenno2/sftp-deploy --dev
```

Add the commands to the Composer scripts by adding this in the `composer.json` file.

```json
"scripts": {
	"sftp-deploy" : [
		"sftp-deploy"
	]
}
```

## Configuration

Use the `init` command to generate a configuration file. See the Commands section below.

Open the generated `sftp-deploy.config.json` file, and edit the properties following the specification below.

### Configuration file properties

- TODO: Properties

## Commands

All commands can be run in the following way:

```sh
composer run sftp-deploy COMMAND_NAME
```

### Init

Generate a configuration file. This file will be called `sftp-deploy.config.json`.

```sh
composer run sftp-deploy init
```

### Build

Build the application.

```sh
composer run sftp-deploy build
```

### Deploy

Build and deploy the application to a (S)FTP server.

```sh
composer run sftp-deploy deploy
```
