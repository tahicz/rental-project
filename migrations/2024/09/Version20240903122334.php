<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240903122334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_record DROP FOREIGN KEY FK_BE0C04074C3A3BB');
        $this->addSql('DROP INDEX IDX_BE0C04074C3A3BB ON payment_record');
        $this->addSql('ALTER TABLE payment_record ADD payment_recipe_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', CHANGE payment_id income_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', CHANGE payment_date received_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE payment_record ADD CONSTRAINT FK_BE0C0407640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id)');
        $this->addSql('ALTER TABLE payment_record ADD CONSTRAINT FK_BE0C04071AF18D3C FOREIGN KEY (payment_recipe_id) REFERENCES payment_recipe (id)');
        $this->addSql('CREATE INDEX IDX_BE0C0407640ED2C0 ON payment_record (income_id)');
        $this->addSql('CREATE INDEX IDX_BE0C04071AF18D3C ON payment_record (payment_recipe_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_record DROP FOREIGN KEY FK_BE0C0407640ED2C0');
        $this->addSql('ALTER TABLE payment_record DROP FOREIGN KEY FK_BE0C04071AF18D3C');
        $this->addSql('DROP INDEX IDX_BE0C0407640ED2C0 ON payment_record');
        $this->addSql('DROP INDEX IDX_BE0C04071AF18D3C ON payment_record');
        $this->addSql('ALTER TABLE payment_record DROP payment_recipe_id, CHANGE income_id payment_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', CHANGE received_on payment_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE payment_record ADD CONSTRAINT FK_BE0C04074C3A3BB FOREIGN KEY (payment_id) REFERENCES payment_recipe (id)');
        $this->addSql('CREATE INDEX IDX_BE0C04074C3A3BB ON payment_record (payment_id)');
    }
}
