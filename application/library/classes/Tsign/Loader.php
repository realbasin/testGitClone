<?php
/**
 * e签宝盖章签署
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/24
 * Time: 11:55
 */
namespace Tsign;
defined("IN_XIAOSHU") or exit("Access Invalid!");

use Tsign\core\eSign;
use Tsign\constants\PersonArea;
use Tsign\constants\PersonTemplateType;
use Tsign\constants\OrganizeTemplateType;
use Tsign\constants\SealColor;
use Tsign\constants\UserType;
use Tsign\constants\OrganRegType;
use Tsign\constants\SignType;

require_once 'eSignOpenAPI.php';
class Loader
{
    protected $esign;
    public function __construct()
    {
        try {
            $env = \Core::config()->getEnvironment();
            $this->esign = new eSign($env);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 添加个人账号
     * @param $userSign
     * @return array|mixed|
     */
    protected function addPersonAccount($userSign)
    {
        $ret = $this->esign->addPersonAccount($userSign['mobile'], $userSign['real_name'], $userSign['id_no']);
        if (!empty($ret)) {
            $userSign['errCode'] = $ret['errCode'];
            $userSign['accountId'] = $ret['accountId'];
            $userSign['accountId_error_msg'] = $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '';
            $this->saveUserSignature($userSign);

            if ($ret['errCode'] > 0) {
                return $ret;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝添加个人帐号请求失败');
    }

    /**
     * 更新个人账号
     * @param $user_id
     * @param $accountId
     * @param $mobile
     * @param $name
     * @return array|bool|mix|mixed|stdClass|string
     */
    public function updatePersonAccount($user_id, $accountId, $mobile, $name)
    {
        $modifyArray = array(
            'mobile' => $mobile,
            'name' => $name,
        );
        $ret = $this->esign->upatePersonAccount($accountId, $modifyArray);

        if (!empty($ret)) {
            $userSign = array(
                'user_id' => $user_id,
                'errCode' => $ret['errCode'],
                'accountId' => $ret['accountId'],
                'accountId_error_msg' => $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '',
            );
            $this->saveUserSignature($userSign);

            if ($ret['errCode'] > 0) {
                return $ret;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝更新个人账号请求失败');
    }

    /**
     * 删除个人账户
     * @param $accountId
     * @return mixed
     */
    public function delUserAccount($esignId, $accountId)
    {
        $res = $this->esign->delUserAccount($accountId);
        if (in_array($res['errCode'], array(0,1007))) {
            $this->deleteUserSign($esignId);
            return true;
        }
        return false;
    }

    /**
     * 添加个人模板印章
     * @param $user_id
     * @param $accountId
     * @return array|bool|mix|mixed|stdClass|string
     */
    protected function addPersonTemplateSeal($user_id, $accountId)
    {
        $ret = $this->esign->addTemplateSeal(
            $accountId,
            $templateType = PersonTemplateType::RECTANGLE,
            $color = SealColor::RED
        );
        if (!empty($ret)) {
            $userSign = array(
                'user_id' => $user_id,
                'errCode' => $ret['errCode'],
                'signature' => $ret['errCode']==0 ? $ret['imageBase64']: '',
                'signature_error_msg' => $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '',
            );
            $this->saveUserSignature($userSign);

            if ($ret['errCode'] > 0) {
                return $ret;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝添加个人模板印章请求失败');
    }

    //添加企业账号
    protected function addOrgAccount($type=2)
    {
        //小树金融
        if ($type == 3) {
            $user_id = 222;
            $mobile = '18922987455';
            $name = '深圳前海小树时代互联网金融服务有限公司'; //企业名称
            $organType = '0';
            $email = '';
            $organCode = '91440300326440129N';  //组织机构代码号或社会信用代码号 或 工商注册号
            $regType = OrganRegType::MERGE; //证件类型
            $legalName = '何敬业'; //法人姓名
            $legalIdNo = '441421198310154092';  //法人证件号
            $legalArea = PersonArea::MAINLAND;  //地区
            $userType = UserType::USER_LEGAL;   //注册类型 1、代理人 2、法人
            $agentName = '';    //代理人姓名
            $agentIdNo = '';    //代理人证件号
        }
        //小树普惠
        else {
            $user_id = 111;
            $mobile = '13826969529';
            $name = '广东小树普惠科技有限公司'; //企业名称
            $organType = '0';
            $email = '';
            $organCode = '91441900MA4UWUFR4J';  //组织机构代码号或社会信用代码号 或 工商注册号
            $regType = OrganRegType::MERGE; //证件类型
            $legalName = '罗英珍'; //法人姓名
            $legalIdNo = '362227198109010319';  //法人证件号
            $legalArea = PersonArea::MAINLAND;  //地区
            $userType = UserType::USER_LEGAL;   //注册类型 1、代理人 2、法人
            $agentName = '';    //代理人姓名
            $agentIdNo = '';    //代理人证件号
        }

        $res = $this->esign->addOrganizeAccount($mobile,
            $name,
            $organCode,
            $regType ,
            $email,
            $organType,
            $legalArea ,
            $userType ,
            $agentName,
            $agentIdNo,
            $legalName,
            $legalIdNo,
            $address = '',
            $scope = '');
        if (!empty($res)) {
            $userSign = array(
                'user_id' => $user_id,
                'errCode' => $res['errCode'],
                'accountId' => $res['errCode']==0 ? $res['accountId'] : '',
                'accountId_error_msg' => $res['errCode']>0 ? json_encode($res, JSON_UNESCAPED_UNICODE) : '',
            );
            $this->saveUserSignature($userSign, $type);

            if ($res['errCode'] > 0) {
                return $res;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝添加企业账号请求失败');
    }

    //更新企业账号
    public function updateOrgAccount($user_id, $accountId)
    {
        //需要修改的字段集
        $modifyArray = array(
            "email" => NULL,  // '' 或 NULL 表示清空改字段
            "mobile" => '',
            //"name" => '', //不修改
            //"organType" => '0', //0-普通企业  不修改
            "userType" => UserType::USER_LEGAL, //1-代理人注册，2-法人注册
            "agentIdNo" => '', //代理人身份证号 userType = 1 此项不能为空
            "agentName" => '', //代理人姓名 userType = 1 此项不能为空
            "legalIdNo" => '360730198902261416', //法人身份证号  userType = 2 此项不能为空
            "legalName" => '张三',//法人身份证号  userType = 2 此项不能为空
            "legalArea" => NULL //用户归属地 0-大陆
        );

        $res = $this->esign->updateOrganizeAccount($accountId, $modifyArray);
        if (!empty($res)) {
            $userSign = array(
                'user_id' => $user_id,
                'errCode' => $res['errCode'],
                'accountId' => $res['accountId'],
                'accountId_error_msg' => $res['errCode']>0 ? json_encode($res, JSON_UNESCAPED_UNICODE) : '',
            );
            $this->saveUserSignature($userSign, 2);

            if ($res['errCode'] > 0) {
                return $res;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝更新企业账号请求失败');
    }

    /**
     * 添加企业模板印章
     * @param $user_id
     * @param $accountId
     * @param int $type
     * @return array|mixed|
     */
    protected function addOrgTemplateSeal($user_id, $accountId, $type=2)
    {
        $ret = $this->esign->addTemplateSeal(
            $accountId,
            $templateType = OrganizeTemplateType::STAR,
            $color = SealColor::RED
        );
        if (!empty($ret)) {
            $userSign = array(
                'user_id' => $user_id,
                'errCode' => $ret['errCode'],
                'signature' => $ret['errCode']==0 ? $ret['imageBase64']: '',
                'signature_error_msg' => $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '',
            );
            $this->saveUserSignature($userSign, $type);

            if ($ret['errCode'] > 0) {
                return $ret;
            }  else {
                $userSign['errCode'] = 0;
                return $userSign;
            }
        }
        return array('errCode'=>1,'msg'=>'e签宝企业模板印章请求失败');
    }

    /**
     * 将电子签章保存到数据表
     * @param $userSign
     * @param string $mode
     * @param int $type
     */
    protected function saveUserSignature($userSign, $type=1)
    {
        $sqlData = array();
        $errCode = $userSign['errCode'];
        unset($userSign['errCode']);

        if (isset($userSign['real_name'])) {
            $sqlData['real_name'] = $userSign['real_name'];
        }
        if (isset($userSign['id_no'])) {
            $sqlData['id_no'] = $userSign['id_no'];
        }
        if (isset($userSign['mobile'])) {
            $sqlData['mobile'] = $userSign['mobile'];
        }

        if (isset($userSign['accountId'])) {
            $sqlData['accountId'] = $userSign['accountId'];
        }

        if (isset($userSign['accountId_error_msg'])) {
            $sqlData['accountId_error_msg'] = $userSign['accountId_error_msg'];
        }

        if (isset($userSign['signature'])) {
            $sqlData['signature'] = $userSign['signature'];
        }

        if (isset($userSign['signature_error_msg'])) {
            $sqlData['signature_error_msg'] = $userSign['signature_error_msg'];
        }

        //不管返回结果成功与否，都保存到数据表
        $signId = intval(\Core::db()->select('id')->from('user_signature')->where(array('is_effect'=>1,'user_id'=>$userSign['user_id']))->execute()->value('id'));
        if ($signId > 0) {
            \Core::db()->update('user_signature', $sqlData)->where(array('id'=>$signId))->execute();
        } else {
            $sqlData['user_id'] = $userSign['user_id'];
            $sqlData['type'] = $type;
            $sqlData['is_effect'] = $errCode==0 ? 1 : 0;
            $sqlData['create_time'] = time();

            \Core::db()->insert('user_signature', $sqlData)->execute();

            //返回结果正确时，才更新xssd_user表的e签宝认证字段
            if ($errCode==0 && $type==1 && $GLOBALS['db']->affected_rows()) {
                \Core::db()->update('user', array('is_esign'=>1))->where(array('id'=>$userSign['user_id']))->execute();
            }
        }
    }

    /**
     * 更新删除e签宝数据
     * @param $esignId
     */
    public function deleteUserSign($esignId)
    {
        \Core::db()->update('user_signature', array('is_effect'=>0))->where(array('id'=>$esignId))->execute();
    }

    /**
     * 获取个人用户签章信息
     * @param $user_id
     * @param $mobile
     * @param $name
     * @param $idNo
     * @return array|string
     */
    public function getPersonSign($userSign)
    {
        $user_id = $userSign['user_id'];
        $signData = \Core::db()->select('user_id,accountId,signature')->from('user_signature')->where(array('is_effect'=>1,'type'=>1,'user_id'=>$user_id))->execute()->row();
        if (empty($signData) || empty($signData['accountId']) || empty($signData['signature'])) {
            //暂时使用
            $userSign['mobile'] = strlen($userSign['mobile'])>11 ? substr($userSign['mobile'], 0, 11) : $userSign['mobile'];
            //注册个人账户
            if (!isset($signData['accountId']) || empty($signData['accountId'])) {
                $accountData = $this->addPersonAccount($userSign);
                if ($accountData['errCode'] > 0) {
                    return array();
                }
                $accountId = $accountData['accountId'];
            } else {
                $accountId = $signData['accountId'];
            }
            //生成个人电子印章
            if (!isset($signData['signature']) || empty($signData['signature'])) {
                $signTemp = $this->addPersonTemplateSeal($user_id, $accountId);
                if ($signTemp['errCode'] > 0) {
                    return array();
                }
                $signature = $signTemp['signature'];
            } else {
                $signature = $signData['signature'];
            }

            $signData = array(
                'user_id' => $user_id,
                'accountId' => $accountId,
                'signature' => $signature,
            );
        }
        return $signData;
    }

    /**
     * 获取企业签章信息
     * @return mixed
     */
    public function getOrganizeSign($type)
    {
        if ($type < 2) {
            return array();
        }
        //查询数据表中是否保存有签章信息
        $signData = \Core::db()->select('user_id,accountId,signature')->from('user_signature')->where(array('is_effect'=>1,'type'=>$type))->execute()->row();

        //无签章信息，则去e签宝上生成获取
        if (empty($signData) || empty($signData['accountId']) || empty($signData['signature'])) {
            //注册企业账户
            if (!isset($signData['accountId']) || empty($signData['accountId'])) {
                $accountData = $this->addOrgAccount($type);
                if ($accountData['errCode'] > 0) {
                    return array();
                }
                $user_id = $accountData['user_id'];
                $accountId = $accountData['accountId'];
            } else {
                $user_id = $signData['user_id'];
                $accountId = $signData['accountId'];
            }

            //生成企业电子签章
            if (!isset($signData['signature']) || empty($signData['signature'])) {
                $signTemp = $this->addOrgTemplateSeal($user_id, $accountId, $type);
                if ($signTemp['errCode'] > 0) {
                    return array();
                }
                $signature = $signTemp['signature'];
            } else {
                $signature = $signData['signature'];
            }

            $signData = array(
                'user_id' => $user_id,
                'accountId' => $accountId,
                'signature' => $signature,
            );
        }
        return $signData;
    }

    /**
     * 平台用户签署（支持多用户盖章）
     * @param $userSign array 用户信息内容
     * @param int $type 1-个人用户盖章签署；2-企业用户盖章签署（小树普惠）；3-企业用户盖章签署（小树金融）
     * @param $posPage  int 需盖章所在页码
     * @param $posX     int 盖章位置横坐标
     * @param $posY     int 盖章位置纵坐标
     * @param $srcPdfFile   string 待盖章文件（文件完整绝对路径）
     * @return array|mixed  array('errCode'=>0,'msg'=>'成功','errorShow'=>'','signServiceId'=>1995066)
     */
    public function userSignPDF($userSign, $type=1, $posPage, $posX, $posY, $srcPdfFile)
    {
        //企业电子签章（小树普惠）
        if ($type == 2) {
            $SignData = $this->getOrganizeSign(2);
        } elseif ($type == 3) {
            $SignData = $this->getOrganizeSign(3);
        }
        //个人用户电子签章
        else {
            $SignData = $this->getPersonSign($userSign);
        }

        $accountId = $SignData['accountId'];
        $sealData = $SignData['signature'];

        $signType = SignType::SINGLE;
        //盖章位置【坐标解释：以左下角为原点，posX为横向（正数向右），posY纵向（正数向上）】
        $signPos = array(
            'posPage' => $posPage,
            'posX' =>  $posX,
            'posY' => $posY,
            'key' =>  '',
            'width' => ''
        );
        //盖章后的文件（名称后缀：_dist.pdf）
        $dstPdfFile = str_replace('.pdf', '_dist.pdf', $srcPdfFile);

        //已盖章文件名称修改成原待盖章文件名称，以便实现在同一文件内存在多个盖章
        if (file_exists($dstPdfFile)) {
            @rename($dstPdfFile, $srcPdfFile);
        }

        $signFile = array(
            'srcPdfFile' => $srcPdfFile,
            'dstPdfFile' => $dstPdfFile,
            'fileName' => '',
            'ownerPassword' => ''
        );
        $res = $this->esign->userSafeMobileSignPDF($accountId, $signFile, $signPos, $signType, $sealData,'','',true);

        //删除待盖章文件
        @unlink($srcPdfFile);
        return $res;
    }

    /**
     * 要保全的文件
     * @param $docFilePath string 完整的文件路径
     * @param $docName     string 文档名称
     * @param $signServerIds    string 签章成功后 signServerId 的字符串集合，多个id用 逗号“,”分割
     * @return array    例：array('errCode'=>0,'msg'=>'成功','errorShow'=>'','docId'=>449749)
     */
    public function saveSignedFile($docFilePath, $docName, $signServerIds)
    {
        $res = $this->esign->saveSignedFile($docFilePath, $docName, $signServerIds);

        return $res;
    }

    /**
     * 获取保全在e签宝的已签章PDF文件地址
     * @param $docId
     * @return mixed    array('errCode'=>0,'msg'=>'成功','errorShow'=>'','downUrl'=>docLink)
     */
    public function getSignedFile($docId)
    {
        return $this->esign->getSignedFile($docId);
    }
}
