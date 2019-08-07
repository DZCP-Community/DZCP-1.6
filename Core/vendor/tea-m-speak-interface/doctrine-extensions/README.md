# doctrine-extensions for PHP 7.1+

## Webmasters Doctrine Extensions for Tea(m)Speak Interface

Just Another Doctrine2 Extension

### Bootstrap

```php
<?php
// Use Composer autoloading
require_once 'vendor/autoload.php';

$configuration = new Webmasters\Doctrine\Configuration();

// MySQL database configuration
$configuration->getConnectionOptions([
    'default' => [
        'driver' => 'pdo_mysql',
        'dbname' => 'example_db',
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'prefix' => '',
    ],
]);

// Application/Doctrine configuration
$configuration->setDebugMode(true);
$configuration->setAutogenerateProxyClasses(true);
$configuration->setProxyDir("data/proxy");
$configuration->setResultCacheImpl(_CACHE_DRIVER_);
.....

// Custom configuration for Application
$configuration->setCustomConfigArray(['test'=> true, 'text' => 'hello']);
$configuration->getCustomConfig()->has('test');
echo $configuration->getCustomConfig()->get('text'); //hello


// Get Doctrine entity manager
$bootstrap = Webmasters\Doctrine\Bootstrap::getInstance();

$em = $bootstrap->getEntityManager();

echo $bootstrap->getConfiguration()->getCustomConfig()->get('text'); //hello

$bootstrap->getConfiguration()->getCustomConfig()->set('world',true);

echo $bootstrap->getConfiguration()->getCustomConfig()->get('world'); //(bool)true

```

### Idea
[Jan Teriete](https://twitter.com/jteriete)