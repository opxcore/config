<?php

namespace OpxCore\Config;

use OpxCore\Arr\Arr;

class Config implements \OpxCore\Interfaces\ConfigInterface, \ArrayAccess
{
    /**
     * All configurations.
     *
     * @var  array
     */
    protected $config = [];

    /**
     * Config constructor.
     *
     * @param  array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
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
     * @param  array|string $key
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
    public function offsetSet($key, $value):void
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