<?php
/**
 * 芝麻信用接口
 * Created by PhpStorm.
 * User: maixh
 * Date: 2016/10/12
 * Time: 17:41
 */

namespace ZhimaCredit;

$env = \Core::config()->getEnvironment();
if ($env == 'production')
{
    //商户ID
    define("ZM_APPID", 1000988);
    //商户私钥文件
    define("ZM_PRIVATE_KEY_FILE", APP_PATH.'library/classes/ZhimaCredit/Storage/rsa_private_key.pem');
    //商户公钥文件gi
    define("ZM_PUBLIC_KEY_FILE", APP_PATH.'library/classes/ZhimaCredit/Storage/zmxy_public_key.pem');
    //芝麻系统中配置的值，由芝麻信用提供，需要匹配，测试反馈和正式反馈使用不同的type_id。 其中测试type_id与反馈字段模板会通过邮件统一提供给合作伙伴，在测试反馈通过之后，再通过邮件提供正式反馈type_id给合作伙伴
    define("BATCH_FEEDBACK_TYPE_ID", "1001613-default-order");
}
else
{
    //商户ID
    define("ZM_APPID", 1001613);
    //商户私钥文件
    define("ZM_PRIVATE_KEY_FILE", APP_PATH.'library/classes/ZhimaCredit/Storage/test_rsa_private_key.pem');
    //商户公钥文件
    define("ZM_PUBLIC_KEY_FILE", APP_PATH.'library/classes/ZhimaCredit/Storage/test_zmxy_public_key.pem');
    //芝麻系统中配置的值，由芝麻信用提供，需要匹配，测试反馈和正式反馈使用不同的type_id。 其中测试type_id与反馈字段模板会通过邮件统一提供给合作伙伴，在测试反馈通过之后，再通过邮件提供正式反馈type_id给合作伙伴
    define("BATCH_FEEDBACK_TYPE_ID", "1001613-default-test");
}

//芝麻信用日志路径
define("ZMOP_SDK_WORK_DIR", 'logger/');

class ZhiMaCreditBase
{
    //芝麻信用网关地址
    protected $gatewayUrl = "https://zmopenapi.zmxy.com.cn/openapi.do";

    //商户私钥文件
    protected $privateKeyFile = ZM_PRIVATE_KEY_FILE;

    //芝麻公钥文件
    protected $zmPublicKeyFile = ZM_PUBLIC_KEY_FILE;

    //数据编码格式
    protected $charset = "UTF-8";

    //芝麻分配给商户的 appId
    protected $appId = ZM_APPID;

    //芝麻会员在商户端的身份标识
    protected $open_id = '';

    //api:商户后台调用；apppc:商户pc端调用；app:商户移动app调用
    protected $channel = 'apppc';

    //来源平台，默认为zmop
    protected $platform = 'zmop';

    //调用接口所需要的参数集合
    protected $data = array();

    //业务流水号(30位到64位之间的数字集合,保证唯一)
    protected $transaction_id = '';

    //页面授权-身份标识类型(1:按照手机号进行授权 2:按照身份证+姓名进行授权)
    protected $identity_type = 1;

    //页面授权-证件类型
    protected $cert_type = 100;

    /**
     * 页面授权-不同身份类型传入的参数列表,json字符串的key-value格式
     * 身份类型identityType=1: {"mobileNo":"15158657683"}
     * 身份类型identityType=2: {"certNo":"330100xxxxxxxxxxxx","name":"张三","certType":"IDENTITY_CARD"}
     */
    protected $identity_param = "";

    /**
     * 页面授权-业务扩展字段,页面授权接口需要传入auth_code,channelType,state
     * auth_code授权码,值取决于授权方式和身份类型
     * PC方式,身份类型identity_type=1: {"auth_code":"M_MOBILE_APPPC"}
     * PC方式,身份类型identity_type=2: {"auth_code":"M_APPPC_CERT"}
     * H5方式(身份类型identity_type为任何值): {"auth_code":"M_H5"}
     * SDK方式(身份类型identity_type为任何值): {"auth_code":"M_APPSDK"}
     *
     * channelType渠道类型,每个授权码支持不同的渠道类型
     * appsdk:sdk接入
     * apppc:商户pc页面接入
     * api:后台api接入
     * windows:支付宝服务窗接入
     * app:商户app接入
     *
     * state是商户自定义的数据,页面授权接口会原样把这个数据返回个商户
     *
     * 例：{"auth_code":"M_APPPC_CERT","channelType":"apppc","state":"商户自定义"}
     */
    protected $biz_params = "";

    //反馈的json格式的文件，其中{"records": 是每个文件的固定开头
    protected $file_records = '';

    public function __construct()
    {
        $this->data['open_id'] = $this->open_id;
        $this->data['channel'] = $this->channel;
        $this->data['platform'] = $this->platform;
        //代表一笔请求的唯一标志
        $this->data['transaction_id'] = $this->transaction_id;
        //身份标识类型(1:按照手机号进行授权 2:按照身份证+姓名进行授权)
        $this->data['identity_type'] = $this->identity_type;
        //证件类型
        $this->data['cert_type'] = $this->cert_type;
        //参数内的各个内容必须要用'"'包含，否则会失败
        $this->data['identity_param'] = $this->identity_param;
        //参数内的各个内容必须要用'"'包含，否则会失败
        $this->data['biz_params'] = $this->biz_params;
        //反馈的json格式的文件，其中{"records": 是每个文件的固定开头
        $this->data['file_records'] = $this->file_records;
    }

    /**
     * 设置参数
     * @param $name
     * @param $value
     */
    public function setParamData($name, $value)
    {
        $this->data[$name] = trim($value);
    }

    /**
     * 页面授权-按手机号授权
     * @param $mobile
     */
    public function setIdentityParamByMobile($mobile)
    {
        $this->setParamData('identity_param', '{"mobileNo":"' . $mobile . '"}');
    }

    /**
     * 页面授权-按身份证+姓名授权
     * @param $name
     * @param $id_card
     */
    public function setIdentityParamByIdCard($name, $id_card)
    {
        $this->setParamData('identity_param', '{"name":"' . $name . '","certType":"IDENTITY_CARD","certNo":"' . $id_card . '"}');
    }

    /**
     * 页面授权-业务扩展字段
     * @param $auth_code
     * @param string $channelType
     * @param string $state
     */
    public function setBizParams($auth_code, $channelType='apppc', $state='')
    {
        $this->setParamData('biz_params', '{"auth_code":"'.$auth_code.'","channelType":"'.$channelType.'","state":"'.$state.'"}');
    }

    /**
     * 页面授权
     * @return string
     */
    public function ZhiMaAuthInfoAuthorize()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);

        $request = new ZhimaAuthInfoAuthorizeRequest();

        $request->setChannel($this->data['channel']);

        $request->setPlatform($this->data['platform']);

        $request->setIdentityType($this->data['identity_type']);

        //json格式:"{\"name\":\"张三\",\"certType\":\"IDENTITY_CARD\",\"certNo\":\"330100xxxxxxxxxxxx\"}"
        $request->setIdentityParam($this->data['identity_param']);

        //json格式:"{\"auth_code\":\"M_H5\",\"channelType\":\"app\",\"state\":\"商户自定义\"}"
        $request->setBizParams($this->data['biz_params']);

        $url = $client->generatePageRedirectInvokeUrl($request);

        return $url;
    }

    /**
     * 授权成功后，解析返回的参数param（param包含open_id,state）
     * @param $params
     * @param $sign
     * @return array array('open_id'=>268814555576479090898629283,'error_message'=>'操作成功','state'=>'商户自定义','error_code'=>'SUCCESS','app_id'=>1000988,'success'=>true)
     * @throws \Exception
     */
    public function analyzeOpenId($params, $sign)
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);

        //open_id=268814555576479090898629283&error_message=%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F&state=%E5%95%86%E6%88%B7%E8%87%AA%E5%AE%9A%E4%B9%89&error_code=SUCCESS&app_id=1000988&success=true
        $result = $client->decryptAndVerifySign($params, $sign);

        //先进行urldecode解码
        $result = urldecode($result);

        //然后将字符串转换成数组
        parse_str($result, $array);

        return $array;
    }

    /**
     * 授权查询
     * @return mixed
     * @throws \Exception
     */
    public function ZhiMaAuthInfoAuthquery()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);

        $request = new ZhimaAuthInfoAuthqueryRequest();

        $request->setChannel($this->data['channel']);

        $request->setPlatform($this->data['platform']);

        $request->setIdentityType("0");

        $request->setIdentityParam('{"openId":"'.$this->data['open_id'].'"}');

        $response = $client->execute($request);

        /**
         * 获取结果失败
         * {"success":false,"error_code":"ZMCREDIT.api_product_not_match","error_message":"输入的产品码不正确"}
         *
         * 获取结果成功
         * {"success":true,"authorized":"true","open_id":"268814555576479090898629283"}
         */
        return json_encode($response);
    }

    /**
     * 芝麻信用评分
     * @return mixed
     * @throws \Exception
     */
    public function ZhiMaCreditScoreGet()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);

        $request = new ZhimaCreditScoreGetRequest();

        $request->setChannel($this->data['channel']);

        $request->setPlatform($this->data['platform']);

        $request->setTransactionId($this->data['transaction_id']);

        $request->setProductCode('w1010100100000000001');

        $request->setOpenId($this->data['open_id']);

        $response = $client->execute($request);
        /**
         * 获取结果失败
         * {"success":false,"error_code":"ZMCREDIT.api_product_not_match","error_message":"输入的产品码不正确"}
         *
         * 获取结果成功
         * {"success":true,"biz_no":"ZM201610193000000022800276241578","zm_score":"721"}
         */
        return json_encode($response);
    }

    /**
     * 反欺诈信息验证
     * @return mixed
     * @throws \Exception
     */
    public function ZhiMaCreditIvsDetailGet()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
        $request = new ZhimaCreditIvsDetailGetRequest();
        $request->setChannel($this->data['channel']);
        $request->setPlatform($this->data['platform']);
        $request->setProductCode('w1010100000000000103');
        $request->setTransactionId($this->data['transaction_id']);

        //证件号。 备注：证件号、姓名、手机号、地址、银行卡、电子邮箱必须传其中两项
        if (isset($this->data['cert_no']))
            $request->setCertNo($this->data['cert_no']);

        //证件类型
        $request->setCertType($this->data['cert_type']);
        
        //姓名
        if (isset($this->data['name']))
            $request->setName($this->data['name']);

        //手机号，最多传入三个，多个手机号之间用|分隔，如15256797367|18669152789。
        if (isset($this->data['mobile']))
            $request->setMobile($this->data['mobile']);

        //电子邮箱，最多传入两个，多个邮箱之间用|分隔，如jnlxhy@alitest.com|john.sss@alitest.com。
        if (isset($this->data['email']))
            $request->setEmail($this->data['email']);

        //银行卡号最多输入两个，多个银行卡号之间用|分隔，如602436748024138|622536748024139。
        if (isset($this->data['bank_card']))
            $request->setBankCard($this->data['bank_card']);

        //用户地址最多输入三个，多个地址之间用|分隔，如 杭州市西湖区天目山路266号|杭州市西湖区万塘路999号。
        if (isset($this->data['address']))
            $request->setAddress($this->data['address']);

        //ip地址
        if (isset($this->data['ip']))
            $request->setIp($this->data['ip']);

        //物理地址
        if (isset($this->data['mac']))
            $request->setMac($this->data['mac']);

        //wifi的物理地址
        if (isset($this->data['wifi_mac']))
            $request->setWifimac($this->data['wifi_mac']);

        //国际移动设备标志
        if (isset($this->data['imai']))
            $request->setImei($this->data['imai']);

        //国际移动用户识别码
        if (isset($this->data['imsi']))
            $request->setImsi($this->data['imsi']);

        $response = $client->execute($request);

        return json_encode($response);
    }

    /**
     * 行业关注名单2.0版
     * @return mixed
     * @throws \Exception
     */
    public function ZhiMaCreditWatchlistiiGet()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);

        $request = new ZhimaCreditWatchlistiiGetRequest();

        $request->setChannel($this->data['channel']);

        $request->setPlatform($this->data['platform']);

        $request->setProductCode('w1010100100000000022');

        $request->setTransactionId($this->data['transaction_id']);

        $request->setOpenId($this->data['open_id']);

        $response = $client->execute($request);

        /**
         * 获取结果失败
         * {"success":false,"error_code":"ZMCREDIT.api_product_not_match","error_message":"输入的产品码不正确"}
         *
         * 获取结果成功
         * {"success":true,"biz_no":"ZM201610193000000852800276451283","is_matched":false}
         *is_matched:true=命中 在关注名单中 false=未命中
         *
         * {"success":true,"biz_no":"ZM201610193000000852800276451283","is_matched":true,"details":*****}
         */
        return json_encode($response);
    }

    /**
     * 数据反馈
     * @return string
     */
    public function ZhimaDataBatchFeedback()
    {
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
        $request = new ZhimaDataBatchFeedbackRequest();
        $request->setPlatform($this->data['platform']);
        $request->setFileType("json_data");// 必要参数
        $request->setFileCharset($this->charset);// 必要参数
        $request->setRecords($this->data['records']);// 必要参数
        $request->setColumns("user_name,user_credentials_type,user_credentials_no,order_no,biz_type,order_status,create_amt,pay_month,gmt_ovd_date,overdue_days,overdue_amt,gmt_pay,memo");// 必要参数
        $request->setPrimaryKeyColumns("order_no,pay_month");// 必要参数
        $request->setFileDescription("小树时代数据反馈");//文件描述信息
        //芝麻系统中配置的值，由芝麻信用提供，需要匹配，测试反馈和正式反馈使用不同的type_id。 其中测试type_id与反馈字段模板会通过邮件统一提供给合作伙伴，在测试反馈通过之后，再通过邮件提供正式反馈type_id给合作伙伴
        $request->setTypeId(BATCH_FEEDBACK_TYPE_ID);// 必要参数
        $request->setBizExtParams("");//扩展参数
        //反馈的json格式的文件，其中{"records": 是每个文件的固定开头
        $request->setFile($this->data['file_records']);// 必要参数
        $response = $client->execute($request);
        return json_encode($response);
    }
}