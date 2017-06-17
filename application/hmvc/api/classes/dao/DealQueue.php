<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealQueue extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'deal_id'//deal_id
        , 'create_time'//create_time
        , 'contract_status'//生成协议状态：0未生成，1已生成
        , 'contract_time'//生成协议时间
        , 'contact_lock'//合同锁，1代表正在生成
        , 'contact_lock_time'//锁定时间
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_queue';
    }

    /**
     * 获取准备生成协议的借款ID，考虑到生成时间较长，默认一次5个
     * @param $cnt
     * @return array
     */
    public function getPreparedDealIdsForContact($cnt = 5)
    {
        $cnt = intval($cnt);
        $ids = [];
        if ($cnt != 0) {
            //获取未锁定的 & 10min以前的
            $time_limit = 10 * 60;
            $time = time() - $time_limit;
            $sql = "SELECT * FROM _tablePrefix_deal_queue WHERE contract_status = 0 AND (contact_lock = 0 OR (contact_lock = 1 AND contact_lock_time < $time))";
            $dealList = $this->getDb()->execute($sql);

            foreach ($dealList as $k => $deal) {
                $deal['contact_lock'] = 1;
                $deal['contact_lock_time'] = time();

                if ($this->update($deal, ['deal_id' => $deal['deal_id']])) {
                    $ids[] = $deal['deal_id'];
                    $cnt--;
                    if ($cnt <= 0) break;
                }
            }
        }
        return $ids;
    }

}
