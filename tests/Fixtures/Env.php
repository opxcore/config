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

use OpxCore\Config\Interfaces\EnvironmentInterface;

class Env implements EnvironmentInterface
{
    public array $options = [];

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function set(string $key, $value, bool $safe = false): bool
    {
        $this->options[$key] = $value;
        return true;
    }

    public function has(string $key): bool
    {
        // TODO: Implement has() method.
    }

    public function unset(string $key): void
    {
        // TODO: Implement unset() method.
    }

    public static function getEnvironment(): array
    {
        // TODO: Implement getEnvironment() method.
    }
}