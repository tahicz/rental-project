<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240828144800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add table for overpayment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE overpayment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', payment_record_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5B392FB8ED45A24 (payment_record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE overpayment ADD CONSTRAINT FK_5B392FB8ED45A24 FOREIGN KEY (payment_record_id) REFERENCES payment_record (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE overpayment DROP FOREIGN KEY FK_5B392FB8ED45A24');
        $this->addSql('DROP TABLE overpayment');
    }
}
