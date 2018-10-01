<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Taba2FA\Event;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Yaml\Yaml;
use Plugin\Taba2FA\Common\UserConfig;

/**
 * Class TwoFactorAuthenticationEvent.
 */
class Event
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    /**
     * TwoFactorAuthenticationEvent constructor.
     *
     * @param object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 2段階認証画面を表示する
     *
     * @param $event
     */
    public function onEventAdminController($event)
    {
        // plugin_enable.ymlを利用し、無効化する機能 
        // ファイルがない場合（null）は、有効
        $file = $this->app['config']['plugin_realdir'] . DIRECTORY_SEPARATOR . 'Taba2FA' . DIRECTORY_SEPARATOR . 'user_config.yml';
        UserConfig::getInstance()->load($file);
        if (UserConfig::getInstance()->get("plugin_enable") === true) {
            // ファイルがある場合かつ、無効の場合は、処理を終了する。
            return;
        } 

        // ログイン済みの確認
        if (!$this->app->isGranted('ROLE_ADMIN')) { return; }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // 画像ファイルは認証チェックしません。
        if ($route == "admin_plugin_taba-2fa_img_product_logo"
            || $route == "admin_plugin_taba-2fa_img_taba_poweredby") {
            return;
        }

        // メンバー情報取得
        $Member = $this->app->user();

        // 2段階認証Entity
        $Member2FA = $this->app['eccube.repository.taba-2fa']->find($Member->getId());

        // 2段階認証の設定を確認
        if ($Member2FA && $Member2FA->isEnable()) {
            $date = new \DateTime();
            $timestamp = $date->format('U');
            if (!$this->app['session']->get('eccube.admin.plugin.twofactorauthentication_lastauth_at')
            || $this->app['session']->get('eccube.admin.plugin.twofactorauthentication_lastauth_at') < $timestamp) {
                if ($route != "admin_plugin_taba-2fa_auth")  {
                    header('Location: '.$this->app->url('admin_plugin_taba-2fa_auth'));
                    exit;
                }
            }
        }
    }

    /**
     * メンバー削除時に2段階認証レコードも削除する
     *
     * @param EventArgs $event
     */
    public function onAdminMemberDeleteInitialize(EventArgs $event)
    {
        $TargetMember = $event->getArgument('TargetMember');
        $Member2FA = $this->app['eccube.repository.taba-2fa']->find($TargetMember->getId());
        if ($Member2FA) {
            $this->app['orm.em']->remove($Member2FA);
            return;
        }
        return;
    }
}
