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

    }


    public function actionError()
    {
        return '404';
        exit;
    }
}
