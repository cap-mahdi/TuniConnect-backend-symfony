<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505133626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, commenter_id INT NOT NULL, text VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CB4D5A9E2 (commenter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covoiturage (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, destination VARCHAR(50) NOT NULL, departure VARCHAR(50) NOT NULL, departure_time DATETIME NOT NULL, number_of_places INT NOT NULL, number_of_places_taken SMALLINT NOT NULL, price SMALLINT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_28C79E89C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covoiturage_member (covoiturage_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_8D8676F362671590 (covoiturage_id), INDEX IDX_8D8676F37597D3FE (member_id), PRIMARY KEY(covoiturage_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covoiturage_notification (id INT AUTO_INCREMENT NOT NULL, covoiturage_id INT NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9306104962671590 (covoiturage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend_request (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, status VARCHAR(20) DEFAULT \'pending\' NOT NULL, INDEX IDX_F284D94F624B39D (sender_id), INDEX IDX_F284D94CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_member (member_source INT NOT NULL, member_target INT NOT NULL, INDEX IDX_5432784B7B5CFD40 (member_source), INDEX IDX_5432784B62B9ADCF (member_target), PRIMARY KEY(member_source, member_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, related_to_id INT NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BF5476CA40B4AC4E (related_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_notification (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_14690B194B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_covoiturage (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, covoiturage_id INT NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_33B3DDCF624B39D (sender_id), INDEX IDX_33B3DDC62671590 (covoiturage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, INDEX IDX_729F519B61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_member (room_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_31AA3CB954177093 (room_id), INDEX IDX_31AA3CB97597D3FE (member_id), PRIMARY KEY(room_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shared_post (id INT AUTO_INCREMENT NOT NULL, sharer_id INT DEFAULT NULL, post_id INT NOT NULL, INDEX IDX_E07C07154EE63723 (sharer_id), INDEX IDX_E07C07154B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shared_post_member (shared_post_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_4B69D2DC6A5E0F9B (shared_post_id), INDEX IDX_4B69D2DC7597D3FE (member_id), PRIMARY KEY(shared_post_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES shared_post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB4D5A9E2 FOREIGN KEY (commenter_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89C3423909 FOREIGN KEY (driver_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE covoiturage_member ADD CONSTRAINT FK_8D8676F362671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE covoiturage_member ADD CONSTRAINT FK_8D8676F37597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE covoiturage_notification ADD CONSTRAINT FK_9306104962671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94F624B39D FOREIGN KEY (sender_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE member_member ADD CONSTRAINT FK_5432784B7B5CFD40 FOREIGN KEY (member_source) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_member ADD CONSTRAINT FK_5432784B62B9ADCF FOREIGN KEY (member_target) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA40B4AC4E FOREIGN KEY (related_to_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE request_covoiturage ADD CONSTRAINT FK_33B3DDCF624B39D FOREIGN KEY (sender_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE request_covoiturage ADD CONSTRAINT FK_33B3DDC62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B61220EA6 FOREIGN KEY (creator_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE room_member ADD CONSTRAINT FK_31AA3CB954177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_member ADD CONSTRAINT FK_31AA3CB97597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post ADD CONSTRAINT FK_E07C07154EE63723 FOREIGN KEY (sharer_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE shared_post ADD CONSTRAINT FK_E07C07154B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE shared_post_member ADD CONSTRAINT FK_4B69D2DC6A5E0F9B FOREIGN KEY (shared_post_id) REFERENCES shared_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shared_post_member ADD CONSTRAINT FK_4B69D2DC7597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages_receiver DROP FOREIGN KEY FK_2CA02C9E537A1329');
        $this->addSql('ALTER TABLE messages_receiver DROP FOREIGN KEY FK_2CA02C9E7597D3FE');
        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_DED1C2924B89032C');
        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_DED1C2927597D3FE');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B57597D3FE');
        $this->addSql('ALTER TABLE post_share DROP FOREIGN KEY FK_781D11B54B89032C');
        $this->addSql('DROP TABLE messages_receiver');
        $this->addSql('DROP TABLE post_likes');
        $this->addSql('DROP TABLE post_share');
        $this->addSql('ALTER TABLE message ADD room_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F54177093 ON message (room_id)');
        $this->addSql('ALTER TABLE person ADD last_name VARCHAR(50) NOT NULL, ADD birthday DATE NOT NULL, ADD gender VARCHAR(10) NOT NULL, ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD cover_picture VARCHAR(255) DEFAULT NULL, CHANGE phone phone INT DEFAULT NULL, CHANGE name first_name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D5BB66C05');
        $this->addSql('DROP INDEX IDX_5A8A6C8D5BB66C05 ON post');
        $this->addSql('ALTER TABLE post ADD photos LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', DROP poster_id, DROP total_likes, DROP total_shares, DROP date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F54177093');
        $this->addSql('CREATE TABLE messages_receiver (message_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_2CA02C9E537A1329 (message_id), INDEX IDX_2CA02C9E7597D3FE (member_id), PRIMARY KEY(message_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE post_likes (post_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_DED1C2924B89032C (post_id), INDEX IDX_DED1C2927597D3FE (member_id), PRIMARY KEY(post_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE post_share (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, member_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_781D11B54B89032C (post_id), INDEX IDX_781D11B57597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE messages_receiver ADD CONSTRAINT FK_2CA02C9E537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages_receiver ADD CONSTRAINT FK_2CA02C9E7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_likes ADD CONSTRAINT FK_DED1C2924B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_likes ADD CONSTRAINT FK_DED1C2927597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B57597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE post_share ADD CONSTRAINT FK_781D11B54B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB4D5A9E2');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89C3423909');
        $this->addSql('ALTER TABLE covoiturage_member DROP FOREIGN KEY FK_8D8676F362671590');
        $this->addSql('ALTER TABLE covoiturage_member DROP FOREIGN KEY FK_8D8676F37597D3FE');
        $this->addSql('ALTER TABLE covoiturage_notification DROP FOREIGN KEY FK_9306104962671590');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94F624B39D');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94CD53EDB6');
        $this->addSql('ALTER TABLE member_member DROP FOREIGN KEY FK_5432784B7B5CFD40');
        $this->addSql('ALTER TABLE member_member DROP FOREIGN KEY FK_5432784B62B9ADCF');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA40B4AC4E');
        $this->addSql('ALTER TABLE post_notification DROP FOREIGN KEY FK_14690B194B89032C');
        $this->addSql('ALTER TABLE request_covoiturage DROP FOREIGN KEY FK_33B3DDCF624B39D');
        $this->addSql('ALTER TABLE request_covoiturage DROP FOREIGN KEY FK_33B3DDC62671590');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B61220EA6');
        $this->addSql('ALTER TABLE room_member DROP FOREIGN KEY FK_31AA3CB954177093');
        $this->addSql('ALTER TABLE room_member DROP FOREIGN KEY FK_31AA3CB97597D3FE');
        $this->addSql('ALTER TABLE shared_post DROP FOREIGN KEY FK_E07C07154EE63723');
        $this->addSql('ALTER TABLE shared_post DROP FOREIGN KEY FK_E07C07154B89032C');
        $this->addSql('ALTER TABLE shared_post_member DROP FOREIGN KEY FK_4B69D2DC6A5E0F9B');
        $this->addSql('ALTER TABLE shared_post_member DROP FOREIGN KEY FK_4B69D2DC7597D3FE');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE covoiturage');
        $this->addSql('DROP TABLE covoiturage_member');
        $this->addSql('DROP TABLE covoiturage_notification');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE member_member');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post_notification');
        $this->addSql('DROP TABLE request_covoiturage');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_member');
        $this->addSql('DROP TABLE shared_post');
        $this->addSql('DROP TABLE shared_post_member');
        $this->addSql('DROP INDEX IDX_B6BD307F54177093 ON message');
        $this->addSql('ALTER TABLE message DROP room_id');
        $this->addSql('ALTER TABLE person ADD name VARCHAR(50) NOT NULL, DROP first_name, DROP last_name, DROP birthday, DROP gender, DROP profile_picture, DROP cover_picture, CHANGE phone phone INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD poster_id INT NOT NULL, ADD total_likes INT NOT NULL, ADD total_shares INT NOT NULL, ADD date DATETIME NOT NULL, DROP photos');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D5BB66C05 FOREIGN KEY (poster_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D5BB66C05 ON post (poster_id)');
    }
}
