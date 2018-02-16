# Fox's Toolset | Artisan Command Collection

A collection of useful (Laravel)Artisan commands.

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

## Development - Getting Started

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## Changelog

See the [CHANGELOG](CHANGELOG.md) file.

## License
 
See the [LICENSE](LICENSE.md) file.
