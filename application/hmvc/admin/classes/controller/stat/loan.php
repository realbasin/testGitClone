<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 借出统计
 */
class  controller_stat_loan extends controller_sysBase {

	public function before() {

	}

	//借出汇总
	//查询缓存10分钟
	public function do_all() {
		$datestart = \Core::post('datestart');
		$dateend = \Core::post('dateend');
		$where = array();
		if ($datestart) {
			$timestart = strtotime($datestart);
		} else {
			$timestart = 0;
		}
		if ($dateend) {
			$timeend = strtotime($dateend);
		} else {
			$timeend = 0;
		}

		$daoRepay = \Core::dao('loan_dealloadrepay');
		$daoUser = \Core::dao('user_user');
		$daoLoad = \Core::dao('loan_dealload');
		$data = $daoRepay -> getStatLoanAll($timestart, $timeend);
		$data['balancetotal'] = $daoUser -> getStatBalanceTotal();
		$data['rebatetotal'] = $daoLoad -> getStatRebateTotal();
		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;
		\Core::view() -> set($data) -> load('stat_loanAll');
	}

	//投资人
	//缓存10分钟
	public function do_investor() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_investor');
	}

	//投资人json数据
	public function do_investor_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoLoad = \Core::dao('loan_dealload');
		$data = $daoLoad -> getInvest(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			$datarow = array();
			$datarow['createdate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarow['moneytotal'] = "0.00";
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}

	//导出投资人excel
	public function do_investor_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['投资人数量'] = 'integer';
		$header['投资金额'] = 'price';

		$daoLoad = \Core::dao('loan_dealload');
		//Excel内容
		$data = $daoLoad -> getInvest(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出投资人/金额统计列表(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('投资人/金额统计列表(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//投资额
	public function do_investAmount() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_investAmount');
	}

	public function do_investAmount_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoLoad = \Core::dao('loan_dealload');
		$data = $daoLoad -> getInvestAmount(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			$datarow = array();
			$datarow['createdate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarow['sucinvest'] = "0.00";
			$datarow['frozeninvest'] = "0.00";
			$datarow['failinvest'] = "0.00";
			$datarow['prizeinvest'] = "0.00";
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}

	public function do_investAmount_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['投资人次'] = 'integer';
		$header['投资成功金额'] = 'price';
		$header['冻结投资金额'] = 'price';
		$header['投资失败金额'] = 'price';
		$header['奖励金额'] = 'price';

		$daoLoad = \Core::dao('loan_dealload');
		//Excel内容
		$data = $daoLoad -> getInvestAmount(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出成功投资比率汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('成功投资比率汇总(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//回款统计
	public function do_payment() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_payment');
	}

	public function do_payment_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay -> getPayment(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			//空数据的情况
			$datarow = array();
			$datarow['repaydate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarow['investrepay'] = "0.00";
			$datarow['investcapital'] = "0.00";
			$datarow['investinterest'] = "0.00";
			$datarow['repaypenalty'] = "0.00";
			$datarow['repayfine'] = "0.00";
			$datarow['investfee'] = "0.00";
			$datarow['loanfee'] = "0.00";
			$datarow['platfromincome'] = "0.00";
			$datarowend = $datarow;
			$datarowend['repaydate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}

	public function do_payment_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['收款人次'] = 'integer';
		$header['回款总额'] = 'price';
		$header['回款本金'] = 'price';
		$header['回款利息'] = 'price';
		$header['提前还款罚息'] = 'price';
		$header['逾期还款罚金'] = 'price';
		$header['管理费(投资方)'] = 'price';
		$header['管理费(借款方)'] = 'price';
		$header['平台收入'] = 'price';

		$daoRepay = \Core::dao('loan_dealloadrepay');
		//Excel内容
		$data = $daoRepay -> getPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出回款统计汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('回款统计汇总(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//待收统计
	public function do_due() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_due');
	}

	public function do_due_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay -> getDue(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			//空数据的情况
			$datarow = array();
			$datarow['repaydate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarow['investrepay'] = "0.00";
			$datarow['investcapital'] = "0.00";
			$datarow['investinterest'] = "0.00";
			$datarowend = $datarow;
			$datarowend['repaydate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}

	public function do_due_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['待收款人次'] = 'integer';
		$header['待收总额'] = 'price';
		$header['待收本金'] = 'price';
		$header['待收利息'] = 'price';

		$daoRepay = \Core::dao('loan_dealloadrepay');
		//Excel内容
		$data = $daoRepay -> getPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出待收统计汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('待收统计汇总(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//待收明细
	public function do_dueDetail() {
		\Core::view() -> load('stat_dueDetail');
	}

	//获取待收明细JSON
	//如果有查询条件过来就先查user表，如果没有查询条件，就先查load_repay表
	//将mysql查询计算转到逻辑计算
	public function do_dueDetail_json() {
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');

		$sortorder = \Core::postGet('sortorder');

		$where = array();

		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//查询条件
		if (\Core::postGet('query') && in_array(\Core::postGet('qtype'), array('user_id', 'user_name', 'real_name', 'mobile', 'idno'))) {
			switch(\Core::postGet('qtype')) {
				case 'user_id' :
					$where['id'] = \Core::postGet('query');
					break;
				case 'user_name' :
					$where['user_name'] = \Core::postGet('query');
					break;
				case 'real_name' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::postGet('query');
					break;
				case 'mobile' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::postGet('query');
					break;
				case 'idno' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(idno_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::postGet('query');
					break;
				default :
					break;
			}
		}

		$daoRepay = \Core::dao('loan_dealloadrepay');
		$daoUser = \Core::dao('user_user');

		$userFields = "id,user_name,
			AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') AS real_name,
			AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "') AS mobile,
			AES_DECRYPT(money_encrypt,'" . AES_DECRYPT_KEY . "') AS money,
			lock_money";

		$dataReturn = array();
		$dataReturn['page'] = $page;
		$dataReturn['total'] = 0;
		$dataReturn['rows'] = array();

		if ($where) {
			//有查询条件，先查user表
			$sort = array();
			if ($sortorder) {
				$sort = array('id' => $sortorder);
			}
			$data = $daoUser -> findAll($where, $sort, null, $userFields);
			//如果有数据开始合并id，查repay表
			if ($data) {
				$idarr = array();
				foreach ($data as $v) {
					$idarr[] = $v['id'];
				}
				$dataRepay = $daoRepay -> getDueDetail($idarr);
				if ($dataRepay) {
					foreach ($dataRepay as $v) {
						$row = array();
						$row['id'] = $v['u_id'];
						$row['cell'][] = $v['u_id'];
						$udata = $data[$v['u_id']];
						$row['cell'][] = $udata['user_name'];
						$row['cell'][] = $udata['real_name'];
						$row['cell'][] = $udata['mobile'];
						$row['cell'][] = $udata['money'];
						$row['cell'][] = $udata['lock_money'];
						$row['cell'][] = $v['self_money'];
						$dataReturn['rows'][] = $row;
					}
				}
			}
		} else {
			//无查询条件，使用分页方式先查deal_load_repay表
			$repayWhere = array('has_repay' => 0);
			$repayFields = "IF(t_user_id=0,user_id,t_user_id) AS u_id,SUM(self_money) AS self_money";
			$sort = array();
			if ($sortorder) {
				$sort = array('u_id' => $sortorder);
			}
			$groupBy = "IF(t_user_id=0,user_id,t_user_id)";
			$data = $daoRepay -> getFlexPage($page, $pagesize, $repayFields, $repayWhere, $sort, $groupBy);
			
			if ($data['total'] > 0) {
				$idarr = array();
				foreach ($data['rows'] as $v) {
					$idarr[] = $v['u_id'];
				}
				$userWhere = array('id' => $idarr);
				$dataUser = $daoUser -> findAll($userWhere, array(), null, $userFields, 'id');
				foreach ($data['rows'] as $v) {
					$row = array();
					$row['id'] = $v['u_id'];
					$row['cell'][] = $v['u_id'];
					//以下代码这样写是因为本地测试库数据未同步完全，避免出错
					$udata = \Core::arrayKeyExists($v['u_id'], $dataUser)?$dataUser[$v['u_id']]:array();
					if(!$udata){
						$udata['user_name']='';
						$udata['real_name']='';
						$udata['mobile']='';
						$udata['money']='';
						$udata['lock_money']='';
					}
					//////////////////////////////////////////////////////
					$row['cell'][] = $udata['user_name'];
					$row['cell'][] = $udata['real_name'];
					$row['cell'][] = $udata['mobile'];
					$row['cell'][] = $udata['money'];
					$row['cell'][] = $udata['lock_money'];
					$row['cell'][] = $v['self_money'];
					$dataReturn['rows'][] = $row;
				}
			}
		}
        $dataReturn['total']=count($dataReturn['rows']);
		echo @json_encode($dataReturn);
	}

	//待收明细导出
	public function do_dueDetail_export() {
		$id = \Core::getPost("id");
		$sortorder = \Core::getPost('sortorder');
		$where = array();
		$ids=array();
		if ($id) {
			$ids = explode(",", $id);
		}
		//查询条件
		if (\Core::getPost('query') && in_array(\Core::getPost('qtype'), array('user_id', 'user_name', 'real_name', 'mobile', 'idno'))) {
			switch(\Core::getPost('qtype')) {
				case 'user_id' :
					$where['id'] = \Core::getPost('query');
					break;
				case 'user_name' :
					$where['user_name'] = \Core::getPost('query');
					break;
				case 'real_name' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::getPost('query');
					break;
				case 'mobile' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::getPost('query');
					break;
				case 'idno' :
					$where["TRIM(BOTH ' ' FROM AES_DECRYPT(idno_encrypt,'" . AES_DECRYPT_KEY . "'))"] = \Core::getPost('query');
					break;
				default :
					break;
			}
		}

		$daoRepay = \Core::dao('loan_dealloadrepay');
		$daoUser = \Core::dao('user_user');

		$userFields = "id,user_name,
			AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') AS real_name,
			AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "') AS mobile,
			AES_DECRYPT(money_encrypt,'" . AES_DECRYPT_KEY . "') AS money,
			lock_money";

		$dataReturn = array();

		if ($where) {
			//有查询条件，先查user表
			$sort = array();
			if ($sortorder) {
				$sort = array('id' => $sortorder);
			}
			if($ids){
				$where['id']=$ids;
			}
			$data = $daoUser -> findAll($where, $sort, null, $userFields);
			//如果有数据开始合并id，查repay表
			if ($data) {
				$idarr = array();
				foreach ($data as $v) {
					$idarr[] = $v['id'];
				}
				$dataRepay = $daoRepay -> getDueDetail($idarr);
				if ($dataRepay) {
					foreach ($dataRepay as $v) {
						$row = array();
						$row[] = $v['u_id'];
						$udata = $data[$v['u_id']];
						$row[] = $udata['user_name'];
						$row[] = $udata['real_name'];
						$row[] = $udata['mobile'];
						$row[] = $udata['money'];
						$row[] = $udata['lock_money'];
						$row[] = $v['self_money'];
						$dataReturn[] = $row;
					}
				}
			}
		} else {
			//无查询条件，先查deal_load_repay表
			$data = $daoRepay -> getDueDetail($ids);
			if ($data) {
				$idarr = array();
				foreach ($data as $v) {
					$idarr[] = $v['u_id'];
				}
				$userWhere = array('id' => $idarr);
				$dataUser = $daoUser -> findAll($userWhere, array(), null, $userFields, 'id');
				foreach ($data as $v) {
					$row = array();
					$row[] = $v['u_id'];
					//以下代码这样写是因为本地测试库数据未同步完全，避免出错
					$udata = \Core::arrayKeyExists($v['u_id'], $dataUser)?$dataUser[$v['u_id']]:array();
					if(!$udata){
						$udata['user_name']='';
						$udata['real_name']='';
						$udata['mobile']='';
						$udata['money']='';
						$udata['lock_money']='';
					}
					//////////////////////////////////////////////////////
					$row[] = $udata['user_name'];
					$row[] = $udata['real_name'];
					$row[] = $udata['mobile'];
					$row[] = $udata['money'];
					$row[] = $udata['lock_money'];
					$row[] = $v['self_money'];
					$dataReturn[] = $row;
				}
			}
		}
        //开始导出
        $header = array();
		$header['会员ID'] = 'integer';
		$header['会员名称'] = 'string';
		$header['真实姓名'] = 'string';
		$header['手机号码'] = 'string';
		$header['账户余额'] = 'price';
		$header['冻结资金'] = 'price';
		$header['待收本金'] = 'price';
		$dao_log = \Core::dao('sys_admin_log');
		$export_range = $id ? $id : '全部';
		$this -> log('导出待收明细统计' . '[会员id:' . $export_range . ']', 'export');
		exportExcel('待收明细统计', $header, $dataReturn);
	}

	//投资排名
	public function do_investRank() {

	}

	//投资比例
	public function do_investProportion() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_investProportion');
	}

	public function do_investProportion_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoLoad = \Core::dao('loan_dealload');
		$data = $daoLoad -> getInvestProportion(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			//空数据的情况
			$datarow = array();
			$datarow['createdate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarow['p1'] = "0";
			$datarow['p2'] = "0";
			$datarow['p3'] = "0";
			$datarow['p4'] = "0";
			$datarow['p5'] = "0";
			$datarow['p6'] = "0";
			$datarow['p7'] = "0";
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}

	public function do_investProportion_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['成功投资总人次'] = 'integer';
		$header['5千以下'] = 'integer';
		$header['5千(含)至1万'] = 'integer';
		$header['1万(含)至5万'] = 'integer';
		$header['5万(含)至10万'] = 'integer';
		$header['10万(含)至20万'] = 'integer';
		$header['20万(含)至50万'] = 'integer';
		$header['50万(含)以上'] = 'integer';

		$daoLoad = \Core::dao('loan_dealload');
		//Excel内容
		$data = $daoLoad -> getInvestProportion(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出投资额比例统计汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('投资额比例统计汇总(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

}
