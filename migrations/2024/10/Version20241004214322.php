<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241004214322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE additional_fee_payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', additional_fee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION NOT NULL, validity_from DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', validity_to DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', note LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BE3E169FFAE75C8B (additional_fee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE additional_fee_payment ADD CONSTRAINT FK_BE3E169FFAE75C8B FOREIGN KEY (additional_fee_id) REFERENCES additional_fee (id)');
        $this->addSql('ALTER TABLE overpayment DROP FOREIGN KEY FK_5B392FB8ED45A24');
        $this->addSql('DROP TABLE overpayment');
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8DD62C21B');
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8727ACA70');
        $this->addSql('DROP INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee');
        $this->addSql('DROP INDEX UNIQ_B6C22BC8727ACA70 ON additional_fee');
        $this->addSql('ALTER TABLE additional_fee DROP parent_id, DROP child_id, DROP fee_amount, DROP validity_from, DROP validity_to');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE overpayment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', payment_record_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5B392FB8ED45A24 (payment_record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE overpayment ADD CONSTRAINT FK_5B392FB8ED45A24 FOREIGN KEY (payment_record_id) REFERENCES payment_record (id)');
        $this->addSql('ALTER TABLE additional_fee_payment DROP FOREIGN KEY FK_BE3E169FFAE75C8B');
        $this->addSql('DROP TABLE additional_fee_payment');
        $this->addSql('ALTER TABLE additional_fee ADD parent_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', ADD child_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', ADD fee_amount DOUBLE PRECISION NOT NULL, ADD validity_from DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', ADD validity_to DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8DD62C21B FOREIGN KEY (child_id) REFERENCES additional_fee (id)');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8727ACA70 FOREIGN KEY (parent_id) REFERENCES additional_fee (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6C22BC8DD62C21B ON additional_fee (child_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6C22BC8727ACA70 ON additional_fee (parent_id)');
    }
}
