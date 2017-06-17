<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 合同业务类
 */
class business_Contract extends Business
{

    /**
     * @param $id
     * @return mixed
     */
    public function createSignPdf($id)
    {
        $deal_id = intval($id);

        $timestamp = time();

        $config = getSiteConfig();

        // 所获得的数组通过post方式请求 (http://www.xiaoshushidai.com/ajax-auto_dositerepay)
        $url = 'http://' . $config['www_host'] . '/ajax-contract_pdf';
        $param = array(
            'site_token' => $config['site_token'],
            'timestamp' => $timestamp,
            'id' => $deal_id
        );
        ksort($param);

        //对url请求做加密处理，在接收端做验证
        $secret = md5(http_build_query($param));
        $param['secret'] = $secret;
        unset($param['site_token']);

        $http_response = curl_post($url, $param);

        return $http_response;
    }
}