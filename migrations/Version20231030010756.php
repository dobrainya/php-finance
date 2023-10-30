<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231030010756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE amount_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reference_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE amount (id INT NOT NULL, type_id INT NOT NULL, name TEXT NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8EA17042C54C8C93 ON amount (type_id)');
        $this->addSql('CREATE TABLE reference (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE amount ADD CONSTRAINT FK_8EA17042C54C8C93 FOREIGN KEY (type_id) REFERENCES reference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE amount_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reference_id_seq CASCADE');
        $this->addSql('ALTER TABLE amount DROP CONSTRAINT FK_8EA17042C54C8C93');
        $this->addSql('DROP TABLE amount');
        $this->addSql('DROP TABLE reference');
    }
}
