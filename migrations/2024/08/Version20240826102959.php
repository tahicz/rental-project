<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240826102959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', CHANGE rent_recipe_id rent_recipe_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE rent_recipe_id rent_recipe_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\'');
    }
}
