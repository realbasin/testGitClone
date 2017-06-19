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