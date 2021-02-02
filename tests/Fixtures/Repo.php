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

use OpxCore\Config\Interfaces\ConfigRepositoryInterface;

class Repo implements ConfigRepositoryInterface
{
    public array $config = [
        'app' => [
            'name' => 'test',
        ]
    ];

    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
        }
    }

    /**
     * @inheritDoc
     */
    public function load(array &$config, $profile = null, $overrides = null): bool
    {
        $config = $this->config;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(array $config, $profile = null): bool
    {
        // TODO: Implement save() method.
    }
}