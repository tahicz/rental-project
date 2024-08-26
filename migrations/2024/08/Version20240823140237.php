<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240823140237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add additional fee table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE additional_fee (id INT AUTO_INCREMENT NOT NULL, rent_recipe_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', description VARCHAR(255) NOT NULL, fee_amount INT NOT NULL, payment_frequency VARCHAR(255) NOT NULL, billable TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6C22BC8B130927A (rent_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE additional_fee ADD CONSTRAINT FK_B6C22BC8B130927A FOREIGN KEY (rent_recipe_id) REFERENCES rental_recipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE additional_fee DROP FOREIGN KEY FK_B6C22BC8B130927A');
        $this->addSql('DROP TABLE additional_fee');
    }
}
