# A lean package that makes creating DMARC records user friendly

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cbowofrivia/dmarc-record-builder.svg?style=flat-square)](https://packagist.org/packages/cbowofrivia/dmarc-record-builder)
[![Tests](https://github.com/cbowofrivia/dmarc-record-builder/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/cbowofrivia/dmarc-record-builder/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/cbowofrivia/dmarc-record-builder.svg?style=flat-square)](https://packagist.org/packages/cbowofrivia/dmarc-record-builder)

## Installation

You can install the package via composer:

```bash
composer require cbowofrivia/dmarc-record-builder
```

## Usage

```php
$record = new DmarcRecord();

$record->policy('none')
    ->subdomainPolicy('none')
    ->pct(100)
    ->rua('mailto:charlesrbowen93@gmail.com')
    ->ruf('mailto:charlesrbowen93@gmail.com')
    ->adkim('relaxed')
    ->aspf('relaxed')
    ->reporting('any')
    ->interval(604800);

$record = (string) $record;
// v=DMARC1; p=none; sp=none; pct=100; rua=mailto:charlesrbowen93@gmail.com; ruf=mailto:charlesrbowen93@gmail.com; fo=1; adkim=r; aspf=r; ri=604800;
```

You can also build the record in the constructor

```php
$record = new DmarcRecord(
    version: 'DMARC1'
    policy: 'none'
    subdomain_policy: 'none'
    pct: 100
    rua: 'mailto:charlesrbowen93@gmail.com'
    ruf: 'mailto:charlesrbowen93@gmail.com'
    adkim: 'relaxed'
    aspf: 'relaxed' 
    reporting: 'any'
    interval: 604800
);

$record = (string) $record;
// v=DMARC1; p=none; sp=none; pct=100; rua=mailto:charlesrbowen93@gmail.com; ruf=mailto:charlesrbowen93@gmail.com; fo=1; adkim=r; aspf=r; ri=604800;
```

## Testing

```bash
composer test
```

If you're using WSL2 + Docker, you can install and test with the provided docker-compose.yaml file.
```bash
docker-compose run --rm php composer install && \
docker-compose run --rm php composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Charles Bowen](https://github.com/cbowofrivia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
