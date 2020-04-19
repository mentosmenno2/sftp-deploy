[![GitHub Actions status](https://github.com/mentosmenno2/sftp-deploy/workflows/Build%20%26%20test/badge.svg)](https://github.com/mentosmenno2/sftp-deploy/actions)

# Mentosmenno2 SFTP Deploy

Deploy applications via SFTP

## Installation

Add the repo to the project by adding this in the `composer.json` file.

```json
"repositories": [
	{
		"url": "https://github.com/mentosmenno2/sftp-deploy.git",
		"type": "git"
	}
]
```

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
