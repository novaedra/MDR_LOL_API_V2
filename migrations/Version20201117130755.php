<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117130755 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, tournois_report_id INT DEFAULT NULL, user_accuser_id INT DEFAULT NULL, user_accused_id INT DEFAULT NULL, motif VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_C42F7784426337E8 (tournois_report_id), INDEX IDX_C42F77847B40D706 (user_accuser_id), INDEX IDX_C42F7784E32DF45 (user_accused_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournois (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournois_utilisateur (tournois_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_29470656752534C (tournois_id), INDEX IDX_29470656FB88E14F (utilisateur_id), PRIMARY KEY(tournois_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784426337E8 FOREIGN KEY (tournois_report_id) REFERENCES tournois (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77847B40D706 FOREIGN KEY (user_accuser_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E32DF45 FOREIGN KEY (user_accused_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE tournois_utilisateur ADD CONSTRAINT FK_29470656752534C FOREIGN KEY (tournois_id) REFERENCES tournois (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournois_utilisateur ADD CONSTRAINT FK_29470656FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784426337E8');
        $this->addSql('ALTER TABLE tournois_utilisateur DROP FOREIGN KEY FK_29470656752534C');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE tournois');
        $this->addSql('DROP TABLE tournois_utilisateur');
    }
}
