<?php

$commonParams = [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'appid' => 'wxe736eb5b61ba3beb',
    'appsercet' => '7fea98413cd90e6b6ae20d2ed943d05a',
    'randkey' => 'GR511M',
    'loginCacheTime' => 7776000, //登陆状态记录时间，单位为秒  90天
    'withoutlogin' => ['login', 'test'],
    'notice' => '为了给广大用户提供更好的服务，云服务器近期将升级，如有网络波动，敬请谅解！'
];

if (YII_ENV == 'dev') {
    $commonParams['serverHost'] = 'http://admin.fyy6.fb/';
    $commonParams['imgHost'] = 'https://img.fyy6.com/';
    $commonParams['imageFirstPath'] = '/usr/local/var/www/admin/web';
} else {
    $commonParams['serverHost'] = 'https://admin.fyy6.com/';
    $commonParams['imgHost'] = 'https://img.fyy6.com/';
    $commonParams['imageFirstPath'] = '/home/www/admin/web';

}

return $commonParams;

