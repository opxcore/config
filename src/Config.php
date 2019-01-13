<?php

namespace OpxCore\Config;

use OpxCore\Config\Interfaces\Config as ConfigInterface;
use Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;
use OpxCore\Arr\Arr;

class Config implements ConfigInterface, \ArrayAccess
{
    /**
     * All configurations.
     *
     * @var  array
     */
    private $config = [];

    /**
     * Path to environment file.
     *
     * @var  string|array
     */
    private $envPaths;

    /**
     * Path to config files.
     *
     * @var  string
     */
    private $configPath;

    /**
     * Path to config cache file.
     *
     * @var  string
     */
    private $configCachePath;

    /**
     * Config cache file name.
     *
     * @var  string
     */
    private $configCacheFilename = 'config.cache';

    /**
     * Environment file name.
     *
     * @var  string
     */
    private $envFilename = '.env';

    /**
     * Flag to determine if configuration was loaded from cache.
     *
     * @var  bool
     */
    private $loadedFromCache = false;

    /**
     * Config constructor.
     *
     * @param  string|array $envPaths
     * @param  string $configPath
     * @param  string $configCachePath
     * @param  string|null $envFilename
     * @param  string|null $configCacheFilename
     */
    public function __construct(
        $envPaths,
        $configPath,
        $configCachePath,
        $envFilename = null,
        $configCacheFilename = null
    )
    {
        $this->envPaths = $envPaths;
        $this->configPath = $configPath;
        $this->configCachePath = $configCachePath;
        $this->envFilename = $envFilename ?? $this->envFilename;
        $this->configCacheFilename = $configCacheFilename ?? $this->configCacheFilename;

        if (!$this->loadFromCache()) {

            $this->loadEnv();

            $this->loadConfigurationFiles();
        }
    }

    /**
     * Try to load configuration from cache.
     *
     * @return  bool
     */
    private function loadFromCache(): bool
    {
        $filename = $this->configCachePath . DIRECTORY_SEPARATOR . $this->configCacheFilename;

        if (!file_exists($filename)) {

            return false;
        }

        $serialized = file_get_contents($filename);

        $this->config = unserialize($serialized, ['allowed_classes' => false]);

        return $this->loadedFromCache = true;
    }

    /**
     * Load environments.
     *
     * @return  void
     */
    private function loadEnv(): void
    {
        $environment = Dotenv::create($this->envPaths, $this->envFilename);

        $environment->load();
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @return  void
     */
    private function loadConfigurationFiles(): void
    {
        $files = $this->getConfigurationFiles();

        foreach ($files as $key => $path) {
            $this->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files from config directory.
     *
     * @return  array
     */
    protected function getConfigurationFiles(): array
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($this->configPath) as $file) {

            $directory = $this->getNestedDirectory($file, $this->configPath);

            /** @var \SplFileInfo $file */
            $realPath = $file->getRealPath();
            $files[$directory . basename($realPath, '.php')] = $realPath;
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo $file
     * @param  string $configPath
     *
     * @return  string
     */
    protected function getNestedDirectory(\SplFileInfo $file, $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
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
     * @param  array|string $key
     * @param  mixed $default
     *
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get many configuration values.
     *
     * @param  array $keys
     *
     * @return  array
     */
    public function getMany($keys): array
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->config, $key, $default);
        }

        return $config;
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
     * Prepend a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return  void
     */
    public function prepend($key, $value): void
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
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
        $this->set($key);
    }
}