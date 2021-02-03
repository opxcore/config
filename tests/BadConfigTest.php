<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Tests\Config;

use OpxCore\Config\Config;
use OpxCore\Config\Exceptions\ConfigException;
use OpxCore\Tests\Config\Fixtures\BadRepo;
use OpxCore\Tests\Config\Fixtures\CacheLoadException;
use OpxCore\Tests\Config\Fixtures\CacheSaveException;
use OpxCore\Tests\Config\Fixtures\Repo;
use PHPUnit\Framework\TestCase;

class BadConfigTest extends TestCase
{
    public function testBadRepo(): void
    {
        $config = new Config(new BadRepo());

        $this->expectException(ConfigException::class);

        $config->load();
    }

    public function testCacheReadError(): void
    {
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new CacheLoadException();
        $config = new Config($repo, $cache);

        $this->expectException(ConfigException::class);
        $config->load();
    }
    public function testCacheWriteError(): void
    {
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new CacheSaveException();
        $config = new Config($repo, $cache);

        $this->expectException(ConfigException::class);
        $config->load();
    }
}
