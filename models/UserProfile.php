<?php

namespace alien\users\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property string $locale
 * @property integer $gender
 *
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'middlename', 'lastname'],'required'],
            [['sex'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'avatar_path', 'avatar_base_url'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['sex', 'in', 'range' => [self::SEX_MALE, self::SEX_FEMALE]],
            ['locale', 'default', 'value' => Yii::$app->language],
            ['locale', 'in', 'range' => array_keys(Yii::$app->params['availableLocales'])]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('common', 'User ID'),
            'firstname' => Yii::t('common', 'Firstname'),
            'middlename' => Yii::t('common', 'Middlename'),
            'lastname' => Yii::t('common', 'Lastname'),
            'avatar_path' => Yii::t('common', 'Avatar Path'),
            'avatar_base_url' => Yii::t('common', 'Avatar Base Url'),
            'locale' => Yii::t('common', 'Locale'),
            'sex' => Yii::t('common', 'Sex'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
        /**
     * @return null|string
     */
    public function getFullName()
    {
        if ($this->firstname || $this->lastname) {
            return implode(' ', [$this->firstname, $this->lastname]);
        }
        return null;
    }

    public function getDefaultPhoto()
    {
        return ($this->sex == self::SEX_MALE) ? 'male' : 'female';
    }
    
    public static function getSexArray()
    {
        return [
            self::SEX_MALE => Yii::t('backend', 'Male'),
            self::SEX_FEMALE => Yii::t('backend', 'Female'),
        ];
    }
    
    public function getFIO()
    {
        if ($this->firstname || $this->lastname) {
            return implode(' ', [$this->lastname, $this->firstname, $this->middlename]);
        }
        return null;
    }
    
    public function getShortFIO ()
    {
        if ($this->firstname || $this->lastname) {
            return $this->lastname." ".substr($this->firstname, 0, 2).". ".substr($this->middlename, 0, 2).".";
        }
        return null;
    }
}