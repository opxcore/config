[![Build Status](https://travis-ci.com/opxcore/config.svg?branch=main)](https://travis-ci.com/opxcore/config)
[![Coverage Status](https://coveralls.io/repos/github/opxcore/config/badge.svg)](https://coveralls.io/github/opxcore/config)
[![Latest Stable Version](https://poser.pugx.org/opxcore/config/v/stable)](https://packagist.org/packages/opxcore/config)
[![Total Downloads](https://poser.pugx.org/opxcore/config/downloads)](https://packagist.org/packages/opxcore/config)
[![License](https://poser.pugx.org/opxcore/config/license)](https://packagist.org/packages/opxcore/config)

# Config

Config is a component designed to load config for your project. It uses three components as dependency injection: config
repository loader, config caching and environment variables. All of them set to config as dependency injection. In
general, you can use standard components operating on local files. Also, you can make your own components for your
purposes. Each of they must implement teh corresponding interface (see below).

Of course, these three components are optional. If component is not set, functionality it provides would not be able.

## Creating

```php
$config = new \OpxCore\Config\Config($repository, $cache, $environment);
```

### Config repository

Config repository provides functionality for loading configuration. This component must
implement [ConfigRepositoryInterface](https://github.com/opxcore/config-repository-interface).

Realization: [ConfigRepositoryFiles](https://github.com/opxcore/config-repository-files)

### Config cache

This component provides functionality for configuration caching. This component must
implement [ConfigCacheInterface](https://github.com/opxcore/config-cache-interface).

Realization: [ConfigCacheFiles](https://github.com/opxcore/config-cache-files)

### Environment

This component provides functionality for defining configuration values via environment files. This component must
implement [EnvironmentInterface](https://github.com/opxcore/config-environment-interface).

Realization: [Environment](https://github.com/opxcore/config-environment)

In case of environment is assigned to config, last one can use environment variables to configure cache driver.

```dotenv
# Is config caching disabled. true of false
CONFIG_CACHE_ENABLE=true
# Cache lifetime in seconds. null for forever
CONFIG_CACHE_TTL=null
```

## Loading configuration

```phpt
$config->load($profile, $overrides, $force);
```

`$profile` and `$overrides` are variables passed to config repository (
see [repository](https://github.com/opxcore/config-repository-files)) for more information.

If `$forse` is set to `true`, loading configuration from cache will bi skipped, as cached configuration was not found.

## Accessing configuration

Config implements `ArrayAccess` interface, so you can use `$config` as an array.

Accessing to configuration is realized with [Array](https://github.com/opxcore/array) so all operations with keys uses
dot notation.

`$config->has($key);` same as `isset($config[$key]);` checks if value is defined;

`$config->get($key, $default);` same sa `$config[$key];` gets value;

`$config->set($key, $value);` same as `$config[$key] = $value;` sets value;

`$config->push($key, $value);` pushes value;

`$config->all();` returns all configuration array.

