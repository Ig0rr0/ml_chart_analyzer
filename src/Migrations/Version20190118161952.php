<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190118161952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chart (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, x_name VARCHAR(255) NOT NULL, y_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point (id INT AUTO_INCREMENT NOT NULL, chart_id INT NOT NULL, x_position DOUBLE PRECISION NOT NULL, y_position DOUBLE PRECISION NOT NULL, predicted TINYINT(1) NOT NULL, INDEX IDX_B7A5F324BEF83E0A (chart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324BEF83E0A');
        $this->addSql('DROP TABLE chart');
        $this->addSql('DROP TABLE point');
    }
}
