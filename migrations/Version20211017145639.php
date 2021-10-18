<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211017145639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_527EDB25EA1EBC33');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, todo_id, taskname FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, todo_id INTEGER NOT NULL, user_id INTEGER NOT NULL, taskname VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_527EDB25EA1EBC33 FOREIGN KEY (todo_id) REFERENCES todo (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, todo_id, taskname) SELECT id, todo_id, taskname FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25EA1EBC33 ON task (todo_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
        $this->addSql('DROP INDEX IDX_5A0EB6A0A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, user_id, todoname FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, todoname VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_5A0EB6A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO todo (id, user_id, todoname) SELECT id, user_id, todoname FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
        $this->addSql('CREATE INDEX IDX_5A0EB6A0A76ED395 ON todo (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_527EDB25EA1EBC33');
        $this->addSql('DROP INDEX IDX_527EDB25A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, todo_id, taskname FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, todo_id INTEGER NOT NULL, taskname VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO task (id, todo_id, taskname) SELECT id, todo_id, taskname FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25EA1EBC33 ON task (todo_id)');
        $this->addSql('DROP INDEX IDX_5A0EB6A0A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, user_id, todoname FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, todoname VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO todo (id, user_id, todoname) SELECT id, user_id, todoname FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
        $this->addSql('CREATE INDEX IDX_5A0EB6A0A76ED395 ON todo (user_id)');
    }
}
