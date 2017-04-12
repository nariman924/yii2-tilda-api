<?php

use yii\db\Migration;

class m170412_115232_create_tilda_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('tilda_pages', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'project_id' => $this->integer(),
            'published' => $this->boolean(),
            'title' => $this->string(),
            'html' => $this->text(),
        ], $tableOptions);

        $this->createTable('tilda_styles', [
            'id' => $this->primaryKey(),
            'tilda_page_id' => $this->integer(),
            'source_url' => $this->string(),
            'path' => $this->string(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->createTable('tilda_scripts', [
            'id' => $this->primaryKey(),
            'tilda_page_id' => $this->integer(),
            'source_url' => $this->string(),
            'path' => $this->string(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->createTable('tilda_images', [
            'id' => $this->primaryKey(),
            'tilda_page_id' => $this->integer(),
            'source_url' => $this->string(),
            'path' => $this->string(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_tilda_styles',
            'tilda_styles',
            'tilda_page_id',
            'tilda_pages',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk_tilda_scripts',
            'tilda_scripts',
            'tilda_page_id',
            'tilda_pages',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk_tilda_images',
            'tilda_images',
            'tilda_page_id',
            'tilda_pages',
            'id',
            'cascade',
            'cascade'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_tilda_styles', 'tilda_styles');
        $this->dropForeignKey('fk_tilda_scripts', 'tilda_scripts');
        $this->dropForeignKey('fk_tilda_images', 'tilda_images');

        $this->dropTable('tilda_pages');
        $this->dropTable('tilda_styles');
        $this->dropTable('tilda_scripts');
        $this->dropTable('tilda_images');
    }
}
