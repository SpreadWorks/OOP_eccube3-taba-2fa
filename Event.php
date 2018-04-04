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

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

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
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 2段階認証状態確認 イベント
     *
     * @param $event
     */
    public function onEventAdminController( $event)
    {
        $this->app['eccube.plugin.taba-2fa_auth.event']->onEventAdminController($event);
    }

    /**
     * メンバー削除場合2段階認証レコード削除 イベント
     *
     * @param $event
     */
    public function onAdminMemberDeleteInitialize( $event)
    {
        $this->app['eccube.plugin.taba-2fa_member_delete.event']->onAdminMemberDeleteInitialize($event);
    }

    /**
     * メニューアイコン用SVG 追加のためのイベント
     *
     * @param
     */
    public function onAdminNavRender(FilterResponseEvent $event)
    {   
        // CSVダウンロード時の対応
        // ・StreamedResponseオブジェクトは、setContentがLogicExceptionとなる
        // ・getContentがTrueの場合のみ実行
        //  　\vendor\symfony\http-foundation\StreamedResponse.php
        $response = $event->getResponse();
        if ($response->getContent()) {
            if (!isset($this->app['eccube.plugin.taba-secure.event.menu'])) {
                $this->app['eccube.plugin.taba-secure.event.menu'] = true;
                $addHtml = <<< EOT
<script>
    $(function() {
        var navElement = $(".nav-sidebar li .taba-secure").parent();
        navStr = navElement.html().replace("taba ","taba&trade; ");
        navElement.html(navStr);
        var svg = '<svg version="1.1" class="cb taba-secure" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">';
        svg += '<path fill="#89949B" d="M57.5,42.3c0-4.2-3.4-7.7-7.7-7.7s-7.7,3.4-7.7,7.7c0,0.5,0,0.9,0.1,1.4h15.1C57.4,43.3,57.5,42.8,57.5,42.3z"/>';
        svg += '<path fill="#89949B" d="M49.8,0.7l-42,13.1V53c0,0,3.6,14.7,11.3,26.8c7,11,30.7,19.5,30.7,19.5s22.8-8,29.8-17.8c8.6-12.1,12.2-26.4,12.2-26.4V13.8L49.8,0.7z M68.8,71.2H30.9V43.7h5.5c0-0.4-0.1-0.9-0.1-1.4c0-7.5,6-13.5,13.5-13.5s13.5,6,13.5,13.5c0,0.5,0,0.9-0.1,1.4h5.5V71.2z"/>';
        svg += '<path fill="#89949B" d="M54.9,60.9h-3.5v-3.6c1.9-0.7,3.2-2.5,3.2-4.7c0-2.8-2.2-5-5-5s-5,2.2-5,5c0,2.1,1.3,3.9,3.2,4.7v9.4h3.5v-2.2h3.5V60.9z"/>';
        svg += '</svg>';
        $(".nav-sidebar li .taba-secure").html(svg);
    });
</script>
</html>
EOT;
                $response->setContent(str_replace("</html>",$addHtml,$response->getContent()));
            }
        }
    }

}
