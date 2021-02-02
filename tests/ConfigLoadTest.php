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
use OpxCore\Tests\Config\Fixtures\Cache;
use OpxCore\Tests\Config\Fixtures\Repo;
use OpxCore\Tests\Config\Fixtures\Env;
use PHPUnit\Framework\TestCase;

class ConfigLoadTest extends TestCase
{
    public function testRepo(): void
    {
        $config = new Config(new Repo());
        $loaded = $config->load();

        self::assertTrue($loaded);
        self::assertEquals('test', $config['app.name']);
        self::assertEquals('test', $config->get('app.name'));
    }

    /**
     * Cache is ok, load from cache
     */
    public function testCacheOk(): void
    {
        $env = new Env();
        // $env->set('CONFIG_CACHE_ENABLE', true); --- set by default
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new Cache();
        $cache->config = ['app' => ['name' => 'cached']];
        $cache->expired = false;

        $config = new Config($repo, $cache, $env);

        self::assertTrue($config->load());
        self::assertEquals('cached', $config['app.name']);
        self::assertEquals('cached', $config->get('app.name'));
    }

    /**
     * Cache is ok, no env, load from cache
     */
    public function testCacheOkNoEnv(): void
    {
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new Cache();
        $cache->config = ['app' => ['name' => 'cached']];
        $cache->expired = false;

        $config = new Config($repo, $cache,);

        self::assertTrue($config->load());
        self::assertEquals('cached', $config['app.name']);
        self::assertEquals('cached', $config->get('app.name'));
    }

    /**
     * Cache disabled by env, load from repo
     */
    public function testRepoEnvDisabled(): void
    {
        $env = new Env();
        $env->set('CONFIG_CACHE_ENABLE', false);
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new Cache();
        $cache->config = ['app' => ['name' => 'cached']];
        $cache->expired = false;

        $config = new Config($repo, $cache, $env);

        self::assertTrue($config->load());
        self::assertEquals('test', $config['app.name']);
        self::assertEquals('test', $config->get('app.name'));
    }

    /**
     * Cache expired, load from repo, store to cache
     */
    public function testRepoCacheExpired(): void
    {
        $env = new Env();
        $env->set('CONFIG_CACHE_ENABLE', true);
        $env->set('CONFIG_CACHE_TTL', 60);
        $repo = new Repo(['app' => ['name' => 'test']]);
        $cache = new Cache();
        $cache->config = ['app' => ['name' => 'cached']];
        $cache->expired = true;

        $config = new Config($repo, $cache, $env);

        self::assertTrue($config->load('profile'));

        self::assertEquals('test', $config['app.name']);
        self::assertEquals('test', $config->get('app.name'));
        self::assertEquals(['app' => ['name' => 'test']], $cache->config);
        self::assertEquals('profile', $cache->profile);
        self::assertEquals(60, $cache->ttl);
    }
}
