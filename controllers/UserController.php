<?php

namespace app\controllers;

use app\models\MonthIncomes;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

use app\models\DayRecords;
use app\models\DayIncomes;


class UserController extends Controller
{

    //用户信息
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

    //首页概况
    public function actionIndexincomes()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'no data';
        $records = [];
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $records = DayIncomes::getIncomes($cacheList['id']);
            $this->jsonResponse['msg'] = 'success';
            $this->jsonResponse['code'] = 0;
            $this->jsonResponse['data'] = $records;
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    //结算详情
    public function actionStateincomes()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'no data';
        $records = [];
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $records = MonthIncomes::getIncomes($cacheList['id']);
            $this->jsonResponse['msg'] = 'success';
            $this->jsonResponse['code'] = 0;
            $this->jsonResponse['data'] = $records;
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    //设置每日广告收益
    public function actionSetdaymoney()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'set error';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $params['incomes'] = $params['incomes'] ?? 0.00;
            $params['date'] = $params['date'] ?? '';
            $setIncomes = DayIncomes::setBannerDayIncomes($params['incomes'], $params['date'], $cacheList['is_admin']);
            if ($setIncomes) {
                $this->jsonResponse['msg'] = 'success';
                $this->jsonResponse['code'] = 0;
            }
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    //设置每月广告收益
    public function actionSetmonthmoney()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'set error';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $params['incomes'] = $params['incomes'] ?? 0.00;
            $params['date'] = $params['date'] ?? '';
            $setIncomes = DayIncomes::setBannerMonthIncomes($params['incomes'], $params['date'], $cacheList['is_admin']);
            if ($setIncomes) {
                $this->jsonResponse['msg'] = 'success';
                $this->jsonResponse['code'] = 0;
            }
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    //获取所有未结算数据
    public function actionAllnostaterecords()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'empty data';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $setIncomes = DayIncomes::getAllNostateRecords($cacheList['is_admin']);
            if ($setIncomes) {
                $this->jsonResponse['msg'] = 'success';
                $this->jsonResponse['code'] = 0;
            }
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }

    //结算某个会员
    public function actionSetstate()
    {
        $params = json_decode(file_get_contents('php://input'),true);
        $access_token = $params['access_token'] ?? '';
        $cache = Yii::$app->redis->get('T2#' . $access_token);
        $this->jsonResponse['msg'] = 'set error';
        if ($access_token && $cache) {
            $cacheList = json_decode($cache, true);
            $params['month_income_id'] = $params['month_income_id'] ?? 0;
            $setIncomes = DayIncomes::setState($params['month_income_id'], $cacheList['is_admin']);
            if ($setIncomes) {
                $this->jsonResponse['msg'] = 'success';
                $this->jsonResponse['code'] = 0;
            }
        }
        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }



}
