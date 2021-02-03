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

class CacheSaveException implements ConfigCacheInterface
{
    public function load(array &$config, $profile = null): bool
    {
        return false;
    }

    public function save(array $config, $profile = null, $ttl = null): bool
    {
        throw new ConfigCacheException('Save rejected');
    }
}