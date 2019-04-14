<?php
namespace alien\users\models\forms;

use alien\users\models\User;
use alien\users\models\UserProfile;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $sex;
    public $photo;
    public $firstname;
    public $middlename;
    public $lastname;
    public $locale;
    public $status;
    public $avatar_path;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            [['username', 'firstname', 'middlename', 'lastname'], 'required'],
            [['id', 'status'], 'integer'],
            [['username','avatar_path','firstname', 'middlename', 'lastname'], 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => '\alien\users\models\User', 'message' => Yii::t('users', 'THIS_USERNAME_ALREADY_TAKEN')],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\alien\users\models\User', 'message' => Yii::t('users', 'THIS_EMAIL_ALREADY_TAKEN')],
            ['sex', 'in', 'range' => [UserProfile::SEX_MALE, UserProfile::SEX_FEMALE]],
            ['photo', 'safe'],

            [['firstname', 'middlename', 'lastname'], 'string', 'max' => 255],
            ['locale', 'default', 'value' => Yii::$app->language],
            ['locale', 'in', 'range' => array_keys(Yii::$app->params['availableLocales'])],
            
            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'username' => Yii::t('users', 'USERNAME'),
            'email' => Yii::t('users', 'EMAIL'),
            'sex' => Yii::t('users', 'SEX'),
            'password' => Yii::t('users', 'PASSWORD'),
            'password_repeat' => Yii::t('users', 'PASSWORD_REPEAT'),
            'photo' => Yii::t('users', 'PHOTO'),
            'firstname' => Yii::t('common', 'Firstname'),
            'middlename' => Yii::t('common', 'Middlename'),
            'lastname' => Yii::t('common', 'Lastname'),
            'locale' => Yii::t('common', 'Locale'),
            'avatar_path' => Yii::t('common', 'Avatar Path'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->attributes = $this->attributes;
            $user->status = User::STATUS_NEW;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                $user_profile = new UserProfile;
                $user_profile->attributes = $this->attributes;
                $user_profile->user_id = $user->id;
                if($user_profile->save())
                    return $user;
            }else
            {
                var_dump($user->getErrors());
            }
        }

        return null;
    }

}
