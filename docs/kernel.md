# Application and Kernel customization

By default, `EFrane\PharBuilder\Application\PharApplication` and
`EFrane\PharBuilder\Application\PharKernel` are used as application and kernel class.

This, however, is completely customizable and may become necessary depending
on your use case.

::: tip
As a refresher, you should read the [Symfony documentation on Console Applications][symfony-console].
:::

## Configuration

Aside from extending your custom `Application` or `Kernel` classes from the above mentioned interfaces,
you need to tell the Phar Builder about their existence:

```yaml
# in config/phar_builder.yaml

phar_builder:
  application_class: Your\Custom\Application
  kernel_class: Your\Custom\Kernel
```

## How does the default Kernel differ from Symfony's?

The short answer: Not much. It assumes the default directory structure and looks for your service
and application configuration in the appropriate places. 

The longer answer: It's in the details. Day to day use of the Kernel is not different at all. However,
Phars pose some unique constraints like being a read-only filesystem and having a build phase. These
lead to some changes around path management.

### What methods not to touch

As a result of the problem space, all path related methods have been specially catered. You should
therefore refrain from changing any of the below **unless you know what you're doing**:

- `getCacheDir`
- `getLogDir`
- `getProjectDir`

Additionally, the process of building and loading the dependency injection container has
been modified to match the needs, this adds a few more methods to be very careful with:

- `configureContainer`
- `initializeContainer`
- `getConfigCache`

[symfony-console]: https://symfony.com/doc/5.1/components/console.html
