<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 *
 */
class business_regionpartner extends Business
{

    public function autoClosePartner()
    {
        $regionPartnerDao = \Core::dao('regionpartner');
        $effect_partner = $regionPartnerDao->getEffectAllRegionPartner();

        $conf = \Core::dao('regionpartnerconf')->getRowByName('auto_close_days');
        $close_days = ($conf) ? intval($conf->value) : 0;

        $days = getXSConf('YZ_IMPSE_DAY');
        $time = strtotime(date('Y-m-d'));
        $info = '';
        foreach ($effect_partner as $partner) {
            $summary = $this->getPartnerGuaranteesSummary($partner['id'], $days);
            $mypartner = $regionPartnerDao->getRegionPartner($partner['id']);
            $mypartner['is_close'] = 0;
            if ($summary['guarantees_balance'] < $summary['guarantees_money'] && $summary['guarantees_balance'] < $summary['guarantees_max']) {
                $mypartner['is_close'] = 1;
            }

            $logDao = \Core::dao('regionpartnerbalancelog');
            $log = $logDao->getAllByPartnerId($partner['id']);
            if ($log) {
                if (floatval($log[0]['paid_amount']) == 0 && (strtotime($log[0]['unpaid_date']) + $close_days * 24 * 3600) <= $time) {
                    $mypartner['is_close'] = 1;
                }
            }
            if ($mypartner['is_close']) {
                $info .= "[伙伴ID:" . $mypartner['id'] . ",姓名:" . $mypartner['name'] . "] ";
            }

            $regionPartnerDao->update($mypartner, ['id' => $partner['id']]);
        }
        $str = '本次自动关闭这些城市合作伙伴: ' . $info . ',请知悉!';

        echo $str;

    }

    public function getPartnerGuaranteesSummary($partner_id, $days)
    {
        $partner = $this->getRegionPartner($partner_id);

        $myids = "";
        $types = ['all', 'A1', 'A2', 'A3', 'B'];
        $today = date('Y-m-d');
        for ($j = 1; $j <= 4; $j++) {
            $ids = $this->getDealIds($partner_id, $types[$j]);
            $deal_ids[] = $ids;
            if ($j == 1) {
                $myids = $ids;
            } else {
                $myids .= "," . $ids;
            }
        }

        $now_time = strtotime(date('Y-m-d')) - intval($partner->expired_repay_debit_days) * 24 * 3600;
        $sql_str = "select sum(b.repay_money+b.manage_money+b.mortgage_fee
            +if(UNIX_TIMESTAMP('{$today}')<=UNIX_TIMESTAMP(b.repay_date),0,
            if(CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600))>={$days},
            b.repay_money*te.impose_fee_day2/100*CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600)),
            b.repay_money*te.impose_fee_day1/100*CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600))))
            +if(UNIX_TIMESTAMP('{$today}')<=UNIX_TIMESTAMP(b.repay_date),0,
            if(CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600))>={$days},
            b.repay_money*te.manage_impose_fee_day2/100*CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600)),
            b.repay_money*te.manage_impose_fee_day1/100*CEIL((UNIX_TIMESTAMP('{$today}')-UNIX_TIMESTAMP(b.repay_date))/(24*3600))))
            ) no_repay_money_total,
          sum(if((b.repay_time<{$now_time} and not isnull(c.type)),b.repay_money,0)) expired_no_repay_money_A1_total,
          sum(if((b.repay_time<{$now_time} and not isnull(d.type)),b.repay_money,0)) expired_no_repay_money_A2_total,
          sum(if((b.repay_time<{$now_time} and not isnull(e.type)),b.repay_money,0)) expired_no_repay_money_A3_total,
          sum(if((b.repay_time<{$now_time} and not isnull(f.type)),b.repay_money,0)) expired_no_repay_money_B_total
            from _tablePrefix_loan_base a inner join _tablePrefix_deal_repay b on a.id=b.deal_id
            inner join _tablePrefix_deal_loan_type t on a.type_id = t.id 
            inner join _tablePrefix_deal_loan_type_extern te on t.id = te.loan_type_id 
            left join (select 'A1' as type,'{$deal_ids[0]}' as ids) as c on  INSTR(c.ids,a.id)>0
            left join (select 'A2' as type,'{$deal_ids[1]}' as ids) as d on  INSTR(d.ids,a.id)>0
            left join (select 'A3' as type,'{$deal_ids[2]}' as ids) as e on  INSTR(e.ids,a.id)>0
            left join (select 'B' as type,'{$deal_ids[3]}' as ids) as f on  INSTR(f.ids,a.id)>0
            where b.has_repay = 0 and a.id in ({$myids}) and a.is_delete<>1";

        $list = \Core::db()->execute($sql_str);
        $result = array();
        $guarantees_money = floatval($list[0]['no_repay_money_total']) * floatval($partner['guarantees_rate']) / 100;

        $expired_close_money_total = floatval($list[0]['expired_no_repay_money_A1_total']) * floatval($partner['guarantees_A1_rate']) / 100 +
            floatval($list[0]['expired_no_repay_money_A2_total']) * floatval($partner['guarantees_A2_rate']) / 100 +
            floatval($list[0]['expired_no_repay_money_A3_total']) * floatval($partner['guarantees_A3_rate']) / 100 +
            floatval($list[0]['expired_no_repay_money_B_total']) * floatval($partner['guarantees_B_rate']) / 100;
        $result['no_repay_money_total'] = floatval($list[0]['no_repay_money_total']);
        $result['expired_close_money_total'] = sprintf("%.2f", floatval($expired_close_money_total));
        $result['guarantees_money'] = sprintf("%.2f", floatval($guarantees_money));
        $result['guarantees_min'] = sprintf("%.2f", floatval($partner['guarantees_min']));
        $result['guarantees_max'] = sprintf("%.2f", floatval($partner['guarantees_max']));
        $guarantees_balance = floatval($partner['guarantees_amount']) - $expired_close_money_total;
        $result['guarantees_balance'] = sprintf("%.2f", $guarantees_balance);

        return $result;
    }

    /**
     * 计算各类借款ids
     * A1：本地区自然流量
     * A2:本地区城市合伙人业务员推荐流量
     * A3:本地区流失流量
     * B:本地区城市合伙人业务员推荐的非本地区流量
     */
    public function getDealIds($partner_id = 0, $type = "A1")
    {
        $partner = $this->getRegionPartner($partner_id);
        $effect_date = ($partner) ? $partner['effect_date'] : 0;
        if (empty($effect_date)) {
            $effect_time = strtotime('2069-12-31');
        } else {
            $effect_time = strtotime($effect_date);
        }

        $ids = "0";
        if ($type == "A1") {
            $regions = \Core::db()->execute("select DISTINCT region_id from _tablePrefix_region_partner_relation where partner_id =" . $partner_id);
            foreach ($regions as $value) {
                $ids .= "," . $value->region_id;
            }

            $sql = "select DISTINCT deal_id from _tablePrefix_deal_region_link a inner join _tablePrefix_loan_base b on a.deal_id=b.id inner join _tablePrefix_user c on b.user_id=c.id where c.pid=0 and a.region_id in ({$ids}) and b.create_time>={$effect_time}";
            $deals = \Core::db()->execute($sql);
            $ids = "0";
            foreach ($deals as $value) {
                $ids .= "," . $value->deal_id;
            }
        } elseif ($type == "A2") {
            $regions = \Core::db()->execute("select DISTINCT region_id from _tablePrefix_region_partner_relation where partner_id =" . $partner_id);
            foreach ($regions as $value) {
                $ids .= "," . $value->region_id;
            }
            $sql = "select DISTINCT deal_id from _tablePrefix_deal_region_link a inner join _tablePrefix_loan_base b on a.deal_id=b.id inner join _tablePrefix_user c on b.user_id=c.id inner join _tablePrefix_user d on d.id=c.pid where d.rpid={$partner_id} and a.region_id in ({$ids}) and b.create_time>={$effect_time}";
            $deals = \Core::db()->execute($sql);
            $ids = "0";
            foreach ($deals as $value) {
                $ids .= "," . $value->deal_id;
            }
        } elseif ($type == "A3") {
            $regions = \Core::db()->execute("select DISTINCT region_id from _tablePrefix_region_partner_relation where partner_id = " . $partner_id);
            foreach ($regions as $value) {
                $ids .= "," . $value->region_id;
            }
            $sql = "select DISTINCT deal_id from _tablePrefix_deal_region_link a inner join _tablePrefix_loan_base b on a.deal_id=b.id inner join _tablePrefix_user c on b.user_id=c.id inner join _tablePrefix_user d on d.id=c.pid where d.rpid != {$partner_id} and a.region_id in ({$ids}) and b.create_time>={$effect_time}";
            $deals = \Core::db()->execute($sql);
            $ids = "0";
            foreach ($deals as $value) {
                $ids .= "," . $value->deal_id;
            }
        } elseif ($type == "B") {
            $regions = \Core::db()->execute("select DISTINCT region_id from _tablePrefix_region_partner_relation where partner_id =" . $partner_id);
            foreach ($regions as $value) {
                $ids .= "," . $value->region_id;
            }
            $sql = "select DISTINCT deal_id from _tablePrefix_deal_region_link a inner join _tablePrefix_loan_base b on a.deal_id=b.id inner join _tablePrefix_user c on b.user_id=c.id inner join _tablePrefix_user d on d.id=c.pid where  d.rpid={$partner_id} and not a.region_id in ({$ids}) and b.create_time>={$effect_time}";
            $deals = \Core::db()->execute($sql);
            $ids = "0";
            foreach ($deals as $value) {
                $ids .= "," . $value->deal_id;
            }
        }
        return $ids;
    }

}