<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240829142043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add income and bank account data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bank_account (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', prefix INT NOT NULL, account_number INT NOT NULL, bank_code INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', recipient_account_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', sender_account_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', income_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', amount DOUBLE PRECISION NOT NULL, variable_symbol INT NOT NULL, message VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3FA862D0E36B15C4 (recipient_account_id), INDEX IDX_3FA862D0CFEF0177 (sender_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0E36B15C4 FOREIGN KEY (recipient_account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0CFEF0177 FOREIGN KEY (sender_account_id) REFERENCES bank_account (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0E36B15C4');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0CFEF0177');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE income');
    }
}
