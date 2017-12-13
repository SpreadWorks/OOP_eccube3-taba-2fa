<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Taba2FA;

use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    public function install($config, $app)
    {
    }

    public function uninstall($config, $app)
    {
      $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    public function enable($config, $app)
    {
      $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }

    public function disable($config, $app)
    {
    }

    public function update($config, $app)
    {
      $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }
}
