<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "requests".
 *
 * @SWG\Definition(
 *     definition="Request",
 *     required={"name", "email", "message"},
 *     @SWG\Property(property="id", type="integer", description="Идентификатор заявки"),
 *     @SWG\Property(property="name", type="string", description="Имя пользователя"),
 *     @SWG\Property(property="email", type="string", description="Email пользователя"),
 *     @SWG\Property(property="status", type="string", description="Статус заявки"),
 *     @SWG\Property(property="message", type="string", description="Сообщение пользователя"),
 *     @SWG\Property(property="comment", type="string", description="Комментарий к заявке"),
 *     @SWG\Property(property="created_at", type="string", format="date-time", description="Дата создания"),
 *     @SWG\Property(property="updated_at", type="string", format="date-time", description="Дата обновления")
 * )
 */
class Request extends \yii\db\ActiveRecord
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
            [['name', 'email', 'message'], 'required'],
            [['status', 'message', 'comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status',
            'message' => 'Message',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {

        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }


        $this->updated_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert);
    }

}
