<?php

namespace app\controllers;

use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

use app\models\DayRecords;


class UserController extends Controller
{

    public function actionInfo()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'userInfo error';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $this->jsonResponse['code'] = 0;
            $this->jsonResponse['msg'] = 'get userinfo success';
            $cacheList['qrcode'] = Yii::$app->params['serverHost'].$cacheList['qrcode'];
            $this->jsonResponse['data'] = $cacheList;
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    public function actionLastweekrecords()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'no data';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $records = DayRecords::getLastWeekRecords($cacheList['id']);
        }
    }

}
