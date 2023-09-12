![Screenshot](https://i.imgur.com/Wp0wFUH.png)

# Envoyer Deploy Commands

![Latest Stable Version](https://img.shields.io/packagist/v/log1x/envoyer-deploy-commands.svg?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/log1x/envoyer-deploy-commands.svg?style=flat-square)
![Build Status](https://img.shields.io/github/actions/workflow/status/log1x/envoyer-deploy-commands/main.yml?branch=main&style=flat-square)

Envoyer Deploy Commands is a simple Laravel package providing Artisan commands to deploy through [Envoyer](https://envoyer.io/) using the API.

## Requirements

- [PHP](https://secure.php.net/manual/en/install.php) >= 8.1
- [Laravel](https://laravel.com/) >= 9.0

## Installation

Install via Composer:

```bash
$ composer require log1x/envoyer-deploy-commands --dev
```

## Getting Started

Start by publishing the package configuration:

```bash
$ php artisan vendor:publish --tag=envoyer-deploy-config
```

Customize the configuration adding at bare minimum your Envoyer API key with the `deployments:create` permission scope.

After adding an API key, you can list the available projects and their corrosponding ID's:

```bash
$ php artisan deploy:list [search]
```

Once you have your project ID, add it the `projects` section of `config/envoyer.php` along with an alias.

## Usage

Usage is extremely straight forward. Use the `artisan deploy` command optionally specifying your project alias. If you only have 1 project, it will be used by default.

```bash
$ php artisan deploy [alias]
```

## Bug Reports

If you discover a bug in Envoyer Deploy Commands, please [open an issue](https://github.com/Log1x/envoyer-deploy-commands/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

Envoyer Deploy Commands is provided under the [MIT License](LICENSE.md).
