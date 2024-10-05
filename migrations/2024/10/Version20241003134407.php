<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241003134407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE rental_recipe_payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', rental_recipe_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', amount DOUBLE PRECISION NOT NULL, maturity INT NOT NULL, validity_from DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', validity_to DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F4F447F2E638A51B (rental_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rental_recipe_payment ADD CONSTRAINT FK_F4F447F2E638A51B FOREIGN KEY (rental_recipe_id) REFERENCES rental_recipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rental_recipe_payment DROP FOREIGN KEY FK_F4F447F2E638A51B');
        $this->addSql('DROP TABLE rental_recipe_payment');
    }
}
