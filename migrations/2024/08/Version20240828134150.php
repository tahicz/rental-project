<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240828134150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add payment record table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payment_record (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', payment_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION NOT NULL, payment_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BE0C04074C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_record ADD CONSTRAINT FK_BE0C04074C3A3BB FOREIGN KEY (payment_id) REFERENCES payment_recipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_record DROP FOREIGN KEY FK_BE0C04074C3A3BB');
        $this->addSql('DROP TABLE payment_record');
    }
}
