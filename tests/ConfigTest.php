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
use OpxCore\Tests\Config\Fixtures\Repo;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testRepo(): void
    {
        $config = new Config(new Repo([
            'app' => [
                'name' => 'test',
                'mode' => 'testing',
            ],
            'stack' => [
                'entry_1',
                'entry_2',
            ]
        ]));

        $loaded = $config->load();

        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'test',
                'mode' => 'testing',
            ],
            'stack' => [
                'entry_1',
                'entry_2',
            ]
        ], $config->all());

        $config->push('stack', 'entry_3');
        self::assertEquals(['entry_1', 'entry_2', 'entry_3'], $config['stack']);

        unset($config['app.mode']);
        self::assertFalse(isset($config['app.mode']));

        $config['app.mode'] = 'testing';
        self::assertEquals('testing', $config['app.mode']);
    }
}
