<?php

namespace globus\tilda\models;

use Yii;

/**
 * This is the model class for table "tilda_styles".
 *
 * @property integer $id
 * @property integer $tilda_page_id
 * @property string $path
 * @property string $name
 *
 * @property TildaPage $tildaPage
 */
class TildaStyle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tilda_styles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tilda_page_id'], 'integer'],
            [['path', 'name', 'source_url'], 'string', 'max' => 255],
            [['tilda_page_id'], 'exist', 'skipOnError' => true, 'targetClass' => TildaPage::className(), 'targetAttribute' => ['tilda_page_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tilda_page_id' => Yii::t('app', 'Tilda Page ID'),
            'path' => Yii::t('app', 'Path'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTildaPage()
    {
        return $this->hasOne(TildaPage::className(), ['id' => 'tilda_page_id']);
    }
}
