<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301100939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cessation (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, motif VARCHAR(255) NOT NULL, date_motif DATE DEFAULT NULL, UNIQUE INDEX UNIQ_4BC106779F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, nom_classe VARCHAR(255) NOT NULL, num_salle INT NOT NULL, capacite_max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe_user (classe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9380A3AF8F5EA509 (classe_id), INDEX IDX_9380A3AFA76ED395 (user_id), PRIMARY KEY(classe_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, date_commande DATETIME NOT NULL, montant_total DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, mode_paiement VARCHAR(20) NOT NULL, INDEX IDX_6EEAA67D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, id_matiere_id INT NOT NULL, name VARCHAR(255) NOT NULL, pdf_filename VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FDCA8C9C51E6528F (id_matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, id_classe_id INT DEFAULT NULL, id_parent_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, moyenne DOUBLE PRECISION NOT NULL, nbre_abscence INT NOT NULL, date_inscription DATE NOT NULL, INDEX IDX_ECA105F7F6B192E (id_classe_id), INDEX IDX_ECA105F7F24F7657 (id_parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, lieu VARCHAR(255) NOT NULL, inscription_requise TINYINT(1) NOT NULL, nombre_places INT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription_evenement (id INT AUTO_INCREMENT NOT NULL, evenement_id INT DEFAULT NULL, enfant_id INT DEFAULT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_AD33AA06FD02F13 (evenement_id), INDEX IDX_AD33AA06450D2529 (enfant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, id_ensg_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, coefficient INT NOT NULL, UNIQUE INDEX UNIQ_9014574A6C6E55B5 (nom), INDEX IDX_9014574A290232E1 (id_ensg_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere_eleve (matiere_id INT NOT NULL, eleve_id INT NOT NULL, INDEX IDX_6001AB82F46CD258 (matiere_id), INDEX IDX_6001AB82A6CC7B2 (eleve_id), PRIMARY KEY(matiere_id, eleve_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, quiz_id INT NOT NULL, score DOUBLE PRECISION NOT NULL, INDEX IDX_CFBDFA14A6CC7B2 (eleve_id), INDEX IDX_CFBDFA14853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, total DOUBLE PRECISION NOT NULL, date_ajout DATETIME NOT NULL, UNIQUE INDEX UNIQ_24CC0DF2727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_produit (panier_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_D31F28A6F77D927C (panier_id), INDEX IDX_D31F28A6F347EFB (produit_id), PRIMARY KEY(panier_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, stock INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, texte LONGTEXT NOT NULL, options JSON NOT NULL, reponse VARCHAR(255) NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, classe_id INT NOT NULL, matiere_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_ajout DATETIME NOT NULL, INDEX IDX_A412FA927ECF78B0 (cours_id), INDEX IDX_A412FA928F5EA509 (classe_id), INDEX IDX_A412FA92F46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_de_creation DATE NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_CE606404A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, nom VARCHAR(180) DEFAULT NULL, prenom VARCHAR(180) DEFAULT NULL, adresse VARCHAR(180) DEFAULT NULL, description VARCHAR(300) DEFAULT NULL, num_tel INT DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, etat_compte VARCHAR(255) DEFAULT NULL, date_naissance DATE DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_PhoneNumber (num_tel), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cessation ADD CONSTRAINT FK_4BC106779F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE classe_user ADD CONSTRAINT FK_9380A3AF8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classe_user ADD CONSTRAINT FK_9380A3AFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C51E6528F FOREIGN KEY (id_matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7F6B192E FOREIGN KEY (id_classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7F24F7657 FOREIGN KEY (id_parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscription_evenement ADD CONSTRAINT FK_AD33AA06FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE inscription_evenement ADD CONSTRAINT FK_AD33AA06450D2529 FOREIGN KEY (enfant_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A290232E1 FOREIGN KEY (id_ensg_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE matiere_eleve ADD CONSTRAINT FK_6001AB82F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matiere_eleve ADD CONSTRAINT FK_6001AB82A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA928F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cessation DROP FOREIGN KEY FK_4BC106779F37AE5');
        $this->addSql('ALTER TABLE classe_user DROP FOREIGN KEY FK_9380A3AF8F5EA509');
        $this->addSql('ALTER TABLE classe_user DROP FOREIGN KEY FK_9380A3AFA76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D727ACA70');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C51E6528F');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7F6B192E');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7F24F7657');
        $this->addSql('ALTER TABLE inscription_evenement DROP FOREIGN KEY FK_AD33AA06FD02F13');
        $this->addSql('ALTER TABLE inscription_evenement DROP FOREIGN KEY FK_AD33AA06450D2529');
        $this->addSql('ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A290232E1');
        $this->addSql('ALTER TABLE matiere_eleve DROP FOREIGN KEY FK_6001AB82F46CD258');
        $this->addSql('ALTER TABLE matiere_eleve DROP FOREIGN KEY FK_6001AB82A6CC7B2');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14A6CC7B2');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14853CD175');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2727ACA70');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F77D927C');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F347EFB');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA928F5EA509');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92F46CD258');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE cessation');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE classe_user');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE inscription_evenement');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE matiere_eleve');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_produit');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
