<?php

namespace OpxCore\Config;

use OpxCore\Arr\Arr;
use OpxCore\Interfaces\ConfigCacheRepositoryInterface;
use OpxCore\Interfaces\ConfigRepositoryInterface;

class Config implements \OpxCore\Interfaces\ConfigInterface, \ArrayAccess
{
    /**
     * Config repository.
     *
     * @var  ConfigRepositoryInterface|null
     */
    protected $configRepository;

    /**
     * Config cache repository.
     *
     * @var  ConfigCacheRepositoryInterface
     */
    protected $cacheRepository;

    /**
     * All configurations.
     *
     * @var  array
     */
    protected $config = [];

    /**
     * Is config loaded from cache.
     *
     * @var  bool
     */
    protected $cached = false;

    /**
     * Config constructor.
     *
     * @param  ConfigRepositoryInterface|null $repository
     * @param  ConfigCacheRepositoryInterface|null $cacheRepository
     */
    public function __construct(ConfigRepositoryInterface $repository = null, ConfigCacheRepositoryInterface $cacheRepository = null)
    {
        $this->configRepository = $repository;
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * Load configuration.
     *
     * @param  string|null $profile
     * @param  bool $force
     *
     * @return  bool
     */
    public function load($profile = null, $force = false): bool
    {
        $cacheEnabled = env('CONFIG_CACHE_DISABLE', false) === false;

        // Try to load config from cache first if this option is enabled and driver
        // for config cache was bind.
        if (!$force && $cacheEnabled && isset($this->cacheRepository)) {

            $this->cached = $this->cacheRepository->load($this->config, $profile);
        }

        if($this->cached || !isset($this->configRepository)) {
            return $this->cached;
        }

        $loaded = $this->configRepository->load($this->config, $profile);

        // Conditionally make cache for config
        if ($loaded && $cacheEnabled && isset($this->cacheRepository)) {
            $this->cacheRepository->save($this->config, $profile);
        }

        return $loaded;
    }

    /**
     * Determine if the value for given key exists.
     *
     * @param  string $key
     *
     * @return  bool
     */
    public function has($key): bool
    {
        return Arr::has($this->config, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed $default
     *
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string|null $key
     * @param  mixed $value
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
     * @param  string $key
     * @param  mixed $value
     *
     * @return  void
     */
    public function push($key, $value): void
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
     * @param  string $key
     *
     * @return  bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     *
     * @return  mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return  void
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     *
     * @return  void
     */
    public function offsetUnset($key): void
    {
        Arr::forget($this->config, $key);
    }
}