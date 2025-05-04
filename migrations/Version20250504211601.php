<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250504211601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE budget ADD name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expenses ADD budget_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B36ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2496F35B36ABA6B8 ON expenses (budget_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE budget DROP name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B36ABA6B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2496F35B36ABA6B8 ON expenses
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expenses DROP budget_id
        SQL);
    }
}
