<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240828144856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_recipe CHANGE paid_amount paid_amount DOUBLE PRECISION DEFAULT \'0\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_recipe CHANGE paid_amount paid_amount DOUBLE PRECISION DEFAULT NULL');
    }
}
