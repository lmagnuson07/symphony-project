<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * @Created 2023-08-09
 */
final class Version20230810020246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edmonton_property_assessment_data (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(255) NOT NULL, suite VARCHAR(255) DEFAULT NULL, house_number VARCHAR(255) DEFAULT NULL, street_name VARCHAR(255) DEFAULT NULL, garage TINYINT(1) DEFAULT NULL, neighbourhood_id INT DEFAULT NULL, neighbourhood VARCHAR(255) DEFAULT NULL, ward VARCHAR(255) DEFAULT NULL, assessed_value INT DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, assessment_class1 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE edmonton_property_assessment_data');
    }
}
