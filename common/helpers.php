<?php
/**
 * Created by PhpStorm.
 * User: binfang
 * Date: 2019-07-24
 * Time: 15:42
 */

if (! function_exists('foo')) {

    function foo() {
        echo 'foo';
    }
}

if (! function_exists('vesionInt')) {

    function vesionInt($version = '') {
        if ($version) {
            $version = str_replace('.', '', $version);
            return (int) $version;
        }
        return 0;
    }
}

if (! function_exists('getRandStr')) {

    function getRandStr($length){
        $str='abcdefghjklmnpqrstuvwxyz0123456789';
        $len=strlen($str)-1;
        $randstr='';
        for($i=0;$i<$length;$i++){
            $num=mt_rand(0,$len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

}

if (! function_exists('sayHello')) {

    function sayHello() {
        $day = date('n');
        if ($day >6 && $day <= 12) {
            return '上午好';
        } else if ($day >12 && $day <= 18) {
            return '下午好';
        } else {
            return '晚上好';
        }
    }
}
