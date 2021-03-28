# Symfony PharBuilder Bundle

The PharBuilder Bundle for [Symfony](https://symfony.com) can help you developing and maintaining Phars
built with the Symfony Framework. It internally uses [Box](https://github.com/box-project/box) to handle 
the actual Phar stuff and is in effect not much more than a fancy Symfony integration for this 
wonderful tool.

::: warning
Currently only CLI applications are supported. It is however planned to support both CLI and Web-facing
applications once this bundle reaches v1.0.
:::

## Getting Started

First install the package with:

```bash
$ composer require efrane/phar-builder-bundle
```

Then activate it in your bundles configuration (typically `config/bundles.php`):

```php
return [
    // ...
    EFrane\PharBuilder\Bundle\PharBuilderBundle::class => ['all'],
    // ...
];
```

**Required configuration**

The PharBuilder has several configuration options, most of which can be skipped for typical setups.
To get started, you only need to set a name for the generated phar and an output directory:

``` yaml
# config/packages/phar_builder.yaml
phar_builder:
    build:
        output_path: '%kernel.project_dir%'
        output_filename: 'your-phar-name'
```

::: tip
Add the concatenation of `output_path` and `output_filename` to your `.gitignore`.
:::

## Adding Commands

The whole point of this bundle is to enable developing a Phar that is separate from it's containing
Symfony application. To that extent, no commands registered to the Symfony console (`bin/console`)
are registered inside the Phar. Instead, you have to implement the
`EFrane\PharBuilder\Command\PharCommandInterface` on commands that shall be accessible in the Phar.
Alternatively, if you need access to commands registered by Symfony itself or other Bundles, you
need to tag the command classes with the `phar.command` tag. 

::: tip
The `EFrane\PharBuilder\Command\PharCommandInterface` interface will **never** require the implementation
of any methods. It is purely used for command discovery.
:::

## Development Workflow

tbd.
