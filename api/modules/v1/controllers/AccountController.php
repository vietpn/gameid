<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 04/04/2016
 * Time: 11:00
 */

namespace api\modules\v1\controllers;

use api\models\UploadImgForm;
use common\models\CardBuying;
use common\models\GameIcashHistory;
use common\models\SmsbulkBrandname;
use common\models\TopupBuying;
use Yii;
use common\models\Account;
use common\models\CardCharching;
use common\models\CardCharging;
use common\models\Pay365;
use common\models\SocialAccount;
use api\controllers\BaseAPIController;
use yii\web\UploadedFile;

class AccountController extends BaseAPIController
{

    public $modelClass = 'common\models\Account';
    // filter field api
    public $filterFields = ['username', 'email', 'phone_number', 'id', 'game_id'];

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['delete']);
        unset($actions['update']);

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function actionUpdate($id)
    {
        $params = Yii::$app->request->post();
        Yii::info($params, Yii::$app->params['key_log']);

        $account = Account::findOne($id);
        if (empty($account)) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'account not exist')
            ];
        }

        $account['scenario'] = Account::SCENARIO_UPDATE;
        $account->attributes = $params;
        if (!$account->validate() || !$account->save()) {
            Yii::$app->response->statusCode = 422;
            Yii::info($account->errors, Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $account->errors
            ];
        }

        Yii::info($account, Yii::$app->params['key_log']);
        return $account;
    }

    /**
     * Check login to server GDC
     * @return array
     */
    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        if (!isset($username) || !isset($password)) {
            Yii::$app->response->statusCode = 200;
            return [
                'code' => 104,
                'status' => 422,
                'message' => Yii::t('account', 'username or password is not blank')
            ];
        }

        $model = Account::find()->where(['username' => $username]);
        if (!$model->exists()) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 105,
                'status' => 422,
                'message' => Yii::t('account', 'username not exist')
            ];
        }

        $password_hash = $model->all()[0]->getAttribute('password_hash');
        if (empty($password_hash) || !Yii::$app->getSecurity()->validatePassword($password, $password_hash)) {
            Yii::$app->response->statusCode = 200;
            return [
                'code' => 106,
                'status' => 422,
                'message' => Yii::t('account', 'username or password is not correct')
            ];
        }

        return $model->all()[0];
    }

    /**
     * @return array|CardCharging
     */
    public function actionCardCharging()
    {
        $params = Yii::$app->request->post();
        $params['account_id'] = Yii::$app->request->queryParams['id'];
        Yii::info($params, Yii::$app->params['key_log_card_charging']);

        $model = new CardCharging();
        $model->attributes = $params;

        if (!$model->chargingPay365()) {
            // if have error
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log_card_charging']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        Yii::info($model, Yii::$app->params['key_log_card_charging']);
        return $model;
    }

    /**
     * Card charing with out account_id
     * @return array|CardChargingNoAcc
     */
    public function actionCardChargingNoAcc()
    {
        $params = Yii::$app->request->post();
        Yii::info($params, Yii::$app->params['key_log_card_charging']);

        $model = new CardCharging();
        $model->attributes = $params;

        if (!$model->chargingPay365NoId()) {
            // if have error
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log_card_charging']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }
        Yii::info($model, Yii::$app->params['key_log_card_charging']);
        return $model;
    }

    /**
     * @return array|CardBuying
     */
    public function actionCardBuying()
    {
        $params = Yii::$app->request->post();
        $params['account_id'] = Yii::$app->request->queryParams['id'];
        Yii::info($params, Yii::$app->params['key_log_card_buying']);

        $model = new CardBuying();
        $model->attributes = $params;

        if (!$model->buyingPay365()) {
            // if have error
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log_card_buying']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        Yii::info($model, Yii::$app->params['key_log_card_buying']);
        return $model;
    }

    /**
     * Tra thuong the khong can account_id
     * @return array|CardBuying
     */
    public function actionCardBuyingNoAcc()
    {
        $params = Yii::$app->request->post();
        Yii::info($params, Yii::$app->params['key_log_card_buying']);

        $model = new CardBuying();
        $model->attributes = $params;

        if (!$model->buyingPay365NoAcc()) {
            // if have error
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log_card_buying']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        Yii::info($model, Yii::$app->params['key_log_card_buying']);
        return $model;
    }

    /**
     * @return array|TopupBuying
     */
    public function actionTopupBuying()
    {
        $params = Yii::$app->request->post();
        $params['account_id'] = Yii::$app->request->queryParams['id'];
        Yii::info($params, Yii::$app->params['key_log']);

        $model = new TopupBuying();
        $model->attributes = $params;

        if (!$model->buyingPay365()) {
            // if have error
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        Yii::info($model, Yii::$app->params['key_log']);
        return $model;
    }

    /**
     * Update ncoin and vcoin
     * @return array|null|static
     */
    public function actionCoin()
    {
        $params = Yii::$app->request->post();
        $account_id = Yii::$app->request->queryParams['id'];

        $account = Account::findOne($account_id);
        if (!isset($account)) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'account not exist')
            ];
        }

        $account['scenario'] = Account::SCENARIO_COIN;
        $account->attributes = $params;
        if (!$account->validate() || !$account->save()) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => $account->errros()
            ];
        }

        return $account;

    }

    /**
     * Login by social network
     * @return array|SocialAccount|\yii\db\ActiveRecord
     */
    public function actionSocialLogin()
    {
        $params = $_REQUEST;
        Yii::info($params, Yii::$app->params['key_log']);

        $model = new SocialAccount();
        $model->attributes = $params;

        if (!isset($params['provider']) || !isset($params['client_id'])) {
            Yii::$app->response->statusCode = 422;
            Yii::info(Yii::t('account', 'provider and client_id ís not blank'), Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'provider and client_id ís not blank')
            ];
        }

        // if social account is existing
        $social_account = SocialAccount::find()->where([
            'provider' => $params['provider'],
            'client_id' => $params['client_id']]);
        if ($social_account->exists()) {
            Yii::info($social_account->all()[0], Yii::$app->params['key_log']);
            return $social_account->all()[0];
        }


        // create new social account
        if (!$model->validate() || !$model->save()) {
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        Yii::info($model, Yii::$app->params['key_log']);
        return $model;
    }

    /**
     * Upload avatar for account
     * @return array|null|static
     */
    public function actionUploadAvatar()
    {
        $account_id = Yii::$app->request->queryParams['id'];

        $account = Account::findOne($account_id);
        if (!isset($account)) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'account not exist')
            ];
        }

        $form = new UploadImgForm();
        $form->image_file = UploadedFile::getInstanceByName('image_file');

        if (!$form->validate()) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => $form->errors
            ];
        }

        if (!$account->uploadAvatar($form->image_file)) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => $account->errors
            ];
        }

        return $account;
    }

    /**
     * Send email verification for acc
     * @return array
     */
    public function actionEmailVerification()
    {
        $account_id = Yii::$app->request->queryParams['id'];
        $account = Account::findOne($account_id);

        if (empty($account)) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'account not exist')
            ];
        }

        if (!$account->sendEmailVerification()) {
            Yii::$app->response->statusCode = 422;
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'Unknown error')
            ];
        }

        Yii::$app->response->statusCode = 200;
        return [
            'code' => 200,
            'status' => 200,
        ];
    }

    /**
     * Reset password of account
     */
    public function actionResetPassword()
    {
        $game_id = Yii::$app->request->queryParams['game_id'];

        //$account = Account::findOne($account_id);
        $account = Account::find()->where(['game_id' => $game_id]);

        if (!$account->exists()) {
            Yii::$app->response->statusCode = 422;
            Yii::info(Yii::t('account', 'game_id not exist'), Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => Yii::t('account', 'game_id not exist')
            ];
        }

        $password = Yii::$app->request->post('password');
        if (empty($password)) {
            Yii::$app->response->statusCode = 422;
            Yii::info(Yii::t('account', 'password is not blank'), Yii::$app->params['key_log']);
            return [
                'code' => 104,
                'status' => 422,
                'message' => Yii::t('account', 'password is not blank')
            ];
        }

        $updateAcc = $account->all()[0];
        $updateAcc->password_hash = $password;

        if (!$updateAcc->validate() || !$updateAcc->save()) {
            Yii::info($updateAcc->errors, Yii::$app->params['key_log']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $updateAcc->errors
            ];
        }

        Yii::info($updateAcc, Yii::$app->params['key_log']);
        return $updateAcc;
    }

    /**
     * Send Sms bulk brand name
     */
    public function actionSmsbulkBrandname()
    {
        $params = Yii::$app->request->post();
        Yii::info($params, Yii::$app->params['key_log_smsbulk_brandname']);

        $model = new SmsbulkBrandname();
        $model->attributes = $params;
        $model->status = 0;

        if (!$model->validate() || !$model->save()) {
            Yii::$app->response->statusCode = 422;
            Yii::info($model->errors, Yii::$app->params['key_log_smsbulk_brandname']);
            return [
                'code' => 422,
                'status' => 422,
                'message' => $model->errors
            ];
        }

        $model->addToQueue();
        Yii::info($model, Yii::$app->params['key_log_smsbulk_brandname']);
        return $model;
    }


    /**
     * get Icash history
     */
    public function actionIcashHistory()
    {
        $game_id = Yii::$app->request->queryParams['game_id'];

        $gameIcashHistory = GameIcashHistory::find()->where(['game_id' => $game_id]);

        // kiem tra ton tai iCash History
        if (!$gameIcashHistory->exists()) {
            //Yii::$app->response->statusCode = 422;
            //Yii::info(Yii::t('account', 'game_id not exist'), Yii::$app->params['key_log']);
            return [];
        }

        // return all history
        return $gameIcashHistory->all();
    }


}