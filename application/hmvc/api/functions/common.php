<?php
/**
 * 公共函数库
 * User: wwb
 * Date: 2017/6/17
 * Time: 12:07
 */

/**
 * 获取网址配置
 * @return array
 */
function getSiteConfig()
{
    $app_env = \Core::config()->getEnvironment();
    $site['site_token'] = md5('api.xiaoshushidai.com');
    if ($app_env == 'production') {
        $site['www_host'] = 'www.xiaoshushidai.com';
    } else if ($app_env == 'test') {
        $site['www_host'] = 'test.xiaoshushidai.com';
    } else if ($app_env == 'development') {
        $site['www_host'] = 'mytest.xiaoshushidai.com';
    } else {
        $site['www_host'] = $app_env . '.xiaoshushidai.com';
    }

    return $site;
}


/**
 * 理财首页成交金额格式化
 * @param $number
 * @param int $decimal_count :小数位数，只接受≥0的参数
 * @param  bool $accurate :是否返回精确结果
 * @return string
 */
function formatMoneyIndex($number, $decimal_count=0, $accurate=false){

    //小数位数，只接受≥0的参数
    $decimal_count = intval($decimal_count);
    if($decimal_count < 0){
        $decimal_count = 0;
    }

    if(!$number || $number == "" || $number == 0.00)
        return sprintf("%.".$decimal_count, 0); //"0.00元";

    if(strpos($number, ",") !== false){
        $number = str_replace(",", "", $number);
    }

    $hundredmillion = "";
    if($number >= 100000000){
        $hundredmillion = intval($number/100000000)."<i>hundred million</i>";
        $number = $number - intval($number/100000000)*100000000;
    }

    $tenthousand = "";
    if($number + 5000 >= 10000){  // +5000 兼容四舍五入
        if($hundredmillion != "" || !$accurate){
            $tenthousand = sprintf("%.".$decimal_count."f<i>ten thousand</i>", $number/10000);
            return $hundredmillion.$tenthousand;
        }else{
            $tenthousand = intval($number/10000)."<i>ten thousand</i>";
            $number = $number - intval($number/10000)*10000;
        }
    }

    if($hundredmillion != ""){
        return $hundredmillion.$tenthousand;
    }else{
        if($number > 0){
            return $tenthousand.sprintf("%.".$decimal_count."f<i>元</i>", $number);
        }else{
            return $tenthousand;
        }
    }
}

