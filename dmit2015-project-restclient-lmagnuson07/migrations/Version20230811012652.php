<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811012652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edmonton_property_assessment_data MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON edmonton_property_assessment_data');
        $this->addSql('ALTER TABLE edmonton_property_assessment_data DROP id');
        $this->addSql('ALTER TABLE edmonton_property_assessment_data ADD PRIMARY KEY (account_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edmonton_property_assessment_data ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
