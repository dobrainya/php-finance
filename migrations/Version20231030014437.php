<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030014437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
INSERT INTO reference (name, code) VALUES ('Expense', 'exp'), ('Income', 'inc')
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE reference CASCADE');
    }
}
