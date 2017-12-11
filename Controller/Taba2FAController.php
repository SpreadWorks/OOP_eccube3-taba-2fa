<?php
/*
  * This file is part of the TwoFactorAuthentication plugin
  *
  * Copyright (C) SPREAD WORKS CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\Taba2FA\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use RobThree\Auth\TwoFactorAuth;

class Taba2FAController extends AbstractController
{

    public function auth(Application $app, Request $request, $id = null)
    {
        // フォームビルド
        $builder = $app['form.factory']->createBuilder('tabasecure_2fa_formtype');
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $tfa = new TwoFactorAuth();

                // メンバー情報取得
                $Member = $app->user();

                // 2段階認証Entity
                $Member2FA = $app['eccube.repository.taba-2fa']->find($Member->getId());

                if ($Member2FA) {
                    if ($tfa->verifyCode($Member2FA->getAuthKey(), $form->get('check_one_code')->getData(),2)) {
                        $date = new \DateTime();
                        $date->modify('+30 days');
                        $timestamp = $date->format('U');
                        $app['session']->set('eccube.admin.plugin.twofactorauthentication_lastauth_at', $timestamp);
                        $app->addSuccess('2段階認証に成功しました。' , 'admin');
                        return $app->redirect($app->url('admin_homepage'));
                    } else {
                        $app->addERROR('2段階認証に失敗しました。' , 'admin');
                    }
                } else {
                    $app->addERROR('2段階認証の登録がありません。' , 'admin');
                }
            } else {
                $app->addERROR('2段階認証コードに誤りがあります。' , 'admin');
            }
        }

        return $app->render(
                'Taba2FA/Resource/template/admin/authentication.twig',
                array(
                    'error' => $app['security.last_error']($request),
                    'form' => $form->createView(),
                    )
            );
    }


    public function member(Application $app, Request $request, $id = null)
    {

        $Members = $app['eccube.repository.taba-2famember']->findBy(array(), array('rank' => 'DESC'));

        $builder = $app['form.factory']->createBuilder();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Members' => $Members,
            ),
            $request
        );

        $form = $builder->getForm();

        return $app->render(
            'Taba2FA/Resource/template/admin/member.twig',
            array(
                'form' => $form->createView(),
                'Members' => $Members,
                )
        );
    }


    public function regist(Application $app, Request $request, $id = null)
    {
        // メンバーのデータ取得
        if ($id) {
            $Member = $app['eccube.repository.member']->find($id);
            if (!$Member) {
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }

        // シークレットキー
        $secret = null;

        // 2段階認証
        $tfa = new TwoFactorAuth();

        // フォームビルド
        $builder = $app['form.factory']->createBuilder('tabasecure_2fa_formtype');
        $builder->add(
            'auth_key',
            'hidden',
            array(
                'label' => '認証キー',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => 16,
                        'min' => 16,
                    )),
                ),
                'attr' => array(
                    'maxlength' => 16,
                ),
            )
        );

        if ('GET' === $request->getMethod()) {
            // シークレットキー生成
            $secret = $tfa->createSecret();

            // シークレットキーをフォームにセット
            $builder->get('auth_key')->setData($secret);
        }

        // フォーム
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $checkResult = $tfa->verifyCode($form->get('auth_key')->getData(), $form->get('check_one_code')->getData(), 2);    // 2 = 2*30sec clock tolerance
                if ($checkResult) {
                    // 2段階認証Entity 更新
                    $Member2FA = $app['eccube.repository.taba-2fa']->find($id);
                    if (!$Member2FA) {
                        $Member2FA = new \Plugin\Taba2FA\Entity\Taba2FA();
                        $Member2FA->setId($id);
                    }
                    $app['orm.em']->getConnection()->beginTransaction();
                    try {
                        $Member2FA->setAuthKey($form->get('auth_key')->getData());
                        $Member2FA->setEnableFlg(1);
                        $app['orm.em']->persist($Member2FA);
                        $app['orm.em']->flush($Member2FA);
                        $app['orm.em']->getConnection()->commit();
                        $app->addSuccess(' 2段階認証のデバイスが登録されました。', 'admin');
                        return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
                    } catch (\Exception $e) {
                        $app['orm.em']->getConnection()->rollback();
                        $app->addERROR('デバイスが登録できませんでした。' , 'admin');
                    }
                } else {
                    $app->addERROR('デバイストークンに誤りがあります。' , 'admin');
                }
            } else {
                $app->addERROR('デバイストークンに誤りがあります。数字6桁でご入力ください。' , 'admin');
            }
        }

        return $app->render(
            'Taba2FA/Resource/template/admin/regist.twig',
                array(
                        'form' => $form->createView(),
                        'Member' => $Member,
                        'TwoFactorAuthentication' => array(
                            'Secret' => $secret
                        )
                    )
            );
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Member2FA = $app['eccube.repository.taba-2fa']->find($id);
        if (!$Member2FA) {
            $app->addSuccess('メンバーが確認できないため、削除できませんでした。', 'admin');
            return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
        }

        $app['orm.em']->getConnection()->beginTransaction();
        try {
            $app['orm.em']->remove($Member2FA);
            $app['orm.em']->flush($Member2FA);
            $app['orm.em']->getConnection()->commit();
            $app->addSuccess('デバイスの登録を削除しました。', 'admin');
        } catch (\Exception $e) {
            $app['orm.em']->getConnection()->rollback();
            $app->addError('削除できませんでした。', 'admin');
        }
        return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
    }

    public function enable(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Member2FA = $app['eccube.repository.taba-2fa']->find($id);
        if (!$Member2FA) {
            $app->addSuccess('メンバーが確認できないため、有効にできませんでした。', 'admin');
            return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
        }

        $app['orm.em']->getConnection()->beginTransaction();
        try {
            $Member2FA->setEnableFlg(1);
            $app['orm.em']->persist($Member2FA);
            $app['orm.em']->flush($Member2FA);
            $app['orm.em']->getConnection()->commit();
            $app->addSuccess('2段階認証を有効にしました。', 'admin');
        } catch (\Exception $e) {
            $app['orm.em']->getConnection()->rollback();
            $app->addError('有効にできませんでした。', 'admin');
        }

        return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
    }

    public function disable(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Member2FA = $app['eccube.repository.taba-2fa']->find($id);
        if (!$Member2FA) {
            $app->addSuccess('メンバーが確認できないため、無効にできませんでした。', 'admin');
            return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
        }

        $app['orm.em']->getConnection()->beginTransaction();
        try {
            $Member2FA->setEnableFlg(0);
            $app['orm.em']->persist($Member2FA);
            $app['orm.em']->flush($Member2FA);
            $app['orm.em']->getConnection()->commit();
            $app->addSuccess('2段階認証を無効にしました。', 'admin');
        } catch (\Exception $e) {
            $app['orm.em']->getConnection()->rollback();
            $app->addError('無効にできませんでした。', 'admin');
        }

        return $app->redirect($app->url('admin_plugin_taba-2fa_member'));
    }


    public function jquery_qrcode_min(Application $app, Request $request, $id = null)
    {
        $response =  $app->render('Taba2FA/Resource/assets/admin/js/jquery-qrcode-0.14.0.min.js.twig', array());
        $response->headers->set('Content-Type', 'text/javascript');
        return $response;
    }

    public function img_app_appstore(Application $app, Request $request, $id = null)
    {
        $response =  $app->render('Taba2FA/Resource/assets/admin/img/app_appstore.svg', array());
        $response->headers->set('Content-Type', 'image/svg+xml');
        return $response;
    }

    public function img_app_googleplay(Application $app, Request $request, $id = null)
    {
        $response =  $app->render('Taba2FA/Resource/assets/admin/img/app_googleplay.svg', array());
        $response->headers->set('Content-Type', 'image/svg+xml');
        return $response;
    }
}