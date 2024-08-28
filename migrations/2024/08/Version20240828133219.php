<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240828133219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename payment table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payment_recipe (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', rental_recipe_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', payable_amount DOUBLE PRECISION NOT NULL, payment_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', maturity_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', paid_amount DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FFB02AA1E638A51B (rental_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_recipe ADD CONSTRAINT FK_FFB02AA1E638A51B FOREIGN KEY (rental_recipe_id) REFERENCES rental_recipe (id)');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DE638A51B');
		$this->addSql('INSERT INTO payment_recipe SELECT * FROM payment');
        $this->addSql('DROP TABLE payment');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', rental_recipe_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', payable_amount DOUBLE PRECISION NOT NULL, payment_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', maturity_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, paid_amount DOUBLE PRECISION DEFAULT NULL, INDEX IDX_6D28840DE638A51B (rental_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE638A51B FOREIGN KEY (rental_recipe_id) REFERENCES rental_recipe (id)');
        $this->addSql('ALTER TABLE payment_recipe DROP FOREIGN KEY FK_FFB02AA1E638A51B');
		$this->addSql('INSERT INTO payment SELECT * FROM payment_recipe');
        $this->addSql('DROP TABLE payment_recipe');
    }
}
