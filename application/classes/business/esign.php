<?php
/**
 * e签宝业务处理逻辑
 */
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_esign extends Business {
    private $esign;

	public function __construct() {
		$this->esign = \Core::library('Tsign/Loader');
	}

    /**
     * 删除个人签章信息
     * @param $esignId
     * @param $accountId
     * @return mixed
     */
    public function delUserPersonSign($esignId, $accountId)
    {
        $res = $this->esign->delUserAccount($accountId);
        if ($res) {
            $delete = array('is_effect'=>0);
            $where = array('id'=>$esignId);
            $res = \Core::dao('user/usersignature')->update($delete, $where);
        }
        return $res;
    }

    /**
     * 获取普通用户私人电子印章信息
     * @param $user_id
     * @param $real_name
     * @param $mobile
     * @param $id_no
     * @return mixed
     */
	public function getUserPersonSign($user_id, $real_name='', $mobile='', $id_no='') {
        $where = array('type'=>1,'is_effect'=>1,'user_id'=>$user_id);
        $signData = \Core::dao('user/usersignature')->getUserSignByCondition('user_id,accountId,signature', $where);

        if (empty($signData) || empty($signData['accountId']) || empty($signData['signature'])) {
            //未创建e签宝账户
            if (!isset($signData['accountId']) || empty($signData['accountId'])) {
                $responseData = $this->esign->addPersonAccount($real_name, $mobile, $id_no);
                /*-------------保存e签宝账户---start----------*/
                $userSign = array(
                    'user_id' => $user_id,
                    'real_name' => $real_name,
                    'mobile' => $mobile,
                    'id_no' => $id_no,
                    'accountId' => $responseData['accountId'],
                    'accountId_error_msg' => $responseData['accountId_error_msg'],
                    'type' => 1,
                    'is_effect' => 1,
                    'create_time' => getGmtime(),
                );
                $res = $this->saveUserSignature($userSign);
                /*-----------------------end---------------------*/
                if ($responseData['errCode'] > 0 || empty($res)) {
                    return array();
                }
                $accountId = $userSign['accountId'];
            } else {
                $accountId = $signData['accountId'];
            }

            //生成个人电子印章
            if (!isset($signData['signature']) || empty($signData['signature'])) {
                $signTemp = $this->esign->addPersonTemplateSeal($accountId);
                /*----------------更新电子印章-----start-------*/
                $updateSign = array(
                    'user_id'=> $user_id,
                    'signature'=>$signTemp['signature'],
                    'signature_error_msg'=>$signTemp['signature_error_msg'],
                );
                $res = $this->saveUserSignature($updateSign);
                /*--------------------end--------------------*/
                if ($signTemp['errCode'] > 0 || empty($res)) {
                    //\Core::dao('user_user')->update(array('is_esign'=>2), array('id'=>$user_id));
                    return array();
                } else {
                    //\Core::dao('user_user')->update(array('is_esign'=>1), array('id'=>$user_id));
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
     * 小树普惠【丙方公司签章】
     * @return mixed
     */
	public function getPuHuiOrganizeSign() {
        return $this->getOrganizeSign(2);
    }

    /**
     * 小树金融【丁方公司签章】
     * @return mixed
     */
    public function getInvestOrganizeSign() {
        return $this->getOrganizeSign(3);
    }

    /**
     * 生成公司签章
     * @param $type
     * @return mixed
     */
    protected function getOrganizeSign($type) {
        $where = array('type'=>$type,'is_effect'=>1);
        $signData = \Core::dao('user/usersignature')->getUserSignByCondition('user_id,accountId,signature', $where);

        //无签章信息，则去e签宝上生成获取
        if (empty($signData) || empty($signData['accountId']) || empty($signData['signature'])) {
            //注册企业账户
            if (!isset($signData['accountId']) || empty($signData['accountId'])) {
                $accountData = $this->esign->addOrgAccount($type);
                $userSign = array(
                    'user_id' => $accountData['user_id'],
                    'accountId' => $accountData['accountId'],
                    'accountId_error_msg' => $accountData['accountId_error_msg'],
                    'is_effect' => 1,
                    'type' => $type,
                    'create_time' => getGmtime(),
                );
                $res = $this->saveUserSignature($userSign, $type);
                if ($accountData['errCode'] > 0 || empty($res)) {
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
                $signTemp = $this->esign->addOrgTemplateSeal($accountId);
                $updateSign = array(
                    'user_id'=> $user_id,
                    'signature'=>$signTemp['signature'],
                    'signature_error_msg'=>$signTemp['signature_error_msg'],
                );
                $res = $this->saveUserSignature($updateSign, $type);
                if ($signTemp['errCode'] > 0 || empty($res)) {
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
     * @param $userSign array 签章信息内容
     * @param $posPage  int 需盖章所在页码
     * @param $posX     int 盖章位置横坐标
     * @param $posY     int 盖章位置纵坐标
     * @param $srcPdfFile   string 待盖章文件（文件完整绝对路径）
     * @return array|mixed  array('errCode'=>0,'msg'=>'成功','errorShow'=>'','signServiceId'=>1995066)
     */
    public function userSignPDF($userSign, $posPage, $posX, $posY, $srcPdfFile) {
        return $this->esign->userSignPDF($userSign, $posPage, $posX, $posY, $srcPdfFile);
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

    /**
     * 保存或更新签章信息
     * @param $userSign
     * @param int $type
     * @return mixed
     */
    protected function saveUserSignature($userSign, $type=1) {
        $where = array('type'=>$type,'is_effect'=>1,'user_id'=>$userSign['user_id']);
        $signId = intval(\Core::dao('user/usersignature')->getUserSignByCondition('id', $where, 'one'));

        if ($signId > 0) {
            $res = \Core::dao('user/usersignature')->update($userSign, array('id'=>$signId));
        } else {
            $res = \Core::dao('user/usersignature')->insert($userSign, true);
        }
        return $res;
    }
}