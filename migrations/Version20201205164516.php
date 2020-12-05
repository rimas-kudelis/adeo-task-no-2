<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201205164516 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create tables for products and weather conditions';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather_condition (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_weather_condition (product_id INT NOT NULL, weather_condition_id INT NOT NULL, INDEX IDX_23DD3B234584665A (product_id), INDEX IDX_23DD3B2386C2CF78 (weather_condition_id), PRIMARY KEY(product_id, weather_condition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_weather_condition ADD CONSTRAINT FK_23DD3B234584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_weather_condition ADD CONSTRAINT FK_23DD3B2386C2CF78 FOREIGN KEY (weather_condition_id) REFERENCES weather_condition (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_weather_condition DROP FOREIGN KEY FK_23DD3B234584665A');
        $this->addSql('ALTER TABLE product_weather_condition DROP FOREIGN KEY FK_23DD3B2386C2CF78');
        $this->addSql('DROP TABLE product_weather_condition');
        $this->addSql('DROP TABLE weather_condition');
        $this->addSql('DROP TABLE product');
    }
}
