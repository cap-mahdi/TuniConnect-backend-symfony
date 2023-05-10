<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509183144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shared_post_likes (shared_post_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_AAC2225D6A5E0F9B (shared_post_id), INDEX IDX_AAC2225D7597D3FE (member_id), PRIMARY KEY(shared_post_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shared_post_likes ADD CONSTRAINT FK_AAC2225D6A5E0F9B FOREIGN KEY (shared_post_id) REFERENCES shared_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post_likes ADD CONSTRAINT FK_AAC2225D7597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post_member DROP FOREIGN KEY FK_4B69D2DC6A5E0F9B');
        $this->addSql('ALTER TABLE shared_post_member DROP FOREIGN KEY FK_4B69D2DC7597D3FE');
        $this->addSql('DROP TABLE shared_post_member');
        $this->addSql('ALTER TABLE covoiturage_notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post ADD owner_id INT NOT NULL, ADD date DATETIME NOT NULL, ADD edited TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7E3C61F9 ON post (owner_id)');
        $this->addSql('ALTER TABLE post_notification DROP FOREIGN KEY FK_14690B194B89032C');
        $this->addSql('ALTER TABLE post_notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES shared_post (id)');
        $this->addSql('ALTER TABLE shared_post ADD date DATETIME NOT NULL, ADD is_shared TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shared_post_member (shared_post_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_4B69D2DC7597D3FE (member_id), INDEX IDX_4B69D2DC6A5E0F9B (shared_post_id), PRIMARY KEY(shared_post_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE shared_post_member ADD CONSTRAINT FK_4B69D2DC6A5E0F9B FOREIGN KEY (shared_post_id) REFERENCES shared_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post_member ADD CONSTRAINT FK_4B69D2DC7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post_likes DROP FOREIGN KEY FK_AAC2225D6A5E0F9B');
        $this->addSql('ALTER TABLE shared_post_likes DROP FOREIGN KEY FK_AAC2225D7597D3FE');
        $this->addSql('DROP TABLE shared_post_likes');
        $this->addSql('ALTER TABLE covoiturage_notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7E3C61F9');
        $this->addSql('DROP INDEX IDX_5A8A6C8D7E3C61F9 ON post');
        $this->addSql('ALTER TABLE post DROP owner_id, DROP date, DROP edited');
        $this->addSql('ALTER TABLE post_notification DROP FOREIGN KEY FK_14690B194B89032C');
        $this->addSql('ALTER TABLE post_notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE shared_post DROP date, DROP is_shared');
    }
}
