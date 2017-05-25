<?php
/**
 * User: Administrator
 * Date: 2017/1/12
 */

namespace Tsign\result;


class GetSignDetailResult extends AbstractResult
{
    public function parseData()
    {
        $response = $this->rawResponse;
        $result = array();
        if (isset($response['signDetail'])) {
            $detail = $response['signDetail'];
            $result['signers'] = $detail['objects'];
            $result['signTime'] = $detail['signTime'];
        }
        return array_merge($this->errInfo, $result);
    }

}