<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $partner_code
 * @property string $provider_code
 * @property string $ref_code
 * @property string $screen_name
 * @property string $fullname
 * @property string $avatar
 * @property string $address
 * @property string $email
 * @property string $email_token
 * @property integer $email_token_expire
 * @property integer $email_status
 * @property string $birthday
 * @property integer $birthyear
 * @property integer $gender
 * @property string $passport
 * @property string $phone_number
 * @property string $client_version
 * @property string $platform
 * @property string $os_type
 * @property integer $login_times
 * @property string $last_login
 * @property string $last_login_ip_addr
 * @property string $date_created
 * @property string $date_modified
 * @property integer $status
 * @property integer $ncoin
 * @property integer $vcoin
 * @property integer $otp_status
 * @property integer $game_id
 * @property integer $is_partner
 */
class Account extends BaseModel
{
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_COIN = 'coin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'partner_code'], 'required'],
            [['birthday', 'last_login', 'date_created', 'date_modified'], 'safe'],
            [['birthyear', 'gender', 'login_times', 'status', 'ncoin', 'vcoin', 'otp_status', 'is_partner', 'game_id'], 'integer'],
            [['username', 'partner_code', 'client_version', 'last_login_ip_addr'], 'string', 'max' => 50],
            [['password_hash', 'email', 'email_token'], 'string', 'max' => 150],
            [['provider_code'], 'string', 'max' => 10],
            [['ref_code'], 'string', 'max' => 128],
            [['screen_name', 'platform'], 'string', 'max' => 100],
            [['fullname', 'os_type'], 'string', 'max' => 512],
            [['avatar'], 'string', 'max' => 250],
            [['address', 'passport'], 'string', 'max' => 200],
            [['phone_number'], 'string', 'max' => 20],
            [['username'], 'unique'],
            // email and phone number are unique
            [['email', 'phone_number'], 'unique'],
            ['email', 'email'],
            // birthday format with pattern
            ['birthday', 'date', 'format' => 'yyyy-M-d H:m:s'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'partner_code' => 'Partner Code',
            'provider_code' => 'Provider Code',
            'ref_code' => 'Ref Code',
            'screen_name' => 'Screen Name',
            'fullname' => 'Fullname',
            'avatar' => 'Avatar',
            'address' => 'Address',
            'email' => 'Email',
            'email_token' => 'Email Token',
            'email_token_expire' => 'Email Token Expire',
            'email_status' => 'Email Status',
            'birthday' => 'Birthday',
            'birthyear' => 'Birthyear',
            'gender' => 'Gender',
            'passport' => 'Passport',
            'phone_number' => 'Phone Number',
            'client_version' => 'Client Version',
            'platform' => 'Platform',
            'os_type' => 'Os Type',
            'login_times' => 'Login Times',
            'last_login' => 'Last Login',
            'last_login_ip_addr' => 'Last Login Ip Addr',
            'date_created' => 'Date Created',
            'date_modified' => 'Date Modified',
            'status' => 'Status',
            'ncoin' => 'Ncoin',
            'vcoin' => 'Vcoin',
            'otp_status' => 'Otp Status',
            'game_id' => 'Game ID',
            'is_partner' => 'Is Partner',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $attributes = array_keys($this->attributeLabels());
        // Scenario Values Only Accepted
        $scenarios[Account::SCENARIO_UPDATE] = array_diff($attributes, ['username', 'ncoin', 'vcoin', 'password_hash', 'email', 'phone_number']);
        $scenarios[Account::SCENARIO_COIN] = ['vcoin', 'ncoin'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_created', 'date_modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_modified'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['password_hash']);

        // format fields
        if (isset($fields['birthday'])) {
            $fields['birthday'] = function ($model) {
                $tmpDate = new \DateTime($model->birthday);
                return $tmpDate->format('Y-m-d');
            };
        }

        return $fields;
    }

    /**
     * Using for create new account
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('password_hash')) {
                $this->password_hash = password_hash($this->getAttribute('password_hash'), PASSWORD_DEFAULT);
            }
            if ($this->isAttributeChanged('birthday')) {
                try {
                    $tmpDate = new \DateTime($this->getAttribute('birthday'));
                    $this->birthyear = $tmpDate->format('Y');
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                }
            }
            //$this->date_modified = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function sendEmailVerification()
    {
        if (empty($this->email)) {
            return false;
        }

        try {
            $this->email_token = sha1(uniqid($this->username, true));
            $this->email_token_expire = $_SERVER["REQUEST_TIME"];
            $this->email_status = Yii::$app->params['status_fail'];

            if (!$this->update()) {
                return false;
            }
            Yii::$app->mailer->compose(
                ['html' => 'emailVerificationToken-html', 'text' => 'emailVerificationToken-text'],
                ['user' => $this])
                ->setFrom(Yii::$app->params['email']['support'])
                ->setTo($this->email)
                ->setSubject('Active account')
                ->send();
            return true;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function uploadAvatar($file)
    {
        try{
            $postdata = fopen($file->tempName, "r");

            /* Get file extension */
            $extension = substr($file->name, strrpos($file->name, '.'));

            /* Generate unique name */
            $filename = uniqid() . $extension;

            /* Open a file for writing */
            $dir = Yii::$app->params['upload_full_path'];
            $baseDir = Yii::$app->params['upload_base_dir']['account']['avatar'] . date('Y/m/d/H') . "/";

            $saveDir = $dir . $baseDir;
            $baseDir = str_replace("\\\\", "/", $baseDir);
            $saveDir = str_replace("\\\\", "/", $saveDir);


            if (!file_exists($saveDir)) {
                mkdir($saveDir, 755, true);
            }

            $fp = fopen($saveDir . $filename, "w");

            /* Read the data 1 KB at a time and write to the file */
            while ($data = fread($postdata, 1024))
                fwrite($fp, $data);

            /* Close the streams */
            fclose($fp);
            fclose($postdata);

            /* the result object that is sent to client*/
            $this->avatar = $baseDir . $filename;

            return ($this->update()) ? true : false;
        }catch (Exception $e){
            Yii::error($e->getMessage());
            return false;
        }
    }
    /*
     * get ditrict provider code
     */
    public static function getProviderCode(){
        $provider_code = Account::find()->select('provider_code')->distinct()->where(['not', ['provider_code' => null]])->orderBy('provider_code desc')->all();
        return $provider_code;
    }
}
