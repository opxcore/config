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

use Error;
use Exception;
use OpxCore\Config\Exceptions\ConfigRepositoryException;
use OpxCore\Config\Interfaces\ConfigRepositoryInterface;

class BadRepo implements ConfigRepositoryInterface
{
    /**
     * @inheritDoc
     * @throws ConfigRepositoryException
     */
    public function load(array &$config, $profile = null, $overrides = null): bool
    {
        try {
            $config = require __DIR__ . DIRECTORY_SEPARATOR . 'bad.php';
        } catch (Exception | Error $e) {
            throw new ConfigRepositoryException("Error reading configuration file {$e->getFile()}:{$e->getLine()} {$e->getMessage()}");
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(array $config, $profile = null): bool
    {
        return true;
    }
}