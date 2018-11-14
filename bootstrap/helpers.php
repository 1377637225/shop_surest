<?php
/**
 * Created by PhpStorm.
 * User: surest.cn
 * Date: 2018/11/1
 * Time: 21:21
 */

/**
 * 获取静态文件的地址
 * @return string
 */
function getAssets() {
    return config('APP_URL') . config('mian.assets');
}

/**
 * 返回登录的字段类型
 *
 * @param $value
 * @return bool|mixed|string
 */
function checkParamType($value) {
    if( filter_var($value,FILTER_VALIDATE_EMAIL) ) {
        return 'email';
    }

    $res = filter_var($value,FILTER_CALLBACK,[
        'options' => function ($value){
            if( preg_match('/^1[34578]\d{9}$/',$value) ) {
                return 'phone';
            }
        }
    ]);

    return is_null($res) ? false : $res;
}
function setCode($num)
{
    $i = 0;
    $code = '';
    while ($i<$num) {
        $code .= random_int(0,9);
        $i++;
    }
    return $code;
}

function eny($pwd,$salt){
    return md5($salt . md5($pwd));
}

function setResponse($msg,$status){
    $res = config('response');
    $res['msg'] = $msg;
    $res['status'] = $status;
    return $res;
}

function getRouteList(){
    $app = app();
    $routes = $app->routes->getRoutes();
    $list = [];
    foreach ($routes as $k=>$value){
        array_push($list,$value->uri);
    }
    return $list;
}

function checkAddress($val) {
    $parttrn = '#(.*)?[/-](.*)?[-/](.*)?#';
    preg_match_all($parttrn, $val, $match);
    if( empty($match) ) {
        return false;
    }

    return $match;
}