<?php

use yii\db\Migration;

class m170426_135502_add_alias extends Migration
{
    public function up()
    {
        $this->addColumn('tilda_pages', 'alias', $this->string());
    }

    public function down()
    {
        $this->dropColumn('tilda_pages', 'alias');
    }
}
