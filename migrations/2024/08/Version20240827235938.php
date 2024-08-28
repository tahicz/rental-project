<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240827235938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment ADD paid_amount DOUBLE PRECISION DEFAULT NULL, CHANGE rental_recipe_id rental_recipe_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', CHANGE amount payable_amount DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment DROP paid_amount, CHANGE rental_recipe_id rental_recipe_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', CHANGE payable_amount amount DOUBLE PRECISION NOT NULL');
    }
}
