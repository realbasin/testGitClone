<?php
namespace ZhimaCredit;
defined("IN_XIAOSHU") or exit("Access Invalid!");
/**
 * 芝麻信用系统调用类
 * Created by PhpStorm.
 * User: maixh
 * Date: 2017/5/23
 * Time: 15:53
 */
class Loader
{
    //芝麻信用基础类
    protected $zhiMaCredit;

    public function __construct() {
        $this->zhiMaCredit = new \ZhimaCredit\ZhiMaCreditBase();
    }

    /**
     * Web版页面授权
     * @param array $args
     * @param int $identity_type 1-按照手机号进行授权；2-按照身份证+姓名进行授权
     * @return string
     */
    public function webAuthorize($args, $identity_type=2) {
        $state = isset($args['state']) ? trim($args['state']) : '';
        if ($identity_type == 2) {
            $this->zhiMaCredit->setIdentityParamByIdCard($args['name'], $args['id_no']);
            $this->zhiMaCredit->setBizParams('M_APPPC_CERT', 'apppc', $state);
        } else {
            $this->zhiMaCredit->setIdentityParamByMobile($args['mobile']);
            $this->zhiMaCredit->setBizParams('M_MOBILE_APPPC', 'apppc', $state);
        }
        $this->zhiMaCredit->setParamData('identity_type', $identity_type);

        return $this->zhiMaCredit->ZhiMaAuthInfoAuthorize();
    }

    /**
     * H5版页面授权
     * @param array $args
     * @param int $identity_type 1-按照手机号进行授权；2-按照身份证+姓名进行授权
     * @return string
     */
    public function appAuthorize($args, $identity_type=2) {
        $state = isset($args['state']) ? trim($args['state']) : '';
        if ($identity_type == 2) {
            $this->zhiMaCredit->setIdentityParamByIdCard($args['name'], $args['id_no']);
        } else {
            $this->zhiMaCredit->setIdentityParamByMobile($args['mobile']);
        }
        $this->zhiMaCredit->setParamData('identity_type', $identity_type);
        $this->zhiMaCredit->setParamData('channel', 'app');
        $this->zhiMaCredit->setBizParams('M_H5', 'app', $state);

        return $this->zhiMaCredit->ZhiMaAuthInfoAuthorize();
    }

    /**
     * 授权成功后，解析返回的参数param（param包含open_id,open_id）
     * @param $args
     * @param $sign
     * @return array
     */
    public function analyzeOpenId($args, $sign) {
        return $this->zhiMaCredit->analyzeOpenId($args, $sign);
    }

    /**
     * 授权查询
     * @param $open_id
     * @return array
     */
    public function authQuery($open_id) {
        $this->zhiMaCredit->setParamData('open_id', $open_id);
        return $this->zhiMaCredit->ZhiMaAuthInfoAuthquery();
    }

    /**
     * 芝麻信用分
     * @param $open_id
     * @return mixed
     */
    public function scoreGet($open_id) {
        $this->zhiMaCredit->setParamData('transaction_id', setTransactionId());
        $this->zhiMaCredit->setParamData('open_id', $open_id);
        return $this->zhiMaCredit->ZhiMaCreditScoreGet();
    }

    /**
     * 反欺诈信息验证
     * @param $args
     * @return mixed
     */
    public function ivsDetailGet($args) {
        $this->zhiMaCredit->setParamData('transaction_id', setTransactionId());

        if (isset($args['name']))
            $this->zhiMaCredit->setParamData('name', $args['name']);
        if (isset($args['cert_no']))
            $this->zhiMaCredit->setParamData('cert_no', $args['cert_no']);
        if (isset($args['mobile']))
            $this->zhiMaCredit->setParamData('mobile', $args['mobile']);
        if (isset($args['email']))
            $this->zhiMaCredit->setParamData('email', $args['email']);
        if (isset($args['bank_card']))
            $this->zhiMaCredit->setParamData('bank_card', $args['bank_card']);
        if (isset($args['address']))
            $this->zhiMaCredit->setParamData('address', $args['address']);
        if (isset($args['ip']))
            $this->zhiMaCredit->setParamData('ip', $args['ip']);

        return $this->zhiMaCredit->ZhiMaCreditIvsDetailGet();
    }

    /**
     * 行业关注名单
     * @param $open_id
     * @return mixed
     */
    public function watchlistiiGet($open_id) {
        $this->zhiMaCredit->setParamData('transaction_id', setTransactionId());
        $this->zhiMaCredit->setParamData('open_id', $open_id);

        return  $this->zhiMaCredit->ZhiMaCreditWatchlistiiGet();
    }

    /**
     * 数据反馈
     * @param int $records
     * @param string $file_records  json文件完整路径
     * @return mixed
     */
    public function ZhimaDataBatchFeedback($records, $file_records)
    {
        //数据总数
        $this->zhiMaCredit->setParamData('records', $records);
        $this->zhiMaCredit->setParamData('file_records', $file_records);

        return $this->zhiMaCredit->ZhimaDataBatchFeedback();
    }
}