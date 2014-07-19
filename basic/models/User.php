<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $email
 * @property integer $is_enabled
 * @property string $name
 * @property string $token
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['is_enabled'], 'integer', 'min'=>0, 'max'=>1],
            [['email', 'name', 'token'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['token'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'is_enabled' => 'Is Enabled',
            'name' => 'Name',
            'token' => 'Token',
        ];
    }

    public function getUsername()
    {
        if (!empty($this->name)) return $this->name;
        else return $this->email;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $user = User::findOne($id);
        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token)
    {
        $user = User::findOne([
            'token' => $token,
        ]);
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return md5($this->id);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return md5($this->id) === $authKey;
    }

    /**
     * @param $email
     * @return User
     */
    public static function findByEmail($email)
    {
        $user = User::findOne([
            'email' => $email,
        ]);
        return $user;
    }

    public static function createUser($email)
    {
        $user = new User;
        $user->email = $email;
        $user->token = md5(mt_rand());
        return $user->save();
    }

    public function clearToken()
    {
        $this->token = null;
        return $this->save(false, ['token']);
    }
}
