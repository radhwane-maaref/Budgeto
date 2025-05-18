<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514093434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE saving_goal (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, target_amount DOUBLE PRECISION NOT NULL, current_amount DOUBLE PRECISION NOT NULL, deadline DATETIME DEFAULT NULL, INDEX IDX_D7DC8FEDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saving_goal ADD CONSTRAINT FK_D7DC8FEDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
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
            ALTER TABLE saving_goal DROP FOREIGN KEY FK_D7DC8FEDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE saving_goal
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B36ABA6B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2496F35B36ABA6B8 ON expenses
        SQL);
    }
}
