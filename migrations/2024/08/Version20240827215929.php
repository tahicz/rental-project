<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240827215929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add payment entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', rental_recipe_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION NOT NULL, payment_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', maturity_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_6D28840DE638A51B (rental_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE638A51B FOREIGN KEY (rental_recipe_id) REFERENCES rental_recipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DE638A51B');
        $this->addSql('DROP TABLE payment');
    }
}
