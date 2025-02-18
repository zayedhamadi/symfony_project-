<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215140536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(180) DEFAULT NULL, CHANGE prenom prenom VARCHAR(180) DEFAULT NULL, CHANGE adresse adresse VARCHAR(180) DEFAULT NULL, CHANGE description description VARCHAR(300) DEFAULT NULL, CHANGE genre genre VARCHAR(255) DEFAULT NULL, CHANGE etat_compte etat_compte VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance DATE DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(180) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(180) DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(180) DEFAULT \'NULL\', CHANGE description description VARCHAR(300) DEFAULT \'NULL\', CHANGE genre genre VARCHAR(255) DEFAULT \'NULL\', CHANGE etat_compte etat_compte VARCHAR(255) DEFAULT \'NULL\', CHANGE date_naissance date_naissance DATE DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
