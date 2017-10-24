<?php
/*
  * This file is part of the TwoFactorAuthentication plugin
  *
  * Copyright (C) SPREAD WORKS CO.,LTD. All Rights Reserved.
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
        log_info("---------------------------2段階認証状態を確認");
        // メンバー情報取得
        $Member = $this->app->user();
        
        // 2段階認証Entity
        $Member2FA = $this->app['eccube.repository.tabasecure-2fa']->find($Member->getId());

        // 2段階認証の設定を確認
        if ($Member2FA && $Member2FA->isEnable()) {
            log_info("---------------------------2段階認証設定 済");

            $date = new \DateTime();
            $timestamp = $date->format('U');

            if (!$this->app['session']->get('eccube.admin.plugin.twofactorauthentication_lastauth_at')
            || $this->app['session']->get('eccube.admin.plugin.twofactorauthentication_lastauth_at') < $timestamp) {
                log_info("---------------------------2段階認証認証状態 Error");
                $request = $event->getRequest();
                $route = $request->attributes->get('_route');
                if ($route != "admin_plugin_tabasecure-2fa_auth")  {
                    log_info("---------------------------2段階認証画面出ない場合は、認証画面へ遷移します");
                    header('Location: '.$this->app->url('admin_plugin_tabasecure-2fa_auth'));
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
        
        log_info("--------------------------- onAdminMemberDeleteInitialize START");

        $TargetMember = $event->getArgument('TargetMember');
        $Member2FA = $this->app['eccube.repository.tabasecure-2fa']->find($TargetMember->getId());
        if ($Member2FA) {
            $this->app['orm.em']->remove($Member2FA);
            return;
        }
        return;
    }
}
