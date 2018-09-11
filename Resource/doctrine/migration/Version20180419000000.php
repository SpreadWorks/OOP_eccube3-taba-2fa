<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180419000000 extends AbstractMigration
{

    const NAME = 'plg_taba_2fa_auth_key';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable(self::NAME)) {
            return true;
        }
        $table = $schema->getTable(self::NAME);
        $table->setPrimaryKey(array('member_id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}