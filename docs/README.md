# Symfony PharBuilder Bundle

The PharBuilder Bundle for [Symfony](https://symfony.com) can help you develop and maintain Phars
built with the Symfony Framework. It internally uses [Box](https://github.com/box-project/box) to handle 
the actual Phar stuff and is in effect not much more than a fancy Symfony integration for this 
wonderful tool.

_This Bundle supports **Symfony &geq; 5.1** and **PHP &geq; 7.3**_

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

**Build your Phar**

```bash
$ php bin/console phar:build 
```

## Adding Commands

The main idea behind this bundle is to enable developing a Phar that is separate from it's containing
Symfony application. To that extent, no commands registered to the Symfony console (`bin/console`)
are registered inside the Phar. Instead, you have to implement the
`EFrane\PharBuilder\Command\PharCommandInterface` on commands that shall be accessible in the Phar.

The `EFrane\PharBuilder\Command\PharCommand` is a Symfony Command base class implementing that interface
to safe you some typing:

```php
<?php

namespace App\Command;

use EFrane\PharBuilder\Command\PharCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooCommand extends PharCommand {
    public static $defaultName = 'foo';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Foo');

        return PharCommand::SUCCESS;
    }
} 
```

Alternatively, if you need access to commands registered by Symfony itself or other Bundles, you
need to tag the command classes with the `phar.command` tag.

::: tip
The `EFrane\PharBuilder\Command\PharCommandInterface` interface will **never** require the implementation
of any methods. It is purely used for command discovery.
:::

## Development Workflow

Phars, unlike normal PHP Applications, need a more traditional development mindset as there always
is a build step and a run step. This bundle aims to make the transition from the instant run workflow
we're used to as PHP Developers to this other style of working as smooth as possible. To that end
the commands `phar:build` and `phar:watch` are provided. 

### Developing with `phar:build`

To build the Phar with the current configuration, simply run

```bash
$ php bin/console phar:build
```

To save some time and unnecessary network requests, you can skip downloading
the dependencies after the first run by using

```bash
$ php bin/console phar:build --no-update-dependencies
```

Remember that you need to rebuild the Phar after **every** change in the code if you want
to test that change.

### Developing with `phar:watch`

::: warning
The watch command is a recent addition and may not work well yet in all circumstances.
:::

As going through the motions of the above described manual development cycle can
become quite tedious, a simpler alternative is offered:

```bash
$ php bin/console phar:watch
```

Will watch for changes in PHP and Yaml files and trigger a build if necessary.
Note that, to save on resources, the change check is only done every ten seconds.

::: tip
You cannot start out with using `phar:watch` on a newly setup system as it runs
the builds without updating the dependencies. Always run a normal `phar:build` first.
:::

## Learn More

* [Customize the Application and Kernel](./kernel.md)
* [Enable debugging features](./debugging.md)
* [How does it work behind the scenes?](./behind-the-scenes.md)
