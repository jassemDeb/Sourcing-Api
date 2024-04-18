<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418151050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_additionnal (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, fullname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_FC0C2596E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_organization_user_additionnal (core_organization_id INT NOT NULL, user_additionnal_id INT NOT NULL, INDEX IDX_233E739C4F346186 (core_organization_id), INDEX IDX_233E739CC617743F (user_additionnal_id), PRIMARY KEY(core_organization_id, user_additionnal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_organization_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_organization_type_core_organization (core_organization_type_id INT NOT NULL, core_organization_id INT NOT NULL, INDEX IDX_5ACCD196FAF62B58 (core_organization_type_id), INDEX IDX_5ACCD1964F346186 (core_organization_id), PRIMARY KEY(core_organization_type_id, core_organization_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dashboard_configuration (id INT AUTO_INCREMENT NOT NULL, core_user_additionnal_id INT DEFAULT NULL, core_organization_id INT DEFAULT NULL, core_organization_type_id INT DEFAULT NULL, is_default TINYINT(1) NOT NULL, INDEX IDX_7064DD4ECC38B26C (core_user_additionnal_id), INDEX IDX_7064DD4E4F346186 (core_organization_id), INDEX IDX_7064DD4EFAF62B58 (core_organization_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dashboard_configuration_widget (id INT AUTO_INCREMENT NOT NULL, dashboard_widget_id INT DEFAULT NULL, core_dashboard_configuration_id INT DEFAULT NULL, name_fr VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, widget_style VARCHAR(255) NOT NULL, widget_width VARCHAR(255) NOT NULL, widget_height VARCHAR(255) NOT NULL, widget_rank VARCHAR(255) NOT NULL, INDEX IDX_C1D38997B31FDD11 (dashboard_widget_id), INDEX IDX_C1D389979BAC2DAB (core_dashboard_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dashboard_widget (id INT AUTO_INCREMENT NOT NULL, core_organization_type_id INT DEFAULT NULL, core_organization_id INT DEFAULT NULL, is_default TINYINT(1) NOT NULL, description_en VARCHAR(255) NOT NULL, description_fr VARCHAR(255) NOT NULL, widget_url VARCHAR(255) NOT NULL, widget_type VARCHAR(255) NOT NULL, transaction_type VARCHAR(255) NOT NULL, widget_visibility VARCHAR(255) NOT NULL, widget_conditions VARCHAR(255) NOT NULL, INDEX IDX_6AC217EBFAF62B58 (core_organization_type_id), INDEX IDX_6AC217EB4F346186 (core_organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, fullname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_additionnal (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, fullname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_admin TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C80DA50AE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_organization_user_additionnal ADD CONSTRAINT FK_233E739C4F346186 FOREIGN KEY (core_organization_id) REFERENCES core_organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_organization_user_additionnal ADD CONSTRAINT FK_233E739CC617743F FOREIGN KEY (user_additionnal_id) REFERENCES user_additionnal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_organization_type_core_organization ADD CONSTRAINT FK_5ACCD196FAF62B58 FOREIGN KEY (core_organization_type_id) REFERENCES core_organization_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_organization_type_core_organization ADD CONSTRAINT FK_5ACCD1964F346186 FOREIGN KEY (core_organization_id) REFERENCES core_organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dashboard_configuration ADD CONSTRAINT FK_7064DD4ECC38B26C FOREIGN KEY (core_user_additionnal_id) REFERENCES user_additionnal (id)');
        $this->addSql('ALTER TABLE dashboard_configuration ADD CONSTRAINT FK_7064DD4E4F346186 FOREIGN KEY (core_organization_id) REFERENCES core_organization (id)');
        $this->addSql('ALTER TABLE dashboard_configuration ADD CONSTRAINT FK_7064DD4EFAF62B58 FOREIGN KEY (core_organization_type_id) REFERENCES core_organization_type (id)');
        $this->addSql('ALTER TABLE dashboard_configuration_widget ADD CONSTRAINT FK_C1D38997B31FDD11 FOREIGN KEY (dashboard_widget_id) REFERENCES dashboard_widget (id)');
        $this->addSql('ALTER TABLE dashboard_configuration_widget ADD CONSTRAINT FK_C1D389979BAC2DAB FOREIGN KEY (core_dashboard_configuration_id) REFERENCES dashboard_configuration (id)');
        $this->addSql('ALTER TABLE dashboard_widget ADD CONSTRAINT FK_6AC217EBFAF62B58 FOREIGN KEY (core_organization_type_id) REFERENCES core_organization_type (id)');
        $this->addSql('ALTER TABLE dashboard_widget ADD CONSTRAINT FK_6AC217EB4F346186 FOREIGN KEY (core_organization_id) REFERENCES core_organization (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE core_organization_user_additionnal DROP FOREIGN KEY FK_233E739C4F346186');
        $this->addSql('ALTER TABLE core_organization_user_additionnal DROP FOREIGN KEY FK_233E739CC617743F');
        $this->addSql('ALTER TABLE core_organization_type_core_organization DROP FOREIGN KEY FK_5ACCD196FAF62B58');
        $this->addSql('ALTER TABLE core_organization_type_core_organization DROP FOREIGN KEY FK_5ACCD1964F346186');
        $this->addSql('ALTER TABLE dashboard_configuration DROP FOREIGN KEY FK_7064DD4ECC38B26C');
        $this->addSql('ALTER TABLE dashboard_configuration DROP FOREIGN KEY FK_7064DD4E4F346186');
        $this->addSql('ALTER TABLE dashboard_configuration DROP FOREIGN KEY FK_7064DD4EFAF62B58');
        $this->addSql('ALTER TABLE dashboard_configuration_widget DROP FOREIGN KEY FK_C1D38997B31FDD11');
        $this->addSql('ALTER TABLE dashboard_configuration_widget DROP FOREIGN KEY FK_C1D389979BAC2DAB');
        $this->addSql('ALTER TABLE dashboard_widget DROP FOREIGN KEY FK_6AC217EBFAF62B58');
        $this->addSql('ALTER TABLE dashboard_widget DROP FOREIGN KEY FK_6AC217EB4F346186');
        $this->addSql('DROP TABLE admin_additionnal');
        $this->addSql('DROP TABLE core_organization');
        $this->addSql('DROP TABLE core_organization_user_additionnal');
        $this->addSql('DROP TABLE core_organization_type');
        $this->addSql('DROP TABLE core_organization_type_core_organization');
        $this->addSql('DROP TABLE dashboard_configuration');
        $this->addSql('DROP TABLE dashboard_configuration_widget');
        $this->addSql('DROP TABLE dashboard_widget');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_additionnal');
    }
}
