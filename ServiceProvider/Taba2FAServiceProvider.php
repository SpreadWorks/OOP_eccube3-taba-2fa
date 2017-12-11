<?php
/*
  * This file is part of the TwoFactorAuthentication plugin
  *
  * Copyright (C) SPREAD WORKS CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\Taba2FA\ServiceProvider;

use Eccube\Common\Constant;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;
use Plugin\Taba2FA\Event\Event;
use Plugin\Taba2FA\Form\Type\Taba2FAFormType;
use Plugin\Taba2FARepository\Taba2FAMemberRepository;

// include log functions (for 3.0.0 - 3.0.11)
require_once __DIR__.'/../log.php';

// composer autoload.php
require_once __DIR__.'/../vendor/autoload.php';


/**
 * Class Taba2FAServiceProvider.
 */
class Taba2FAServiceProvider implements ServiceProviderInterface
{
    /**
     * register.
     *
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        // Route 設定
        //  管理画面
        $admin = $app['controllers_factory'];
        //   強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $admin->requireHttps();
        }
        //   メンバー一覧
        $admin->match('/plugin/taba-secure/2fa/member', 'Plugin\Taba2FA\Controller\Taba2FAController::member')->bind('admin_plugin_tabasecure-2fa_member');
        //   登録ページ
        $admin->match('/plugin/taba-secure/2fa/regist/{id}', 'Plugin\Taba2FA\Controller\Taba2FAController::regist')->assert('id', '\d+')->bind('admin_plugin_tabasecure-2fa_regist');
        //   登録ページ QR生成JS
        $admin->match('/plugin/taba-secure/2fa/js/jquery-qrcode-min.js', 'Plugin\Taba2FA\Controller\Taba2FAController::jquery_qrcode_min')->bind('admin_plugin_tabasecure-2fa_jquery-qrcode-min');
        //   アプリバナー
        $admin->match('/plugin/taba-secure/2fa/img/app_appstore.svg', 'Plugin\Taba2FA\Controller\Taba2FAController::img_app_appstore')->bind('admin_plugin_tabasecure-2fa_img_app_appstore');
        $admin->match('/plugin/taba-secure/2fa/img/app_googleplay.svg', 'Plugin\Taba2FA\Controller\Taba2FAController::img_app_googleplay')->bind('admin_plugin_tabasecure-2fa_app_googleplay');
        //   削除
        $admin->match('/plugin/taba-secure/2fa/delete/{id}', 'Plugin\Taba2FA\Controller\Taba2FAController::delete')->assert('id', '\d+')->bind('admin_plugin_tabasecure-2fa_delete');
        //   有効
        $admin->match('/plugin/taba-secure/2fa/enable/{id}', 'Plugin\Taba2FA\Controller\Taba2FAController::enable')->assert('id', '\d+')->bind('admin_plugin_tabasecure-2fa_enable');
        //   無効
        $admin->match('/plugin/taba-secure/2fa/disable/{id}', 'Plugin\Taba2FA\Controller\Taba2FAController::disable')->assert('id', '\d+')->bind('admin_plugin_tabasecure-2fa_disable');

        //   認証ページ
        $admin->match('/plugin/taba-secure/2fa/auth', 'Plugin\Taba2FA\Controller\Taba2FAController::auth')->bind('admin_plugin_tabasecure-2fa_auth');
        //  管理画面のルーティング設定をappに設定
        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);


        // Event 設定
        $app['eccube.plugin.tabasecure-2fa_auth.event'] = $app->share(function () use ($app) {
            return new Event($app);
        });
        $app['eccube.plugin.tabasecure-2fa_member_delete.event'] = $app->share(function () use ($app) {
            return new Event($app);
        });

        // FormType 設定
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new Taba2FAFormType();
            return $types;
        }));

        // Repository 設定
        //  2段階認証
        $app['eccube.repository.tabasecure-2fa'] = $app->share(function () use ($app) {
            return $repository = $app['orm.em']->getRepository('Plugin\Taba2FA\Entity\Taba2FA');
        });
        //  メンバー (2段階認証用に拡張)
        //   エンティティの継承機能がマージされた場合に、継承したクラスを用意する予定（3.1でエンティティの継承）
        //   https://github.com/EC-CUBE/ec-cube/pull/1817
        $app['eccube.repository.tabasecure-2famember'] = $app->share(function () use ($app) {
            return $repository = $app['orm.em']->getRepository('Plugin\Taba2FA\Entity\Taba2FAMember');
        });

        // Menu 設定
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $parent = array('id' => 'tabasecure','name' => 'taba secure','icon' => 'taba-secure','has_child' => true,);

            $child = array(
                'id' => 'tabasecure-twofactorauthentication',
                'name' => '2段階認証',
                'url' => 'admin_plugin_tabasecure-2fa_member',
            );

            $insert_pos = 0;
            foreach ($config['nav'] as $key => &$val) {
                if ($val['id'] == 'setting') {
                    $insert_pos = $key;
                } else if ($val['id'] == 'tabasecure') {
                    $insert_pos = 0;
                    $val['child'][] = $child;
                    break;
                }
            }
            if ($insert_pos) {
                $parent['child'][] = $child;
                array_splice($config['nav'],$insert_pos,0,array($parent));
            }

            return $config;
        }));

    }

    /**
     * boot.
     *
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }
}
