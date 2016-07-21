<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wx_reply".
 *
 * @property integer $g_id
 * @property integer $w_id
 * @property string $g_rule
 * @property string $g_reply
 * @property integer $g_type
 */
class WxReply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_reply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['w_id', 'g_type'], 'integer'],
            [['g_rule'], 'string', 'max' => 100],
            [['g_reply'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'g_id' => 'G ID',
            'w_id' => 'W ID',
            'g_rule' => 'G Rule',
            'g_reply' => 'G Reply',
            'g_type' => 'G Type',
        ];
    }
}

