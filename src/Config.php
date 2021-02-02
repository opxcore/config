<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Config;

use ArrayAccess;
use OpxCore\Arr\Arr;
use OpxCore\Config\Interfaces\ConfigInterface;
use OpxCore\Config\Interfaces\ConfigRepositoryInterface;
use OpxCore\Config\Interfaces\ConfigCacheInterface;
use OpxCore\Config\Interfaces\EnvironmentInterface;

class Config implements ConfigInterface, ArrayAccess
{
    /**
     * Config repository driver.
     *
     * @var ConfigRepositoryInterface|null
     */
    protected ?ConfigRepositoryInterface $configRepository;

    /**
     * Config cache repository driver.
     *
     * @var ConfigCacheInterface|null
     */
    protected ?ConfigCacheInterface $cacheRepository;

    /**
     * Environment driver.
     *
     * @var EnvironmentInterface|null
     */
    protected ?EnvironmentInterface $environment;

    /**
     * All configurations.
     *
     * @var  array
     */
    protected array $config = [];

    /**
     * Is config loaded from cache.
     *
     * @var  bool
     */
    protected bool $cached = false;

    /**
     * Config constructor.
     *
     * @param ConfigRepositoryInterface|null $repository
     * @param ConfigCacheInterface|null $cache
     * @param EnvironmentInterface|null $environment
     */
    public function __construct(?ConfigRepositoryInterface $repository = null, ?ConfigCacheInterface $cache = null, ?EnvironmentInterface $environment = null)
    {
        $this->configRepository = $repository;
        $this->cacheRepository = $cache;
        $this->environment = $environment;
    }

    /**
     * Load configuration.
     *
     * @param string|null $profile
     * @param string|null $overrides
     * @param bool $force Set to true to skip loading from cache
     *
     * @return  bool
     */
    public function load(?string $profile = null, ?string $overrides = null, bool $force = false): bool
    {
        $cacheEnabled = true;

        // Check if cache for config is not disabled by environment.
        if ($this->environment !== null) {
            $cacheEnabled = $this->environment->get('CONFIG_CACHE_ENABLE', true) === true;
        }

        // Try to load config from cache first if force option is not set, cache is not disabled by environment and
        // config cache was bound.
        if (!$force && $cacheEnabled && isset($this->cacheRepository)) {

            $this->cached = $this->cacheRepository->load($this->config, $profile);
        }

        // If config was loaded from cache successfully no need to do anything else.
        if ($this->cached || !isset($this->configRepository)) {
            return $this->cached;
        }

        // If there is no cached and not expired config load it via config repository.
        $loaded = $this->configRepository->load($this->config, $profile, $overrides);

        // Conditionally make cache for config
        if ($loaded && $cacheEnabled && isset($this->cacheRepository)) {
            // Get TTL from environment or use null (forever)
            $ttl = $this->environment->get('CONFIG_CACHE_TTL');
            $this->cacheRepository->save($this->config, $profile, $ttl);
        }

        return $loaded;
    }

    /**
     * Determine if the value for given key exists.
     *
     * @param string $key
     *
     * @return  bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->config, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param array|string|null $key
     * @param mixed $value
     *
     * @return  void
     */
    public function set($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $localKey => $localValue) {
            Arr::set($this->config, $localKey, $localValue);
        }
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return  void
     */
    public function push(string $key, $value): void
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return  array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param $offset
     *
     * @return  bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get a configuration option.
     *
     * @param $offset
     *
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set a configuration option.
     *
     * @param $offset
     * @param mixed $value
     *
     * @return  void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param $offset
     *
     * @return  void
     */
    public function offsetUnset($offset): void
    {
        Arr::forget($this->config, $offset);
    }
}