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
use Eccube\Util\EntityUtil;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Taba2FAMember.
 */
class Taba2FAMember extends AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @var \Plugin\Taba2FA\Entity\Taba2FA
     */
    private $TwoFactorAuthentication;

    /**
     * Set TwoFactorAuthentication
     *
     * @param  \Plugin\Taba2FA\Entity\Taba2FA $TwoFactorAuthentication
     * @return Member
     */
    public function setTwoFactorAuthentication(\Plugin\Taba2FA\Entity\Taba2FA $TwoFactorAuthentication)
    {
        $this->TwoFactorAuthentication = $TwoFactorAuthentication;

        return $this;
    }

    /**
     * Get TwoFactorAuthentication
     *
     * @return \Plugin\Taba2FA\Entity\Taba2FA
     */
    public function getTwoFactorAuthentication()
    {
        if (EntityUtil::isEmpty($this->TwoFactorAuthentication)){
            return false;
        }
        return $this->TwoFactorAuthentication;
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $department;

    /**
     * @var \Eccube\Entity\Master\Authority
     */
    private $Authority;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \Eccube\Entity\Master\Work
     */
    private $Work;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Member
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set department
     *
     * @param  string $department
     * @return Member
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set authority
     *
     * @param \Eccube\Entity\Master\Authority $authority
     * @return Member
     */
    public function setAuthority(\Eccube\Entity\Master\Authority $Authority = null)
    {
        $this->Authority = $Authority;

        return $this;
    }

    /**
     * Get authority
     *
     * @return \Eccube\Entity\Master\Authority
     */
    public function getAuthority()
    {
        return $this->Authority;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return Member
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set work
     *
     * @param  \Eccube\Entity\Master\Work $Work
     * @return Member
     */
    public function setWork(\Eccube\Entity\Master\Work $Work)
    {
        $this->Work = $Work;

        return $this;
    }

    /**
     * Get work
     *
     * @return \Eccube\Entity\Master\Work
     */
    public function getWork()
    {
        return $this->Work;
    }

    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Member
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return boolean
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }


}
