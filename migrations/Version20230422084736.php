<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422084736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B54B89032C');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B57597D3FE');
        $this->addSql('ALTER TABLE post_share ADD id INT AUTO_INCREMENT NOT NULL, ADD date DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B54B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B57597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP date');
        $this->addSql('ALTER TABLE post_share MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B54B89032C');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B57597D3FE');
        $this->addSql('DROP INDEX `PRIMARY` ON post_share');
        $this->addSql('ALTER TABLE post_share DROP id, DROP date');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B54B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B57597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_share ADD PRIMARY KEY (post_id, member_id)');
    }
}
