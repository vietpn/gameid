<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 04/04/2016
 * Time: 11:00
 */

namespace api\modules\v1\controllers;

use api\controllers\BaseAPIController;
use common\models\GameIcashHistory;
use Faker\Provider\DateTime;
use Yii;

class GameIcashHistoryController extends BaseAPIController
{

    public $modelClass = 'common\models\GameIcashHistory';
    // filter field api
    public $filterFields = ['id', 'game_id', 'icash_change', 'icash', 'created_at', 'deal_status', 'yotel_id'];

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['delete']);
        unset($actions['create']);
        //unset($actions['update']);

        return $actions;
    }

    public function actionDrawnGraph()
    {
        $params = Yii::$app->request->post();
        if (empty($params['start_time']) ||
            empty($params['end_time'])
        ) {
            return [
                'code' => 422,
                'status' => 422,
                'message' => 'start_time, end_time is not blank'
            ];
        }
        $list_month =[];
        for($i=strtotime($params['start_time']); $i<strtotime($params['end_time']); $i+=86400)
        {
            $list[] = date('Y-m-d', $i);
        }
        if(count($list)<31){
            $model = GameIcashHistory::find()->andWhere(['>=', 'created_at', $params['start_time']])
                ->andWhere(['<=', 'created_at', $params['end_time']])->orderBy('created_at asc');
            $user =  GameIcashHistory::find()->select('count(game_id) as user,created_at')->andWhere(['>=', 'created_at', $params['start_time']])
                ->andWhere(['<=', 'created_at', $params['end_time']])->groupBy('created_at')->orderBy('created_at asc');
            $rawUser =$user->asArray()->all();
        }elseif(count($list)>31){

            $model = GameIcashHistory::find()->andWhere(['>=', "DATE_FORMAT(created_at,'%Y-%m')", date('Y-m',strtotime($params['start_time']))])
                ->andWhere(['<=', 'DATE_FORMAT(created_at,"%Y-%m")', date('Y-m',strtotime($params['end_time']))])->orderBy('created_at asc');
            $user =  GameIcashHistory::find()->select('count(game_id) as user,created_at')->andWhere(['>=', "DATE_FORMAT(created_at,'%Y-%m')", date('Y-m',strtotime($params['start_time']))])
                ->andWhere(['<=', "DATE_FORMAT(created_at,'%Y-%m')", date('Y-m',strtotime($params['end_time']))])->groupBy(["DATE_FORMAT(created_at,'%Y-%m')"])->orderBy('created_at asc');
            $rawUser =$user->asArray()->all();
        }
        // var_dump($rawUser);die();
        //search query
        $i = date("Y-m", strtotime($params['start_time']));
        while($i <= date("Y-m", strtotime($params['end_time']))){
            array_push($list_month,$i);
            if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
            else
                $i++;
        }
        // format data
        $rawData = $model->all();
        $data = [];
        $date_key = [];
        foreach ($rawData as $obj) {
            if(count($list)>31){
                $date = date('Y-m',strtotime($obj->created_at));
            }else{
                $date = date('Y-m-d',strtotime($obj->created_at));
            }
            if (!isset($data[$date])) {
                $data[$date]['receive'] = 0;
                $data[$date]['transfer'] = 0;
            }
            if ($obj->icash_change > 0) {
                $data[$date]['receive'] += $obj->icash;
            } else {
                $data[$date]['transfer'] += $obj->icash;
            }
            array_push($date_key,$date);
        }
        foreach ($data as $key => $value){
            foreach ($rawUser as $k => $v){
                if(count($list)>31){
                    $date = date('Y-m',strtotime($v['created_at']));

                }else {
                    $date = date('Y-m-d', strtotime($v['created_at']));
                }
                if($key == $date){
                    $data[$key]['user'] = (int)$v['user'];
                }
            }
        }
        $data_list=[];
        if(count($list)<31){
            foreach ($list as $key => $value){
                if(in_array($value,$date_key)){
                    $data_list[$value]=$data[$value];
                }else{
                    $data_list[$value] = [
                        "receive"=> 0,
                        "transfer"=> 0,
                        "user"=> 0
                    ];
                }
            }
        }else{
            foreach ($list_month as $key => $value){
                if(in_array($value,$date_key)){
                    $data_list[$value]=$data[$value];
                }else{
                    $data_list[$value] = [
                        "receive"=> 0,
                        "transfer"=> 0,
                        "user"=> 0
                    ];
                }
            }
        }
        return ['items' => $data_list];
    }
    public function actionGetTopMecharnt()
    {
        $params = Yii::$app->request->queryParams;
        if (empty($params)) {
            return [
                'code' => 422,
                'status' => 422,
                'message' => 'params is not blank'
            ];
        }
        $game_id = [];
        $game_id = explode(',', $params['game_id']);
        $startTime = $params['startTime'];
        $endTime = $params['endTime'];
        if (empty($game_id) || empty($startTime) || empty($endTime)) {
            return [
                'code' => 422,
                'status' => 422,
                'message' => 'start_time, end_time is not blank'
            ];
        }
        $game_icash_history = GameIcashHistory::find()->select(['game_id', 'count(game_id) as tongkhachhang'])->where(['game_id' => $game_id])->andWhere(['>=', 'created_at', $startTime])
            ->andWhere(['<=', 'created_at', $endTime])->groupBy('game_id')->orderBy('count(game_id) desc')->asArray()->all();
        if (count($game_icash_history) > 0):
            foreach ($game_icash_history as $key => $value):
                $game_icash_history[$key]['game_id'] = $value['game_id'];
                $game_icash_history[$key]['tongkhachhang'] = $value['tongkhachhang'];
                $game_icash_history[$key]['id'] = $key + 1;
                $item_chuyen = GameIcashHistory::find()->select('sum(icash) as chuyen')->where(['game_id' => $value['game_id']])->andWhere(['icash_change' => '-100000'])->andWhere(['>=', 'created_at', $startTime])
                    ->andWhere(['<=', 'created_at', $endTime])->asArray()->all();
                $game_icash_history[$key]['chuyen'] = $item_chuyen[0]['chuyen'];
                $item_nhan = GameIcashHistory::find()->select('sum(icash) as nhan')->where(['game_id' => $value['game_id']])->andWhere(['icash_change' => '100000'])->andWhere(['>=', 'created_at', $startTime])
                    ->andWhere(['<=', 'created_at', $endTime])->asArray()->all();
                $game_icash_history[$key]['nhan'] = $item_nhan[0]['nhan'];
                $game_icash_history[$key]['tong'] = $item_chuyen[0]['chuyen'] + $item_nhan[0]['nhan'];
            endforeach;
        endif;
        return ['items' => $game_icash_history];
    }

    public function actionGetTopCustom()
    {
        $params = Yii::$app->request->queryParams;
        if (empty($params)) {
            return [
                'code' => 422,
                'status' => 422,
                'message' => 'params is not blank'
            ];
        }
        $game_id = explode(',', $params['game_id']);
        $startTime = $params['startTime'];
        $endTime = $params['endTime'];
        if (empty($game_id) || empty($startTime) || empty($endTime)) {
            return [
                'code' => 422,
                'status' => 422,
                'message' => 'game_id, start_time, end_time is not blank'
            ];
        }
        $item_chuyen = GameIcashHistory::find()->select('game_id,count(icash_change) as solanchuyen,sum(icash) as chuyen')->where(['game_id' => $game_id])->andWhere(['icash_change' => '-100000'])->andWhere(['>=', 'created_at', $startTime])
            ->andWhere(['<=', 'created_at', $endTime])->groupBy('icash_change')->asArray()->all();

        return ['items' => $item_chuyen];
    }
}