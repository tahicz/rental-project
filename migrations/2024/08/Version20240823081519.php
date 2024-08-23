<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240823081519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add rental recipe table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE rental_recipe (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', basic_rent INT NOT NULL, maturity INT NOT NULL, validity_from DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE rental_recipe');
    }
}
