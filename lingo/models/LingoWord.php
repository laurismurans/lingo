<?php

namespace lingo\models;

use Yii;

class LingoWord extends \app\components\ActiveRecord
{

    public static function tableName()
    {
        return 'lingo_word';
    }
    
    public function rules() {
        return [
            [['name', 'length'], 'required'], // Length is for quality of life. Easier to work with data. Alternative would be querying length in the SQL.
            [['name'], 'string', 'max' => 255],
            [['length'], 'integer'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Code'),
            'length' => Yii::t('app', 'Name'),
        ];
    }
    
    public static function getRandomWord($length = 5, $guessed_words = []) {
        return self::find()
            ->where(['length' => $length])
            ->andWhere(['NOT', ['name' => $guessed_words]])
            ->orderBy(new \yii\db\Expression('rand()'))
            ->one()
        ;
    }
    
    

}
