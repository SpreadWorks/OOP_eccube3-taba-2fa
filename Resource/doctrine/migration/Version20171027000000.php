<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171027000000 extends AbstractMigration
{

    const NAME = 'plg_taba_2fa_auth_key';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($schema->hasTable(self::NAME)) {
            return true;
        }
        $table = $schema->createTable(self::NAME);

        $table->addColumn('member_id', 'integer', array(
            'notnull' => true,
            'length'=>255,
            'unique'=>true
        ));

        $table->addColumn('auth_key', 'text', array(
            'notnull' => false,
        ));

        $table->addColumn('enable_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => '1'
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
        ));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable(self::NAME)) {
            return true;
        }
        $schema->dropTable(self::NAME);
    }
}