# Fox's Toolset | Artisan Command Collection

A collection of useful (Laravel)Artisan commands.

[![Build Status](https://travis-ci.com/christian-graf/artisan-command-collection.svg?branch=master)](https://travis-ci.com/christian-graf/artisan-command-collection)
[![Latest Stable Version](https://poser.pugx.org/fox/artisan-cmd-collection/v/stable)](https://packagist.org/packages/fox/artisan-cmd-collection)
[![Total Downloads](https://poser.pugx.org/fox/artisan-cmd-collection/downloads)](https://packagist.org/packages/fox/artisan-cmd-collection)
[![License](https://poser.pugx.org/fox/artisan-cmd-collection/license)](https://packagist.org/packages/fox/artisan-cmd-collection)


## Installation

In Laravel >= 6.0 the collection will register via the new package discovery feature, so you only need to add the package via composer to your project.

```bash
composer require "fox/artisan-cmd-collection:^2.0"
```

After the installation is complete, you should see

```bash
Discovered Package: fox/artisan-cmd-collection
```

and you are ready to go!

To see all available commands of this collection run

```bash
php artisan list fox
```

in your command line.

## Available commands

### fox:cache:clear

```bash
Usage:
  fox:cache:clear [options] [--] [<cache>]...

Arguments:
  cache                 Type of the cache(s) to clear - available values are [app|config|route|view]

Options:
      --all             Clear all caches
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Clears multiple caches used by your application. e.G. config or view caches
```

### db:create

```bash
Description:
  Create a database using the designated connection.

Usage:
  db:create [options] [--] [<connection>]

Arguments:
  connection                   Name of the connection defined in your config/database.php file. [default: "default"]

Options:
  -h, --help                   Display this help message
  -q, --quiet                  Do not output any message
  -V, --version                Display this application version
      --ansi                   Force ANSI output
      --no-ansi                Disable ANSI output
  -n, --no-interaction         Do not ask any interactive question
      --env[=ENV]              The environment the command should run under
  -incl-drop-database, --drop  Include drop database statement
  -v|vv|vvv, --verbose         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

## Development - Getting Started

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## Changelog

See the [CHANGELOG](CHANGELOG.md) file.

## License

See the [LICENSE](LICENSE.md) file.
