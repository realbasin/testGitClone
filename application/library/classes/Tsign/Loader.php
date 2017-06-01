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
     * @param $real_name
     * @param $mobile
     * @param $id_no
     * @return array
     */
    public function addPersonAccount($real_name, $mobile, $id_no)
    {
        $ret = $this->esign->addPersonAccount($mobile, $real_name, $id_no);
        if (!empty($ret)) {
            $result = array();
            $result['errCode'] = $ret['errCode'];
            $result['accountId'] = $ret['accountId'];
            $result['accountId_error_msg'] = $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '';
            return $result;
        }
        return array('errCode'=>1,'msg'=>'e签宝添加个人帐号请求失败');
    }

    /**
     * 删除个人账户
     * @param $accountId
     * @return mixed
     */
    public function delUserAccount($accountId)
    {
        $res = $this->esign->delUserAccount($accountId);
        if (in_array($res['errCode'], array(0,1007))) {
            return true;
        }
        return false;
    }

    /**
     * 添加个人模板印章
     * @param $accountId
     * @return array|bool|mix|mixed|stdClass|string
     */
    public function addPersonTemplateSeal($accountId)
    {
        $ret = $this->esign->addTemplateSeal(
            $accountId,
            $templateType = PersonTemplateType::RECTANGLE,
            $color = SealColor::RED
        );
        if (!empty($ret)) {
            $result = array();
            $result['errCode'] = $ret['errCode'];
            $result['signature'] = $ret['errCode']==0 ? $ret['imageBase64']: '';
            $result['signature_error_msg'] = $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '';
            return $result;
        }
        return array('errCode'=>1,'msg'=>'e签宝添加个人模板印章请求失败');
    }

    //添加企业账号
    public function addOrgAccount($type=2)
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
            $result = array();
            $result['errCode'] = $res['errCode'];
            $result['user_id'] = $user_id;
            $result['accountId'] = $res['accountId'];
            $result['accountId_error_msg'] = $res['errCode']>0 ? json_encode($res, JSON_UNESCAPED_UNICODE) : '';
            return $result;
        }
        return array('errCode'=>1,'msg'=>'e签宝添加企业账号请求失败');
    }

    //更新企业账号
    public function updateOrgAccount($accountId, $updateData=array())
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
            $result = array(
                'errCode' => $res['errCode'],
                'accountId' => $res['accountId'],
                'accountId_error_msg' => $res['errCode']>0 ? json_encode($res, JSON_UNESCAPED_UNICODE) : '',
            );
            return $result;
        }
        return array('errCode'=>1,'msg'=>'e签宝更新企业账号请求失败');
    }

    /**
     * 添加企业模板印章
     * @param $accountId
     * @return array|mixed|
     */
    public function addOrgTemplateSeal($accountId)
    {
        $ret = $this->esign->addTemplateSeal(
            $accountId,
            $templateType = OrganizeTemplateType::STAR,
            $color = SealColor::RED
        );
        if (!empty($ret)) {
            $userSign = array(
                'errCode' => $ret['errCode'],
                'signature' => $ret['errCode']==0 ? $ret['imageBase64']: '',
                'signature_error_msg' => $ret['errCode']>0 ? json_encode($ret, JSON_UNESCAPED_UNICODE) : '',
            );
            return $userSign;
        }
        return array('errCode'=>1,'msg'=>'e签宝企业模板印章请求失败');
    }

    /**
     * 平台用户签署（支持多用户盖章）
     * @param $userSign array 签章信息内容
     * @param $posPage  int 需盖章所在页码
     * @param $posX     int 盖章位置横坐标
     * @param $posY     int 盖章位置纵坐标
     * @param $srcPdfFile   string 待盖章文件（文件完整绝对路径）
     * @param $unlink   bool 是否删除文件
     * @return array|mixed  array('errCode'=>0,'msg'=>'成功','errorShow'=>'','signServiceId'=>1995066)
     */
    public function userSignPDF($userSign, $posPage, $posX, $posY, $srcPdfFile, $unlink=true)
    {
        $accountId = $userSign['accountId'];
        $sealData = $userSign['signature'];

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
        if ($unlink) {
            @unlink($srcPdfFile);
        }
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
        return $this->esign->saveSignedFile($docFilePath, $docName, $signServerIds);
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
