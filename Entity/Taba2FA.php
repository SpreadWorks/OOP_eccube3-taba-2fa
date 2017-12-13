<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Taba2FA\Entity;

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Taba2FA.
 */
class Taba2FA extends AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        $this->getName();
    }

    private $member_id;

    private $auth_key;

    private $enable_flg;

    private $create_date;

    public function setId($id)
    {
        $this->member_id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->member_id;
    }

    public function setAuthKey($auth_key)
    {
        $this->auth_key = $auth_key;

        return $this;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function setEnableFlg($enable_flg)
    {
        $this->enable_flg = $enable_flg;

        return $this;
    }

    public function getEnableFlg()
    {
        return $this->enable_flg;
    }

    public function isEnable()
    {
        if ($this->enable_flg === 1) {
            return true;
        }
        return false;
    }

    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }
}
