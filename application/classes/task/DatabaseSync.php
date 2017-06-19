<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 数据库同步
 * Class task_DatabaseSync
 */
class  task_DatabaseSync extends Task
{

    private $dealDao, $loanBaseDao, $loanBidDao, $loanExtDao;
    private $daoArr = [];

    function execute(CliArgs $args)
    {
        $this->init();
        $this->dealListSync();
    }

    private function init()
    {
        $this->dealDao = \Core::dao('Deal');
        $this->loanBaseDao = \Core::dao('LoanBase');
        $this->loanBidDao = \Core::dao('LoanBid');
        $this->loanExtDao = \Core::dao('LoanExt');

        $this->daoArr['Deal'] = $this->dealDao;
        $this->daoArr['LoanBase'] = $this->loanBaseDao;
        $this->daoArr['LoanBid'] = $this->loanBidDao;
        $this->daoArr['LoanExt'] = $this->loanExtDao;
    }

    /**
     * 同步单条记录
     * @param $deal
     */
    private function dealSync($deal)
    {
        $loanBaseColumnList = $this->loanBaseDao->getColumns();
        $loanBase = [];
        foreach ($loanBaseColumnList as $loanBaseColumn) {
            if (isset($deal[$loanBaseColumn])) {
                $loanBase[$loanBaseColumn] = $deal[$loanBaseColumn];
            }
        }
        $loanBase['second_audit_admin_id'] = 0;//@TODO    deal表中没有，待处理
        $loanBase['source_id'] = 0;//@TODO    deal表中没有，待处理


        $loanBidColumnList = $this->loanBidDao->getColumns();
        $loanBid = [];
        foreach ($loanBidColumnList as $loanBidColumn) {
            if (isset($deal[$loanBidColumn])) {
                $loanBid[$loanBidColumn] = $deal[$loanBidColumn];
            }
        }
        $loanBid['loan_id'] = $deal['id'];
        $loanBid['collec_times'] = 0;//TODO  deal表中没有该字段，待处理
        $loanBid['collec_last_time'] = null;//TODO  deal表中没有该字段，待处理
        $loanBid['collec_allocation'] = 0;//TODO  deal表中没有该字段，待处理

        $configCommon = [
            'services_fee' => $deal['services_fee'],
            'score' => $deal['score'],
            'manage_fee' => $deal['manage_fee'],
            'user_loan_manage_fee' => $deal['user_loan_manage_fee'],
            'manage_impose_fee_day1' => $deal['manage_impose_fee_day1'],
            'manage_impose_fee_day2' => $deal['manage_impose_fee_day2'],
            'impose_fee_day1' => $deal['impose_fee_day1'],
            'impose_fee_day2' => $deal['impose_fee_day2'],
            'user_load_transfer_fee' => $deal['user_load_transfer_fee'],
            'transfer_day' => $deal['transfer_day'],
            'compensate_fee' => $deal['compensate_fee'],
            'user_bid_score_fee' => $deal['user_bid_score_fee'],
            'user_loan_interest_manage_fee' => $deal['user_loan_interest_manage_fee'],
            'user_loan_early_interest_manage_fee' => $deal['user_loan_early_interest_manage_fee'],
            'generation_position' => $deal['generation_position'],
            'user_bid_rebate' => $deal['user_bid_rebate']
        ];

        $configAmt = [
            'guarantees_amt' => $deal['guarantees_amt'],
            'l_guarantees_amt' => $deal['l_guarantees_amt'],
            'real_freezen_l_amt' => $deal['real_freezen_l_amt'],
            'un_real_freezen_l_amt' => $deal['un_real_freezen_l_amt'],
            'real_freezen_amt' => $deal['real_freezen_amt'],
            'un_real_freezen_amt' => $deal['un_real_freezen_amt'],
            'guarantor_amt' => $deal['guarantor_amt'],
            'guarantor_margin_amt' => $deal['guarantor_margin_amt'],
            'guarantor_real_freezen_amt' => $deal['guarantor_real_freezen_amt'],
            'un_guarantor_real_freezen_amt' => $deal['un_guarantor_real_freezen_amt'],
            'guarantor_pro_fit_amt' => $deal['guarantor_pro_fit_amt'],
            'guarantor_real_fit_amt' => $deal['guarantor_real_fit_amt']
        ];

        $loanExtColumnList = $this->loanExtDao->getColumns();
        $loanExt = [];
        foreach ($loanExtColumnList as $loanExtColumn) {
            if (isset($deal[$loanExtColumn])) {
                $loanExt[$loanExtColumn] = $deal[$loanExtColumn];
            }
        }
        $loanExt['loan_id'] = $deal['id'];
        $loanExt['config_common'] = serialize($configCommon);
        $loanExt['config_amt'] = serialize($configAmt);

        $this->loanBaseDao->insert($loanBase);
        $this->loanBidDao->insert($loanBid);
        $this->loanExtDao->insert($loanExt);
    }

    /**
     * 同步deal数据
     */
    private function dealListSync()
    {
        echo "dealListSync start......\n";
        $this->check();

        $dealDao = \Core::dao('Deal');
        $totalRecordNum = $dealDao->getTotalRecordNum();
        printf("deal table total record num:%s\n\n", $totalRecordNum);

        $pageSize = 1000;
        $pageCount = ceil($totalRecordNum / $pageSize);
        for ($i = 1; $i <= $pageCount; $i++) {
            $startTime = microtime(true);

            $startLimit = ($i - 1) * $pageSize;
            $endLimit = $startLimit + $pageSize;
            printf("get deal table data,start-end:%s-%s\n", $startLimit, $endLimit);
            $dealList = $dealDao->getDealList($startLimit, $pageSize);

            printf("sync deal,start-end:%s-%s\n", $startLimit, $endLimit);
            \Core::db()->begin();
            try {
                foreach ($dealList as $deal) {
                    $this->dealSync($deal);
                }
                \Core::db()->commit();
                printf("sync deal start-end:%s-%s success\n", $startLimit, $endLimit);
            } catch (Exception $e) {
                \Core::db()->rollback();
                printf("deal %s-%s data sync failed,fail msg:%s\n", $startLimit, $endLimit, $e->getMessage());
                printf("deal %s-%s data sync failed,executed rollback\n", $startLimit, $endLimit);
                exit("execute exit operation");
            }finally{
                $endTime = microtime(true);
                printf("execute time:%s sec\n\n", $endTime - $startTime);
            }
        }

        echo "dealListSync success\n";
    }

    /**
     * 检查
     */
    private function check()
    {
        $this->checkTableExists('LoanBase');
        $this->checkTableExists('LoanBid');
        $this->checkTableExists('LoanExt');

        $this->truncateTable('LoanBase');
        $this->truncateTable('LoanBid');
        $this->truncateTable('LoanExt');
    }

    /**
     * 检查表是否存在
     * @param $daoName
     */
    private function checkTableExists($daoName)
    {
        $dao = $this->daoArr[$daoName];
        $tableName = \Core::db()->getTablePrefix() . $dao->getTable();
        $value = \Core::db()->execute("SELECT table_name FROM information_schema.TABLES WHERE table_name ='$tableName'")->value('table_name');
        if (empty($value)) {
            printf("table %s not exists", $tableName);
            exit;
        }
    }

    /**
     * 清空表数据
     * @param $daoName
     */
    private function truncateTable($daoName)
    {
        $dao = $this->daoArr[$daoName];
        $tableName = \Core::db()->getTablePrefix() . $dao->getTable();
        \Core::db()->execute("truncate table $tableName");
    }
}