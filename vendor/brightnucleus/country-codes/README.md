# Bright Nucleus Country Codes Database

[![Latest Stable Version](https://poser.pugx.org/brightnucleus/country-codes/v/stable)](https://packagist.org/packages/brightnucleus/country-codes)
[![Total Downloads](https://poser.pugx.org/brightnucleus/country-codes/downloads)](https://packagist.org/packages/brightnucleus/country-codes)
[![Latest Unstable Version](https://poser.pugx.org/brightnucleus/country-codes/v/unstable)](https://packagist.org/packages/brightnucleus/country-codes)
[![License](https://poser.pugx.org/brightnucleus/country-codes/license)](https://packagist.org/packages/brightnucleus/country-codes)

This is a Composer plugin that provides an automated version of the free MaxMind GeoLite CSV Country Codes database.

The main advantage is that the downloaded database will be checked for updates on each `composer install` and `composer update`.

## Table Of Contents

* [Attribution](#attribution)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Contributing](#contributing)
* [License](#license)

## Attribution

This product includes GeoLite Legacy data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.

## Installation

The only thing you need to do to make this work is adding this package as a dependency to your project:

```BASH
composer require brightnucleus/country-codes
```

## Basic Usage

On each `composer install` or `composer update`, a check will be made to see whether there's a new version of the database available. If there is, that new version is downloaded.

Usage is pretty straight-forward. Just use one of the two provided static methods:

```PHP
<?php

use BrightNucleus\CountryCodes\Country;

// Get the name from an ISO 3166 country code.
$name = Country::getNameFromCode( 'US' ); // Returns 'United States'.

// Get the ISO 3166 country code from a country name.
$code = Country::getCodeFromName( 'United States' ); // Returns 'US'.
```

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

This code is released under the MIT license.

For the full copyright and license information, please view the [`LICENSE`](LICENSE) file distributed with this source code.
