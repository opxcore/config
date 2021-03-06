<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Tests\Config\Fixtures;

use OpxCore\Config\Exceptions\ConfigCacheException;
use OpxCore\Config\Interfaces\ConfigCacheInterface;

class CacheLoadException implements ConfigCacheInterface
{
    public array $config = [];

    public bool $expired = false;
    public ?string $profile = null;
    public ?int $ttl = null;

    public function load(array &$config, $profile = null): bool
    {
        throw new ConfigCacheException('Load rejected');
    }

    public function save(array $config, $profile = null, $ttl = null): bool
    {
        $this->config = $config;
        $this->profile = $profile;
        $this->ttl = $ttl;

        return true;
    }
}