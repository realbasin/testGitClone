<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * E签宝PDF协议
 * Class task_SignPdf
 */
class  task_SignPdf extends Task
{

    function execute(CliArgs $args)
    {
        $deal_ids = \Core::dao('DealQueue')->getPreparedDealIdsForContact();
        if (!empty($deal_ids)) {
            $contractBusiness = \Core::business('Contract');
            foreach ($deal_ids as $id) {
                $response = $contractBusiness->createSignPdf($id);
            }
        }
        return 'done';
    }
}