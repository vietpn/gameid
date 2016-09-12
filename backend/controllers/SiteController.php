<?php
namespace backend\controllers;

use backend\models\AccountSearch;
use common\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'setting'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {

        $searchModel = new AccountSearch();
        $params = Yii::$app->request->queryParams;
        $sess = Yii::$app->session;
        if (!isset($params[$searchModel->formName()]['startTime'])) {
            $previousWeek = strtotime(date('Y-m-1'));
            $searchModel['startTime'] = date('d-m-Y', $previousWeek);
        }
        if (!isset($params[$searchModel->formName()]['endTime'])) {
            $searchModel['endTime'] = date('d-m-Y');
        }
        //set percent
        if (!isset($params[$searchModel->formName()]['cnc'])) {
            $searchModel['cnc'] = \Yii::$app->params['gdc_total']['cnc'];
        }
        if (!isset($params[$searchModel->formName()]['one_pay'])) {
            $searchModel['one_pay'] = \Yii::$app->params['gdc_total']['one_pay'];
        }
        if (!isset($params[$searchModel->formName()]['yotel'])) {
            $searchModel['yotel'] = \Yii::$app->params['gdc_total']['yotel'];
        }
        if (!isset($params[$searchModel->formName()]['cps'])) {
            $searchModel['cps'] = \Yii::$app->params['gdc_total']['cps'];
        }
        if ($sess->has('cur_start_time')) {
            $searchModel->startTime = $sess->get('cur_start_time');
        }
        if ($sess->has('cur_end_time')) {
            $searchModel->endTime = $sess->get('cur_end_time');
        }
        $dataProvider = $searchModel->searchProviderCode($params);
        $dataProviderSales = $searchModel->searchTotalRevenue($params);
        //dự đoán doanh thu
        $expectedRevenue = $searchModel->ExpectedRevenue();
        return $this->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataProviderSales' => $dataProviderSales,
                'expectedRevenue'   =>  $expectedRevenue,
            ]);
    }

    /**
     * Action chuyển ngôn ngữ trên Menu
     */
    public function actionSetting() {
        $lang = Yii::$app->request->get('lang');
        if (!empty($lang)) {
            Yii::$app->language = $lang;

            $languageCookie = new Cookie([
                'name' => 'language',
                'value' => $lang,
                'expire' => time() + 60 * 60 * 24 * 30, // 30 days
            ]);
            Yii::$app->response->cookies->add($languageCookie);
        }

        $this->redirect(['/site/index']);

    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
