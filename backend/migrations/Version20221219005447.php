<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219005447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `appointment` (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, carwash_id INT DEFAULT NULL, service_id INT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, INDEX IDX_FE38F8449395C3F3 (customer_id), INDEX IDX_FE38F8444FB0BF84 (carwash_id), INDEX IDX_FE38F844ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `carwash` (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, address VARCHAR(256) NOT NULL, name VARCHAR(256) NOT NULL, UNIQUE INDEX UNIQ_9F0E1C3BD4E6F81 (address), UNIQUE INDEX UNIQ_9F0E1C3B5E237E06 (name), INDEX IDX_9F0E1C3B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carwash_service (carwash_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_8450138F4FB0BF84 (carwash_id), INDEX IDX_8450138FED5CA9E6 (service_id), PRIMARY KEY(carwash_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `service` (id INT AUTO_INCREMENT NOT NULL, price INT NOT NULL, description VARCHAR(256) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, telephone_nr VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64945F1B9BF (telephone_nr), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `appointment` ADD CONSTRAINT FK_FE38F8449395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `appointment` ADD CONSTRAINT FK_FE38F8444FB0BF84 FOREIGN KEY (carwash_id) REFERENCES `carwash` (id)');
        $this->addSql('ALTER TABLE `appointment` ADD CONSTRAINT FK_FE38F844ED5CA9E6 FOREIGN KEY (service_id) REFERENCES `service` (id)');
        $this->addSql('ALTER TABLE `carwash` ADD CONSTRAINT FK_9F0E1C3B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE carwash_service ADD CONSTRAINT FK_8450138F4FB0BF84 FOREIGN KEY (carwash_id) REFERENCES `carwash` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carwash_service ADD CONSTRAINT FK_8450138FED5CA9E6 FOREIGN KEY (service_id) REFERENCES `service` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `appointment` DROP FOREIGN KEY FK_FE38F8449395C3F3');
        $this->addSql('ALTER TABLE `appointment` DROP FOREIGN KEY FK_FE38F8444FB0BF84');
        $this->addSql('ALTER TABLE `appointment` DROP FOREIGN KEY FK_FE38F844ED5CA9E6');
        $this->addSql('ALTER TABLE `carwash` DROP FOREIGN KEY FK_9F0E1C3B7E3C61F9');
        $this->addSql('ALTER TABLE carwash_service DROP FOREIGN KEY FK_8450138F4FB0BF84');
        $this->addSql('ALTER TABLE carwash_service DROP FOREIGN KEY FK_8450138FED5CA9E6');
        $this->addSql('DROP TABLE `appointment`');
        $this->addSql('DROP TABLE `carwash`');
        $this->addSql('DROP TABLE carwash_service');
        $this->addSql('DROP TABLE `service`');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
