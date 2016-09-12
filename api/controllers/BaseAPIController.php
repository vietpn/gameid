<?php
namespace api\controllers;

//use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
//use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\Response;


/**
 * Author Controller API
 */
class BaseAPIController extends ActiveController
{
    public $query;

    public $limit = 20; // total item per page
    public $offset = 0;
    public $fields = [];
    public $page = 1; // current page

    public $maxItemInPage = 100; // maximum items in page

    public $collectionEnvelope = "items";
    public $metaEnvelope = "_meta";

    //public $defaultContentType = "application/json; charset=utf-8";

    public $sort = "";
    public $whiteListSortFields = []; // cac trường được phép sort

    public $filterFields = []; // các trường được phép filter

    /**
     * Filter:
     *      - filters[]=<field_name>+<operator>+<params>
     *      - Operators: gt, gte, lt, lte, ne, between, include, exclude
     *        between và in thì tham số/params sẽ phân cách bởi dấu phẩy
     *        Ex:
     *           filters[]=age+beetween+3,4&filters[]=weight+beetween+30,80
     */
    public $filters = []; // mảng chứa các filter query

    // cache control
    public $cacheIndexPage = 300; // 5 phut - cache trang index
    public $cacheViewPage = 1800; // 30 phut - cache trang view detail

    // lay theo nhieu
    public $ids = [];

    // tên table đang sử dụng cho active query
    public $tableName; // dùng trong trường hợp join bảng

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['index']);
        //unset($actions['create'], $actions['delete'], $actions['update']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // https://github.com/githubjeka/yii2-rest
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ['class' => HttpBearerAuth::className()],
                ['class' => QueryParamAuth::className(), 'tokenParam' => 'accessToken'],
            ]
        ];

        /*$behaviors['exceptionFilter'] = [
            'class' => ErrorToExceptionFilter::className(),
        ];*/


        // rate limit
        /*
        $behaviors['rateLimiter'] = [
            'class' => \yii\filters\RateLimiter::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        */

        $behaviors['bootstrap'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // filter ip address
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'ips' => Yii::$app->params['allowedIPs'],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * Filter:
     *      - filters[]=<field_name>+<operator>+<params>
     *      - Operators: gt, gte, lt, lte, ne, between, include, exclude
     *        between và in thì tham số/params sẽ phân cách bởi dấu phẩy
     *        Ex:
     *           filters[]=age+beetween+3,4&filters[]=weight+beetween+30,80
     *
     * Set default offset, limit
     *
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        // default language is vietnam
        Yii::$app->language = 'vi';

        try {
            // check select multi ids
            if (isset($_REQUEST['ids'])) {
                $ids = trim($_REQUEST['ids']);
                if (!empty($ids)) {
                    $idsArr = explode(',', $ids);
                    $idsArr = array_unique($idsArr);

                    // check if size > 100 items ==> max 100 items
                    if (count($idsArr) > 100) {
                        $idsArr = array_splice($idsArr, 100);
                    }
                    $this->ids = $idsArr;
                }
            }

            if (!empty(Yii::$app->request->getQueryParam('offset'))) {
                $this->offset = (int)$_REQUEST['offset'];
            }

            if (!empty(Yii::$app->request->getQueryParam('page'))) {
                $this->page = (int)$_REQUEST['page'];
            }

            if (!empty(Yii::$app->request->getQueryParam('pageSize'))) {
                $this->limit = (int)$_REQUEST['pageSize'];
            } else {
                if (!empty(Yii::$app->request->getQueryParam('limit'))) {
                    $this->limit = (int)$_REQUEST['limit'];
                }
            }

            if (!empty(Yii::$app->request->getQueryParam('fields'))) {
                $this->fields = preg_split('/\s*,\s*/', $_REQUEST['fields'], -1, PREG_SPLIT_NO_EMPTY);
            }

            if (!empty(Yii::$app->request->getQueryParam('orderBy'))) {
//            $tmpSort = preg_split('/\s*,\s*/', $_REQUEST['sort'], -1, PREG_SPLIT_NO_EMPTY);
//            foreach($tmpSort as $sort) {
//                var_dump($sort);
//                $sort = trim($sort);
//                $pattern = '/([\+\-])(.+)/i';
//                $replacement = '${2} $1';
//                $sort = preg_replace($pattern, $replacement, $sort);
//                $sort = str_replace("-", "DESC", $sort);
//                $sort = str_replace("+", "ASC", $sort);
//                $this->sort[] = $sort;
//            }

                $this->sort = $_REQUEST['orderBy'];
            }

            if ($this->limit > $this->maxItemInPage) {
                $this->limit = $this->maxItemInPage;
            }

            if ($this->offset < 0) {
                $this->offset = 0;
            }

            //initial query

            $modelClass = $this->modelClass;

            // set tableName
            $activeModel = new $this->modelClass;
            $this->tableName = $activeModel->getTableSchema()->name;
            unset($activeModel);

            // set Query
            $this->query = $modelClass::find();

            // add filter by ids
            if (count($this->ids) > 0) {
                $this->query->andWhere(["`" . $this->tableName . "`." . 'id' => $this->ids]);
            }

            // add offset
            $this->query->offset($this->offset);

            // add limit
            $this->query->limit($this->limit);

            // add sort by
            if (!empty($this->sort)) {
                $this->query->orderBy($this->sort);
            }

            // filter (msisdn=841234567&contentType=1)
            if (!empty($this->filterFields)) {
                foreach ($this->filterFields as $key => $field) {

                    if (isset($_REQUEST[$field]) || isset($_REQUEST[$key])) {

                        if (is_numeric($key)) { // nếu ko fai là trường rewrite (gender => sex )
                            $fieldValue = $_REQUEST[$field];
                        } else {
                            $fieldValue = $_REQUEST[$key];
                        }

                        $this->query->andWhere("`" . $this->tableName . "`" . "." . $field . " = :" . $field, [':' . $field => $fieldValue]);
                    }
                }
            }

            if (!empty(Yii::$app->request->getQueryParam('expand'))) {
                $tmpExpands = preg_split('/\s*,\s*/', Yii::$app->request->getQueryParam('expand'), -1, PREG_SPLIT_NO_EMPTY);
                foreach ($tmpExpands as $expand) {
                    $this->query->joinWith($expand);
                }
            }

            // add selected fields
            if (!empty($this->fields)) {
                $tmpFields = [];
                foreach ($this->fields as $field) {
                    //TODO: check xem tên field khi select có chấm ngăn cách tên bảng và tên trường không
                    $tmpFields[] = $this->tableName . "." . $field;
                }
                $this->query->select($tmpFields);
            }

            // filters
            $tmpFilters = array();
            $filterParam = Yii::$app->getRequest()->getQueryParam('filter', []);
            if (is_array($filterParam)) {
                $tmpFilters = $filterParam;
            } else {
                $tmpFilters[] = $filterParam;
            }

            //$filterRegex = '/\s*(\w+)\s+(\w+)\s+([,\s\w]+)\s*/';
            $filterRegex = '/\s*(\w+)\s+(\w+)\s+([,\s\w\-:]+)\s*/';
            foreach ($tmpFilters as $tmpFilter) {
                preg_match($filterRegex, $tmpFilter, $matches);
                if ($matches) {
                    $tmpFilterField = $matches[1];      // Trường muốn filter
                    $tmpFilterOperator = $matches[2];   // Toán tử để filter
                    $tmpFilterValue = $matches[3];      // Giá trị filter

                    //TODO: Chuẩn hóa lại tên trường, nếu có table.field --> không thêm table nữa.

                    /*var_dump($tmpFilterField);
                    var_dump($tmpFilterOperator);
                    var_dump($tmpFilterOperator);*/

                    if (!in_array($tmpFilterField, $this->filterFields)) {
                        Yii::error("Not support filter by : " . $tmpFilterField . ", FILTER=" . $tmpFilter);
                        throw new NotSupportedException("Not support filter by : " . $tmpFilterField . ", FILTER=" . $tmpFilter);
                        break;
                    }

                    $filterField = "`$this->tableName`.`$tmpFilterField`";
                    $filterFieldParam = $this->tableName . "_" . $tmpFilterField;

                    switch ($tmpFilterOperator) {
                        case "like":  // equal
                            $this->query->andFilterWhere(['like', $filterField, $tmpFilterValue]);
                            break;
                        case "eq":  // equal
                            $this->query->andWhere("$filterField = :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "neq":  // not equal
                            $this->query->andWhere("$filterField <> :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "gt":  // greater than >
                            $this->query->andWhere("$filterField > :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "lt":  // less than  <
                            $this->query->andWhere("$filterField < :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "gte": // greater than or equal >=
                            $this->query->andWhere("$filterField >= :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "lte": // less than equal  <=
                            $this->query->andWhere("$filterField <= :$filterFieldParam", [":$filterFieldParam" => $tmpFilterValue]);
                            break;
                        case "between": // between (1,2)
                            preg_match('/\s*(.+)\s*,\s*(.+)\s*/', $tmpFilterValue, $matchesBetween);
                            if ($matchesBetween) {
                                $value1 = $matchesBetween[1];
                                $value2 = $matchesBetween[2];

                                $this->query->andWhere("$filterField >= :$filterFieldParam" . "_FROM AND $filterField <= :$filterFieldParam" . "_TO", [
                                    ":$filterFieldParam" . "_FROM" => $value1,
                                    ":$filterFieldParam" . "_TO" => $value2,
                                ]);
                            } else {
                                Yii::error("Invalid between query, FILTER=" . $tmpFilter);
                            }
                            break;
                        case "in": // = include ( id in (1,2,3)
                            break;
                        case "include":
                            //TODO : nm_profile_skill.skill_id in (1,2,3)
                            break;
                        case "exclude":
                            //TODO
                            break;
                    }
                }
            }

            //var_dump($tmpFilters);exit;

            // authentication
            /*
            $user = $this->user ? : Yii::$app->getUser()->getIdentity(false);
            if ($user instanceof RateLimitInterface) {
                Yii::trace('Check rate limit', __METHOD__);
                $this->checkRateLimit(
                    $user,
                    $this->request ? : Yii::$app->getRequest(),
                    $this->response ? : Yii::$app->getResponse(),
                    $action
                );
            } elseif ($user) {
                Yii::info('Rate limit skipped: "user" does not implement RateLimitInterface.', __METHOD__);
            } else {
                Yii::info('Rate limit skipped: user not logged in.', __METHOD__);
            }
            */
        } catch (Exception $ex) {
            Yii::$app->response->statusCode = 500;
            Yii::$app->response->content = json_encode([
                'status' => 500,
                'name' => $ex->getName(),
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ]);
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }


    public function afterAction($action, $result)
    {
        //header('Content-type: ' . $this->defaultContentType);
        return parent::afterAction($action, $result);
    }

    /*
    public function afterAction($action, $result) {
        header('Content-type: application/json');
        return parent::afterAction($action, $result);
    }
    */

    /**
     * Hàm lấy danh sách các item
     */
    public function actionIndex()
    {
        $dependency = null;
        $modelClass = $this->modelClass;

        $results = $this->queryIndex()->all();
//        $results = $modelClass::getDb()->cache(function ($db) {
//            return $this->queryIndex()->all();
//        }, $this->cacheIndexPage, $dependency);

        $_result = [];
        if ($results != null) {
            foreach ($results as $item) {
                $expands = [];
                if (isset($_REQUEST['expand']) && !empty($_REQUEST['expand'])) {
                    $expands = preg_split('/\s*,\s*/', $_REQUEST['expand'], -1, PREG_SPLIT_NO_EMPTY);
                }

                $tmp = $item->toArray($this->fields, $expands);
                $_result[] = $tmp;
            }
        } else {
            // return message here
        }

        $totalItems = $this->queryIndex()->count();
//        $totalItems = $modelClass::getDb()->cache(function ($db) {
//            return $this->queryIndex()->count();
//        }, $this->cacheIndexPage, $dependency);

        return [
            $this->collectionEnvelope => $_result,
            $this->metaEnvelope => [
                'count' => (int)$totalItems
            ]];
    }

    /**
     * Query cho hàm lấy danh sách các item
     *
     * @param $query
     * @return mixed
     */
    public function queryIndex()
    {
        return $this->query;
    }


    //public abstract function queryView($query);


    public function setHeader($status)
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
    }

    public function _getStatusCodeMessage($status)
    {
        $codes = Array(
            200 => 'OK',
            204 => 'Delete OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}
