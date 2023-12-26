<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "requests".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $message
 * @property string $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Requests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'status', 'message'], 'required'],
            [['status', 'message', 'comment'], 'string'],
            [['email'], 'email'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => ['Active', 'Resolved']],
            [['name', 'email'], 'string', 'max' => 255],
            [['comment'], 'required', 'when' => function ($model) {
                return $model->status == 'Resolved';
            }, 'whenClient' => "function (attribute, value) {
                return $('#requests-status').val() == 'Resolved';
            }", 'message' => 'Comment is required when the request is resolved.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'message' => Yii::t('app', 'Message'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return RequestsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequestsQuery(get_called_class());
    }
}
