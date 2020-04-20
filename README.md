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

> __Does these actions:__
> - First builds your app with the `build` command.
> - Then deploys that build to the (S)FTP server.
> - Finally cleans up old builds with the `cleanup` command.

```sh
composer run sftp-deploy deploy
```

### Cleanup

Cleanup old builds.

> __Notes:__
> - This only works when `builds_use_subdirectory` is enabled in the configuration.
> - Keeps up to `builds_keep_revisions` intact. See configuration.

```sh
composer run sftp-deploy deploy
```

## Contributing

You are free to contribute on this project. I want this project to become better, just like you do.

If you want to contribute, please create a fork, and make a pull request.

Please note that your pull request needs to comply to these rules:

- It has to contain a description of why it's required or uesful.
- It has to contain a description of what the fix does.
- If some code could be unclear for other developers, please add comments.
- The code must pass the automatic Github action checks. You can test that locally by running `composer run test`.

### Local installation

```sh
git clone git@github.com:mentosmenno2/sftp-deploy.git
composer install
composer dump-autoload
```

### Code checks

Code must pass code checks to be used in this project.

```sh
composer run test
```

You can also let the code checker try to fix problems by itself.

> __Note:__
> This only fixes common problems. You may need to still fix some yourself.

```sh
composer run fix
```
