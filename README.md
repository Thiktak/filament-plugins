# Filament Plugins

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thiktak/filament-plugins.svg?style=flat-square)](https://packagist.org/packages/thiktak/filament-plugins)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/thiktak/filament-plugins/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/thiktak/filament-plugins/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/thiktak/filament-plugins/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/thiktak/filament-plugins/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/thiktak/filament-plugins.svg?style=flat-square)](https://packagist.org/packages/thiktak/filament-plugins)


Get some information about Laravel, Filament, Composer and your plugins directly from web.

> [!WARNING]
> Reserved for Admin purpose.


> [!WARNING]
> In dev mode. Will be subject to refactoring.

## Todo

- [ ] Refactor code
- [ ] Review Composer logic
- [ ] Add a "read only" mode (without run Composer)
- [ ] Cron mode for composer check
- [ ] handle auth/policies access
- [ ] Option to configure access to some parts
- [ ] Customize access menu (Navigation (with everything or not), UserMenu)

## Installation

You can install the package via composer:

```bash
composer require thiktak/filament-plugins
```

Add into your Provider\Filament\(...)PanelProvider :

```php
    public function panel(Panel $panel): Panel
    {
         return $panel
            // ...
            ->plugin(\Thiktak\FilamentPlugins\FilamentPluginsPlugin::make())
            // ...
        ;
    }
```

## Usage

For now (will change), you can access via:

![image](https://github.com/Thiktak/filament-plugins/assets/1201486/beaf4403-9138-461e-a32d-e66ea005800c)

### Dashboard
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/0fc8d254-d4b5-4e0f-9273-428551ecd1c8)

#### Application Panels
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/e938ea64-bf28-483d-bdc4-70a614cddb30)
...
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/3493189a-d2d8-4921-83b4-5059bb1613e4)

### Composer
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/88045186-e4e1-4b7c-b62e-69c2297fbc31)

### Artisan 'about'
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/2c47ce51-030b-4ebe-9a9c-d08e0d2c0368)

### All views
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/d4e6a3aa-3999-4768-908f-50df8b38853c)
... With more information
![image](https://github.com/Thiktak/filament-plugins/assets/1201486/f3869850-b3ed-4866-aec2-64c94341dd8a)



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
