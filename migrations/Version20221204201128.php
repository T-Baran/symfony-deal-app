<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221204201128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF60E2305F60E2305');
        $this->addSql('DROP INDEX IDX_9474526CF60E2305 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE deal_id deal INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE3FEC116 FOREIGN KEY (deal) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9474526CE3FEC116 ON comment (deal)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE3FEC116');
        $this->addSql('DROP INDEX IDX_9474526CE3FEC116 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE deal deal_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF60E2305F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9474526CF60E2305 ON comment (deal_id)');
    }
}
