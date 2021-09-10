[![GitHub Actions status](https://github.com/mentosmenno2/sftp-deploy/workflows/Build%20%26%20test/badge.svg)](https://github.com/mentosmenno2/sftp-deploy/actions)

# Mentosmenno2 SFTP Deploy

Build and deploy PHP applications via (S)FTP, with a simple command!

## Requirements

- PHP 7.1+

## Installation

Install this dependency with composer as dev-dependency.

```sh
composer require mentosmenno2/sftp-deploy --dev
```

Add the commands to the Composer scripts by adding this in the `composer.json` file. For long deploy processes, you may need to disable the process timeout as shown in the example deploy command below.

```json
"scripts": {
	"sftp-deploy" : [
		"sftp-deploy"
	],
	"deploy": [
		"Composer\\Config::disableProcessTimeout",
		"@sftp-deploy deploy"
	]
}
```

## Configuration

Use the `init` command to generate a configuration file. See the Commands section below.

Open the generated `sftp-deploy.config.json` file, and edit the properties following the specification below.

### Configuration file properties

| Property 							| Type 			| Default 				| Description 																							|
|--- 								|--- 			|--- 					|--- 																									|
| __builds_directory__ 				| string 		| `"deployments"` 		| The path where the the build will be created. Relative to your project directory.						|
| __builds_use_subdirectory__ 		| boolean 		| `true` 				| Will make a separate subdirectory inside the `builds_directory` for your build. 						|
| __builds_keep_revisions__ 		| integer 		| `5` 					| Revisions of builds to keep when cleaning up builds. 													|
| __run_before__ 					| [] string 	| `[]` 					| Commands to run before cloning your project from git 													|
| __repo_url__ 						| string / null | `null` 				| Repository url to use for cloning your project from git. 												|
| __repo_clone_directory__ 			| string 		| `"."` 				| Path to clone the repo in. Relative to the `builds_directory` (with subdirectory if enabled). 		|
| __repo_checkout__ 				| string 		| `"master"` 			| Default branch or tag to checkout when cloning the repo. 												|
| __run_after__ 					| [] string 	| `[]` 					| Commands to run after cloning your project from git. 													|
| __deploy_directory__ 				| string 		| `"."` 				| Directory to upload to (S)FTP. Relative to the `builds_directory` (with subdirectory if enabled). 	|
| __sftp_adapter__ 					| string 		| `"sftp"` 				| Options: `sftp`, `ftp`. Adapter to choose for deploying. 												|
| __sftp_host__ 					| string 		| `"example.com"` 		| (S)FTP host. 																							|
| __sftp_port__ 					| integer 		| `22` 					| (S)FTP port. SFTP usually uses `22`. FPT usually uses `21`. 											|
| __sftp_username__ 				| string 		| `"username"` 			| (S)FTP username. 																						|
| __sftp_password__ 				| string 		| `"password"` 			| (S)FTP password. 																						|
| __sftp_root__ 					| string 		| `"/path/to/root"` 	| Path where files from `deploy_directory` should be placed. Absolute path from (S)FTP root. 			|
| __sftp_passive__	 				| boolean 		| `true` 				| Use a passive connection. Only works when `ftp` is selected as `sftp_adapter`. 						|
| __sftp_ssl__	 					| boolean 		| `true` 				| Use a SSL connection. Only works when `ftp` is selected as `sftp_adapter`. 							|
| __sftp_ignore_passive_address__ 	| boolean 		| `false` 				| Don't use passive address. Useful when connection is blocked by firewall. Only works when `ftp` is selected as `sftp_adapter`. 	|
| __sftp_private_key_file__ 		| string / null | `null` 				| Path to private key file. Absolute path. Only works when `sftp` is selected as `sftp_adapter`. 		|
| __sftp_private_key_password__ 	| string / null | `null` 				| Private key password. Only works when `sftp` is selected as `sftp_adapter`. 							|
| __sftp_directory_permission__ 	| integer 		| `0755` 				| Set directory permission of `sftp_root`. 																|


You can also use a custom configuration file. To do so, you can specify your custom configuration file, using the `config` operand. If you do so, you should also change the current deploy command (see installation section), or create a new one.

```json
"scripts": {
	"deploy": [
		"@sftp-deploy deploy -- --config=\"custom-config-filename.json\""
	]
}
```

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
composer run sftp-deploy build [checkout]
```

#### Arguments
| Argument 	| Required 	| Default 	| Description 												|
|--- 		|--- 		|--- 		|--- 														|
| checkout 	| No 		| master 	| The branch or tag you would like to checkout with git. Overwrites `repo_checkout` configuration parameter. 	|

### Deploy

Build and deploy the application to a (S)FTP server.

> __Does these actions:__
> - First builds your app with the `build` command.
> - Then deploys that build to the (S)FTP server.
> - Finally cleans up old builds with the `cleanup` command.

```sh
composer run sftp-deploy deploy [checkout]
```

#### Arguments
| Argument 	| Required 	| Default 	| Description 												|
|--- 		|--- 		|--- 		|--- 														|
| checkout 	| No 		| master 	| The branch or tag you would like to checkout with git. Overwrites `repo_checkout` configuration parameter. 	|

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
