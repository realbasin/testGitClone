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


/*首页成交金额格式化统计*/
function formatMoneyIndex($number,$decimal_count=0){
    if($number == "")
        return "0.00";

    $attr_number = explode(".",$number);
    if(strpos($attr_number[0],',') == false)
        $num_str = $attr_number[0];
    else
        $num_str = $attr_number[0];

    $yi = 100000000;
    $wan = 10000;

    if($num_str > $yi){
        $yi_str = $num_str/$yi;
        $yi_arr = explode('.',$yi_str);
        $wan_str = $yi_arr[1]/$wan;
        $wan_arr = explode('.',$wan_str);

        $data_str = $yi_arr[0].'<i>100 million</i>'.$wan_arr[0].'<i>ten thousand</i>';
        return $data_str;
    }elseif($num_str > $wan){
        $wan_str = $num_str/$wan;
        $wan_arr = explode('.',$wan_str);

        if($decimal_count)
            $data_str = $wan_arr[0].'.'.substr($wan_arr[1],0,$decimal_count).'<i>万</i>';
        else
            $data_str = $wan_arr[0].'<i>万</i>';
        return $data_str;
    }else{
        return $num_str.'<i>元</i>';
    }

}

