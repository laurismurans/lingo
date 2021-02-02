<?php

namespace lingo\models;

use Yii;

class LingoSetting extends \app\components\ActiveRecord {

    public static function tableName() {
        return 'lingo_setting';
    }

    public function rules() {
        return [
            [['code', 'name', 'type', 'value'], 'required'],
            [['code', 'name', 'type', 'value'], 'string'],
            [['updated_at'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Code'),
            'code' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }
    
    public static function get($code) {
        return self::findOne(['code' => $code])->value;
    }
        
    public static function set($code, $value) {
        $result = false;
        $model = self::get($code);
        $model->value = $value;
        $model->updated_at = time();
        
        if ($model->save()) {
            $result = true;
        }
        
        return $result;
    }
}
    

    
    
