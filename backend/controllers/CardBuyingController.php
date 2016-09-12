<?php

namespace backend\controllers;

use Yii;
use common\models\CardBuying;
use backend\models\CardBuyingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CardBuyingController implements the CRUD actions for CardBuying model.
 */
class CardBuyingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CardBuying models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CardBuyingSearch();
        $params = Yii::$app->request->queryParams;
        $sess = Yii::$app->session;

        if (!isset($params[$searchModel->formName()]['startTime'])) {
            $previousWeek = strtotime('-1 week');
            $searchModel['startTime'] = date('d-m-Y', $previousWeek);
        }
        if (!isset($params[$searchModel->formName()]['endTime'])) {
            $searchModel['endTime'] = date('d-m-Y');
        }

        if ($sess->has('cur_start_time')) {
            $searchModel->startTime = $sess->get('cur_start_time');
        }
        if ($sess->has('cur_end_time')) {
            $searchModel->endTime = $sess->get('cur_end_time');
        }
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CardBuying model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CardBuying model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new CardBuying();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing CardBuying model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing CardBuying model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the CardBuying model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CardBuying the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CardBuying::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
