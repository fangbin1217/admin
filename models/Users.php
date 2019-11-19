<?php

namespace app\models;
use Yii;

class Users  extends \yii\db\ActiveRecord
{

    static public $error_msg = '';

    static public $isFull = 0;

    static public $playerName = '';

    static public $playerAvatar = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'members';
    }

    static public function getUserByAccessToken($access_token) {
        return self::find()->where(['access_token'=>$access_token, 'is_del'=> 0])->asArray()->one();
    }

    static public function passwdEnpt($passwd) {
        return strtoupper(md5(md5(trim($passwd).'R').'G'));
    }

    static function saveMember($params) {
        $now = date('Y-m-d H:i:s');
        $users = new Users();
        $users->username = getRandStr(6);
        $users->passwd = self::passwdEnpt('123456');
        $users->create_time = $now;
        $users->update_time = $now;
        return $users->save();
    }

    static public function doLogin($mobile, $pwd) {
        $user = self::find()->where(['mobile'=>$mobile, 'passwd'=>$pwd, 'is_del'=> 0])->asArray()->one();
        if (!$user) {
            return ['code'=>-1, 'msg'=>'账号密码错误', 'data'=>[]];
        }
        $uid = $user['id'];
        $now = date('Y-m-d H:i:s');
        $time = time();
        $access_token = strtoupper(md5($uid.$now.rand(1,9999)));
        $expire_time = $time + Yii::$app->params['loginCacheTime'];

        $Users = Users::find()->where(['id'=>$uid])->one();
        $Users->access_token = $access_token;
        $Users->expire_time = $expire_time;
        $Users->update_time = $now;
        if ($Users->save()) {
            $userInfo = [
                'id'=>$uid, 'mobile'=>$mobile, 'username'=>$user['username'],
                'access_token'=>$access_token, 'is_admin'=>$user['is_admin'], 'qrcode' => $user['qrcode']
            ];
            Yii::$app->redis->set('T2#'.$access_token, json_encode($userInfo, JSON_UNESCAPED_UNICODE));
            Yii::$app->redis->expire('T2#'.$access_token, Yii::$app->params['loginCacheTime']);
            return ['code'=>0, 'msg'=>'success', 'data'=>$userInfo];
        }
        return ['code'=>-1, 'msg'=>'保存失败', 'data'=>[]];
    }


    static public function isLogin($access_token = '') {
        if (!$access_token) {
            return false;
        }
        $cache = Yii::$app->redis->get('T2#'.$access_token);
        if ($cache) {
            return true;
        }
        $userInfo = Users::getUserByAccessToken($access_token);
        if ($userInfo) {
            if ($userInfo['expire_time'] >= time()) {
                $expire_time = $userInfo['expire_time'] - time();
                Yii::$app->redis->set('T2#'.$access_token, json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                Yii::$app->redis->expire('T2#'.$access_token, $expire_time);
                return true;
            }
        }
        return false;
    }

    static public function getUserInfo($uid) {
        return  self::find()->where(['id'=>$uid, 'is_del'=> 0])->asArray()->one();
    }

    static public function getUserInfo2($uid) {
        return  self::find()->where(['id'=>$uid, 'is_del'=> 0])->one();
    }
}
