<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231029204143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Generates income\expense table';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
CREATE TABLE amounts (
    id UUID,
    name TEXT NOT NULL,
    amount NUMERIC (15, 2) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP
)
SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE amounts');
    }
}
