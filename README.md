[![Tests (PHP)](https://github.com/eFrane/phar-builder-bundle/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/eFrane/phar-builder-bundle/actions/workflows/phpunit.yml)

# Phar Builder Bundle for Symfony

> Utils to make creating (CLI) Phars with Symfony easy.

**WARNING** This project is in an early alpha stage and should not be used in production
environments yet. 

## Requirements

* Symfony >= 5.1
* Box and required Box Plugins should be installed in the Host project (this may change)

## Installation

```bash
composer require efrane/phar-builder-bundle
``` 

In `config/bundles.php`:

```
EFrane\PharBuilder\Bundle\PharBuilderBundle::class => ['all' => true],
```

## Configuration

Use `bin/console config:dump-reference phar_builder` to dump the reference configuration.
Place your own in the default Symfony places or wherever you're loading configuration from.

## Usage

* Install Box (see `bin/console phar:setup-info` for assistance)
* Configure at least output path and name
* Dump the box configuration with `bin/console phar:dump:box`
* Build the phar with `bin/console phar:build`
