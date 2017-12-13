<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
  * 認証キー入力用のフォーム
  */

namespace Plugin\Taba2FA\Form\Type;

use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Taba2FAFormType
 */
class Taba2FAFormType extends AbstractType
{


    /**
     * buildForm.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'check_one_code',
                'text',
                array(
                    'label' => 'デバイストークン',
                    'required' => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => 6,
                            'min' => 6,
                        )),
                    ),
                    'attr' => array(
                        'maxlength' => 6,
                    ),
                )
            );
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return 'tabasecure_2fa_formtype';
    }
}
