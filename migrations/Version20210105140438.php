<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105140438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, sort INT NOT NULL, status TINYINT(1) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, template VARCHAR(255) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_lang (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, meta LONGTEXT DEFAULT NULL, INDEX IDX_E93ADCE412469DE2 (category_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, INDEX IDX_D95DB16B98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities_lang (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, INDEX IDX_139E586F8BAC62AF (city_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currencies (id INT UNSIGNED AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, value DOUBLE PRECISION UNSIGNED DEFAULT \'0\' NOT NULL, nominal SMALLINT UNSIGNED DEFAULT 1 NOT NULL, difference DOUBLE PRECISION DEFAULT \'0\' NOT NULL, date DATE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expert_quotes (id INT AUTO_INCREMENT NOT NULL, expert_id INT UNSIGNED NOT NULL, post_id INT UNSIGNED DEFAULT NULL, text LONGTEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, INDEX IDX_C4A78103C5568CE4 (expert_id), INDEX IDX_C4A781034B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_widgets (id INT UNSIGNED AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, type enum(\'youtube\',\'facebook\',\'twitter\',\'instagram\',\'vk\',\'playbuzz\',\'other\'), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persons (id INT UNSIGNED AUTO_INCREMENT NOT NULL, photo_id INT UNSIGNED DEFAULT NULL, status TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_A25CC7D37E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_photo (person_id INT UNSIGNED NOT NULL, photo_id INT UNSIGNED NOT NULL, INDEX IDX_B8819BDC217BBB47 (person_id), INDEX IDX_B8819BDC7E9E4C8C (photo_id), PRIMARY KEY(person_id, photo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persons_lang (id INT AUTO_INCREMENT NOT NULL, person_id INT UNSIGNED DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, language VARCHAR(255) NOT NULL, meta LONGTEXT DEFAULT NULL, INDEX IDX_B0742EF0217BBB47 (person_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos (id INT UNSIGNED AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, gradient_rgb VARCHAR(255) DEFAULT NULL, resolution VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos_lang (id INT AUTO_INCREMENT NOT NULL, photo_id INT UNSIGNED DEFAULT NULL, information VARCHAR(255) DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, language VARCHAR(255) NOT NULL, INDEX IDX_A61336837E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_exports (id INT AUTO_INCREMENT NOT NULL, post_id INT UNSIGNED DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D1C6A3734B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_logs (id INT AUTO_INCREMENT NOT NULL, post_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, snapshot LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_39DAA1574B89032C (post_id), INDEX IDX_39DAA157A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_notes (id INT AUTO_INCREMENT NOT NULL, body VARCHAR(255) NOT NULL, post_group_id INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_notifications (id INT AUTO_INCREMENT NOT NULL, post_id INT UNSIGNED DEFAULT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7F710EC04B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_short_links (hash VARCHAR(255) NOT NULL, post_id INT UNSIGNED DEFAULT NULL, redirect_to VARCHAR(255) NOT NULL, transits INT NOT NULL, UNIQUE INDEX UNIQ_BDC78B324B89032C (post_id), PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_speech (id INT UNSIGNED AUTO_INCREMENT NOT NULL, post_id INT UNSIGNED NOT NULL, filename VARCHAR(42) NOT NULL, original_filename VARCHAR(255) DEFAULT NULL, duration INT DEFAULT 0 NOT NULL, size INT DEFAULT 0 NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_BAD905924B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_id INT UNSIGNED DEFAULT NULL, category_id INT NOT NULL, translator_id INT DEFAULT NULL, author_id INT DEFAULT NULL, story_id INT DEFAULT NULL, city_id INT DEFAULT NULL, expert_id INT UNSIGNED DEFAULT NULL, photo_id INT UNSIGNED DEFAULT NULL, youtube_id VARCHAR(11) DEFAULT NULL, language VARCHAR(2) NOT NULL, created_by INT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, marked_words LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', option_type ENUM(\'ads\', \'closed\'), source VARCHAR(255) DEFAULT NULL, is_published TINYINT(1) DEFAULT \'0\' NOT NULL, is_deactivated TINYINT(1) DEFAULT \'0\', is_main TINYINT(1) DEFAULT \'0\' NOT NULL, is_exclusive TINYINT(1) DEFAULT \'0\' NOT NULL, is_actual TINYINT(1) DEFAULT \'0\' NOT NULL, is_breaking TINYINT(1) DEFAULT \'0\' NOT NULL, is_important TINYINT(1) DEFAULT \'0\' NOT NULL, views INT UNSIGNED DEFAULT 0 NOT NULL, links_no_index TINYINT(1) DEFAULT \'1\' NOT NULL, published_at DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, type VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, meta LONGTEXT DEFAULT NULL, INDEX IDX_885DBAFA727ACA70 (parent_id), INDEX IDX_885DBAFA12469DE2 (category_id), INDEX IDX_885DBAFA5370E40B (translator_id), INDEX IDX_885DBAFAF675F31B (author_id), INDEX IDX_885DBAFAAA5D4036 (story_id), INDEX IDX_885DBAFA8BAC62AF (city_id), INDEX IDX_885DBAFAC5568CE4 (expert_id), INDEX IDX_885DBAFA7E9E4C8C (photo_id), UNIQUE INDEX slug_unique (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_photo (post_id INT UNSIGNED NOT NULL, photo_id INT UNSIGNED NOT NULL, INDEX IDX_83AC08F74B89032C (post_id), INDEX IDX_83AC08F77E9E4C8C (photo_id), PRIMARY KEY(post_id, photo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_related (post_id INT UNSIGNED NOT NULL, related_id INT UNSIGNED NOT NULL, INDEX IDX_5DBFC7A34B89032C (post_id), INDEX IDX_5DBFC7A34162C001 (related_id), PRIMARY KEY(post_id, related_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_person (post_id INT UNSIGNED NOT NULL, person_id INT UNSIGNED NOT NULL, INDEX IDX_4FE35134B89032C (post_id), INDEX IDX_4FE3513217BBB47 (person_id), PRIMARY KEY(post_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_tag (post_id INT UNSIGNED NOT NULL, tag_id INT UNSIGNED NOT NULL, INDEX IDX_5ACE3AF04B89032C (post_id), INDEX IDX_5ACE3AF0BAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, sort INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions_lang (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_A426322C98260155 (region_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stories (id INT AUTO_INCREMENT NOT NULL, cover INT UNSIGNED DEFAULT NULL, status TINYINT(1) NOT NULL, show_on_site TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9C8B9D5F8D0886C5 (cover), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stories_lang (id INT AUTO_INCREMENT NOT NULL, story_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, language VARCHAR(255) NOT NULL, meta LONGTEXT DEFAULT NULL, INDEX IDX_A9D1A46DAA5D4036 (story_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT UNSIGNED AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FBC9426989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_lang (id INT AUTO_INCREMENT NOT NULL, tag_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, count INT NOT NULL, INDEX IDX_3EAE2832BAD26311 (tag_id), UNIQUE INDEX name_unique (name, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(50) NOT NULL, email_additional VARCHAR(50) DEFAULT NULL, password VARCHAR(255) NOT NULL, gender ENUM(\'male\', \'female\'), phone VARCHAR(100) DEFAULT NULL, birthdate DATE DEFAULT NULL, thumb VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), INDEX IDX_472E5446FED90CCA (permission_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_lang (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, position VARCHAR(255) DEFAULT NULL, slug VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, language CHAR(2) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, meta LONGTEXT DEFAULT NULL, INDEX IDX_FE80CA6CA76ED395 (user_id), UNIQUE INDEX slug (slug, language), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote_logs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vote_id INT UNSIGNED NOT NULL, vote_option_id INT UNSIGNED NOT NULL, user_agent VARCHAR(255) NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX IDX_47ED376D72DCDAFC (vote_id), INDEX IDX_47ED376DE588DAAA (vote_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote_options (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vote_id INT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, sort INT UNSIGNED NOT NULL, score INT UNSIGNED DEFAULT 0 NOT NULL, INDEX IDX_5B87A51F72DCDAFC (vote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE votes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, show_recaptcha TINYINT(1) DEFAULT \'0\' NOT NULL, title VARCHAR(255) NOT NULL, language VARCHAR(2) NOT NULL, show_on_main TINYINT(1) NOT NULL, start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, end_date DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather (id INT AUTO_INCREMENT NOT NULL, data LONGTEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE categories_lang ADD CONSTRAINT FK_E93ADCE412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16B98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE cities_lang ADD CONSTRAINT FK_139E586F8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE expert_quotes ADD CONSTRAINT FK_C4A78103C5568CE4 FOREIGN KEY (expert_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE expert_quotes ADD CONSTRAINT FK_C4A781034B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D37E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE person_photo ADD CONSTRAINT FK_B8819BDC217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_photo ADD CONSTRAINT FK_B8819BDC7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE persons_lang ADD CONSTRAINT FK_B0742EF0217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE photos_lang ADD CONSTRAINT FK_A61336837E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE post_exports ADD CONSTRAINT FK_D1C6A3734B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_logs ADD CONSTRAINT FK_39DAA1574B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_logs ADD CONSTRAINT FK_39DAA157A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_notifications ADD CONSTRAINT FK_7F710EC04B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_short_links ADD CONSTRAINT FK_BDC78B324B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_speech ADD CONSTRAINT FK_BAD905924B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA727ACA70 FOREIGN KEY (parent_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA5370E40B FOREIGN KEY (translator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAC5568CE4 FOREIGN KEY (expert_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_photo ADD CONSTRAINT FK_83AC08F74B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_photo ADD CONSTRAINT FK_83AC08F77E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_related ADD CONSTRAINT FK_5DBFC7A34B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE post_related ADD CONSTRAINT FK_5DBFC7A34162C001 FOREIGN KEY (related_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE post_person ADD CONSTRAINT FK_4FE35134B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_person ADD CONSTRAINT FK_4FE3513217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE regions_lang ADD CONSTRAINT FK_A426322C98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F8D0886C5 FOREIGN KEY (cover) REFERENCES photos (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE stories_lang ADD CONSTRAINT FK_A9D1A46DAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE tags_lang ADD CONSTRAINT FK_3EAE2832BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_lang ADD CONSTRAINT FK_FE80CA6CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE vote_logs ADD CONSTRAINT FK_47ED376D72DCDAFC FOREIGN KEY (vote_id) REFERENCES votes (id)');
        $this->addSql('ALTER TABLE vote_logs ADD CONSTRAINT FK_47ED376DE588DAAA FOREIGN KEY (vote_option_id) REFERENCES vote_options (id)');
        $this->addSql('ALTER TABLE vote_options ADD CONSTRAINT FK_5B87A51F72DCDAFC FOREIGN KEY (vote_id) REFERENCES votes (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE categories_lang DROP FOREIGN KEY FK_E93ADCE412469DE2');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA12469DE2');
        $this->addSql('ALTER TABLE cities_lang DROP FOREIGN KEY FK_139E586F8BAC62AF');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA8BAC62AF');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FED90CCA');
        $this->addSql('ALTER TABLE expert_quotes DROP FOREIGN KEY FK_C4A78103C5568CE4');
        $this->addSql('ALTER TABLE person_photo DROP FOREIGN KEY FK_B8819BDC217BBB47');
        $this->addSql('ALTER TABLE persons_lang DROP FOREIGN KEY FK_B0742EF0217BBB47');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAC5568CE4');
        $this->addSql('ALTER TABLE post_person DROP FOREIGN KEY FK_4FE3513217BBB47');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D37E9E4C8C');
        $this->addSql('ALTER TABLE person_photo DROP FOREIGN KEY FK_B8819BDC7E9E4C8C');
        $this->addSql('ALTER TABLE photos_lang DROP FOREIGN KEY FK_A61336837E9E4C8C');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA7E9E4C8C');
        $this->addSql('ALTER TABLE post_photo DROP FOREIGN KEY FK_83AC08F77E9E4C8C');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F8D0886C5');
        $this->addSql('ALTER TABLE expert_quotes DROP FOREIGN KEY FK_C4A781034B89032C');
        $this->addSql('ALTER TABLE post_exports DROP FOREIGN KEY FK_D1C6A3734B89032C');
        $this->addSql('ALTER TABLE post_logs DROP FOREIGN KEY FK_39DAA1574B89032C');
        $this->addSql('ALTER TABLE post_notifications DROP FOREIGN KEY FK_7F710EC04B89032C');
        $this->addSql('ALTER TABLE post_short_links DROP FOREIGN KEY FK_BDC78B324B89032C');
        $this->addSql('ALTER TABLE post_speech DROP FOREIGN KEY FK_BAD905924B89032C');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA727ACA70');
        $this->addSql('ALTER TABLE post_photo DROP FOREIGN KEY FK_83AC08F74B89032C');
        $this->addSql('ALTER TABLE post_related DROP FOREIGN KEY FK_5DBFC7A34B89032C');
        $this->addSql('ALTER TABLE post_related DROP FOREIGN KEY FK_5DBFC7A34162C001');
        $this->addSql('ALTER TABLE post_person DROP FOREIGN KEY FK_4FE35134B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16B98260155');
        $this->addSql('ALTER TABLE regions_lang DROP FOREIGN KEY FK_A426322C98260155');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAAA5D4036');
        $this->addSql('ALTER TABLE stories_lang DROP FOREIGN KEY FK_A9D1A46DAA5D4036');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE tags_lang DROP FOREIGN KEY FK_3EAE2832BAD26311');
        $this->addSql('ALTER TABLE post_logs DROP FOREIGN KEY FK_39DAA157A76ED395');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA5370E40B');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAF675F31B');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE users_lang DROP FOREIGN KEY FK_FE80CA6CA76ED395');
        $this->addSql('ALTER TABLE vote_logs DROP FOREIGN KEY FK_47ED376DE588DAAA');
        $this->addSql('ALTER TABLE vote_logs DROP FOREIGN KEY FK_47ED376D72DCDAFC');
        $this->addSql('ALTER TABLE vote_options DROP FOREIGN KEY FK_5B87A51F72DCDAFC');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_lang');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE cities_lang');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE expert_quotes');
        $this->addSql('DROP TABLE media_widgets');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE persons');
        $this->addSql('DROP TABLE person_photo');
        $this->addSql('DROP TABLE persons_lang');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE photos_lang');
        $this->addSql('DROP TABLE post_exports');
        $this->addSql('DROP TABLE post_logs');
        $this->addSql('DROP TABLE post_notes');
        $this->addSql('DROP TABLE post_notifications');
        $this->addSql('DROP TABLE post_short_links');
        $this->addSql('DROP TABLE post_speech');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE post_photo');
        $this->addSql('DROP TABLE post_related');
        $this->addSql('DROP TABLE post_person');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE regions_lang');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE stories');
        $this->addSql('DROP TABLE stories_lang');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_lang');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE users_lang');
        $this->addSql('DROP TABLE vote_logs');
        $this->addSql('DROP TABLE vote_options');
        $this->addSql('DROP TABLE votes');
        $this->addSql('DROP TABLE weather');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
