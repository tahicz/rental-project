<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241004173034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rental_recipe DROP FOREIGN KEY FK_EAA6A550727ACA70');
        $this->addSql('ALTER TABLE rental_recipe DROP FOREIGN KEY FK_EAA6A550DD62C21B');
        $this->addSql('DROP INDEX UNIQ_EAA6A550727ACA70 ON rental_recipe');
        $this->addSql('DROP INDEX UNIQ_EAA6A550DD62C21B ON rental_recipe');
        $this->addSql('ALTER TABLE rental_recipe DROP parent_id, DROP child_id, DROP basic_rent, DROP maturity, DROP note');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rental_recipe ADD parent_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', ADD child_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', ADD basic_rent DOUBLE PRECISION NOT NULL, ADD maturity INT NOT NULL, ADD note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE rental_recipe ADD CONSTRAINT FK_EAA6A550727ACA70 FOREIGN KEY (parent_id) REFERENCES rental_recipe (id)');
        $this->addSql('ALTER TABLE rental_recipe ADD CONSTRAINT FK_EAA6A550DD62C21B FOREIGN KEY (child_id) REFERENCES rental_recipe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EAA6A550727ACA70 ON rental_recipe (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EAA6A550DD62C21B ON rental_recipe (child_id)');
    }
}
