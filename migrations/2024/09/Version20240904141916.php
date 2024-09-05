<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240904141916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update of additional fees';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee ADD child_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', ADD validity_from DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8DD62C21B FOREIGN KEY (child_id) REFERENCES additional_fee (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee (child_id)');
        $this->addSql('ALTER TABLE payment_record CHANGE income_id income_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8DD62C21B');
        $this->addSql('DROP INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee');
        $this->addSql('ALTER TABLE additional_fee DROP child_id, DROP validity_from');
        $this->addSql('ALTER TABLE payment_record CHANGE income_id income_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\'');
    }
}
