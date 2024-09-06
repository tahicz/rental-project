<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240905233824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8DD62C21B');
        $this->addSql('DROP INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee');
        $this->addSql('ALTER TABLE additional_fee CHANGE child_id parent_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8727ACA70 FOREIGN KEY (parent_id) REFERENCES additional_fee (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6C22BC8727ACA70 ON additional_fee (parent_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8727ACA70');
        $this->addSql('DROP INDEX UNIQ_B6C22BC8727ACA70 ON additional_fee');
        $this->addSql('ALTER TABLE additional_fee CHANGE parent_id child_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8DD62C21B FOREIGN KEY (child_id) REFERENCES additional_fee (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee (child_id)');
    }
}
