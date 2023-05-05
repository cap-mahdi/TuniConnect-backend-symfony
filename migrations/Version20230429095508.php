<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230429095508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member ADD date_of_membership DATE NOT NULL');
        $this->addSql('ALTER TABLE message DROP is_edited');
        $this->addSql('DROP INDEX UNIQ_34DCD1769B6B5FBA ON person');
        $this->addSql('ALTER TABLE person DROP email, DROP is_admin, CHANGE account_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176A76ED395 ON person (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `member` DROP date_of_membership');
        $this->addSql('ALTER TABLE message ADD is_edited TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176A76ED395');
        $this->addSql('DROP INDEX UNIQ_34DCD176A76ED395 ON person');
        $this->addSql('ALTER TABLE person ADD email VARCHAR(100) NOT NULL, ADD is_admin TINYINT(1) NOT NULL, CHANGE user_id account_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD1769B6B5FBA ON person (account_id)');
    }
}
