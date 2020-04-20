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

| Property 						| Type 			| Default 				| Description 																							|
|--- 							|--- 			|--- 					|--- 																									|
| builds_directory 				| string 		| `"deployments"` 		| The path where the the build will be created. Relative to your project directory.						|
| builds_use_subdirectory 		| boolean 		| `true` 				| Will make a separate subdirectory inside the `builds_directory` for your build. 						|
| builds_keep_revisions 		| integer 		| `5` 					| Revisions of builds to keep when cleaning up builds. 													|
| run_before 					| [] string 		| `[]` 					| Commands to run before cloning your project from git 													|
| repo_url 						| string / null | `null` 				| Repository url to use for cloning your project from git. 												|
| repo_clone_directory 			| string 		| `"."` 				| Path to clone the repo in. Relative to the `builds_directory` (with subdirectory if enabled). 		|
| repo_checkout 				| string 		| `"master"` 			| Default branch or tag to checkout when cloning the repo. 												|
| run_after 					| [] string 		| `[]` 					| Commands to run after cloning your project from git. 													|
| deploy_directory 				| string 		| `"."` 				| Directory to upload to (S)FTP. Relative to the `builds_directory` (with subdirectory if enabled). 	|
| sftp_adapter 					| string 		| `"sftp"` 				| Options: `sftp`, `ftp`. Adapter to choose for deploying. 												|
| sftp_host 					| string 		| `"example.com"` 		| (S)FTP host. 																							|
| sftp_port 					| integer 		| `22` 					| (S)FTP port. SFTP usually uses `22`. FPT usually uses `21`. 											|
| sftp_username 				| string 		| `"username"` 			| (S)FTP username. 																						|
| sftp_password 				| string 		| `"password"` 			| (S)FTP password. 																						|
| sftp_root 					| string 		| `"/path/to/root"` 	| Path where files from `deploy_directory` should be placed. Absolute path from (S)FTP root. 			|
| sftp_passive	 				| boolean 		| `true` 				| Use a passive connection. Only works when `ftp` is selected as `sftp_adapter`. 						|
| sftp_ssl	 					| boolean 		| `true` 				| Use a SSL connection. Only works when `ftp` is selected as `sftp_adapter`. 							|
| _sftp_ignore_passive_address_ 	| boolean 		| `false` 				| Don't use passive address. Useful when connection is blocked by firewall. Only works when `ftp` is selected as `sftp_adapter`. 	|
| sftp_private_key_file 		| string / null | `null` 				| Path to private key file. Absolute path. Only works when `sftp` is selected as `sftp_adapter`. 		|
| sftp_private_key_password 	| string / null | `null` 				| Private key password. Only works when `sftp` is selected as `sftp_adapter`. 							|
| sftp_directory_permission 	| integer 		| `0755` 				| Set directory permission of `sftp_root`. 																|

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
