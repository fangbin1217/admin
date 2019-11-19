<?php

namespace app\controllers;

use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;



class LoginController extends Controller
{

    public function actionIndex()
    {

        $this->jsonResponse['msg'] = 'login error';
        $params = json_decode(file_get_contents('php://input'),true);
        $params['mobile'] = $params['mobile'] ?? '';
        $params['mobile'] = trim($params['mobile']);
        $params['passwd'] = $params['passwd'] ?? '';
        $params['passwd'] = trim($params['passwd']);
        if (!$params['mobile'] || !$params['passwd']) {
            $this->jsonResponse['msg'] = '用户密码不能空';
            return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
        }
        $params['passwd'] = Users::passwdEnpt($params['passwd']);
        $doLogin = Users::doLogin($params['mobile'], $params['passwd']);
        $this->jsonResponse['msg'] = $doLogin['msg'];
        if ($doLogin['code'] == 0) {
            $this->jsonResponse['code'] = 0;
            $this->jsonResponse['data'] = $doLogin['data'];
        }

        return json_encode($this->jsonResponse, JSON_UNESCAPED_UNICODE);
    }


    public function actionError()
    {
        return '404';
        exit;
    }

    public function actionTest()
    {
        //$records = \app\models\MonthIncomes::getIncomes(2);
        $records = \app\models\DayIncomes::getIncomes(2);

        print_r($records);
    }
}
