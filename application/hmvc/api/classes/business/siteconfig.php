<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/**
 * 网址配置业务类
 */
class business_siteconfig extends Business
{

    public function getConfig()
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

}