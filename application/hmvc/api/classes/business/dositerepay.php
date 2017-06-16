<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 网站垫付业务类
 */
class business_dositerepay extends Business
{
    public function getToDoSiteRepayListData()
    {

        $auto_key = getXSConf('IS_AUTO_SITE_REPAY');

        // 开启网站自动垫付
        if ($auto_key > 0)
        {
            $autoSiteList = \Core::dao('dealrepay')->getAutoSiteList();

            $loanBaseDao = \Core::dao('loanbase');
            $data_list = [];
            foreach ($autoSiteList as $item){
                $deal = [
                    'deal_id' => $item['deal_id'],
                    'l_key' => $item['l_key']
                ];

                if($loanBaseDao->checkRepayStatus($item['deal_id'])){
                    $data_list[] = $deal;
                }
            }

            if(!$data_list){
                return '';
            }

            $timestamp = time();
            $config = $config = \Core::business('siteconfig')->getConfig();;

            // 所获得的数组通过post方式请求 (http://www.xiaoshushidai.com/ajax-auto_dositerepay)
            $url = 'http://' . $config['www_host'] . '/ajax-auto_dositerepay';
            $param = array(
                'site_token' => $config['site_token'],
                'timestamp' => $timestamp,
                'data_list' => json_encode($data_list),
            );
            ksort($param);

            //对url请求做加密处理，在接收端做验证
            $secret = md5(http_build_query($param));
            $param['secret'] = $secret;
            unset($param['site_token']);

            $http_response = curl_post($url, $param);

            $response = json_decode($http_response, true);

            return "调用垫付逻辑返回结果：".$http_response."-->json_decode结果:".$response;
        }
        else
        {
            return "网站自动垫付未开启";
        }
    }
}