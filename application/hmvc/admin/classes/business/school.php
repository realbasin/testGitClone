<?php

/**
 * school business
 */
class  business_school extends Business
{
    public function getGradeTypeDesc($gradeType)
    {
        $result = '';
        switch ($gradeType) {
            case 1:
                $result = '一本';
                break;
            case 2:
                $result = '二本';
                break;
            case 3:
                $result = '三本';
                break;
            case 4:
                $result = '大专';
                break;
            default:
                $result = '未知';
                break;
        }

        return $result;
    }

    public function getInvestTypeDesc($investType)
    {
        $result = '';
        switch ($investType) {
            case 1:
                $result = '国立';
                break;
            case 2:
                $result = '公立';
                break;
            case 3:
                $result = '私立';
                break;
            case 4:
                $result = '民办';
                break;
            case 5:
                $result = '中外合作办学';
                break;
            default:
                $result = '未知';
                break;
        }

        return $result;
    }

    public function getOwnerTypeDesc($ownerType)
    {
        $result = '';
        switch ($ownerType) {
            case 1:
                $result = '部委属';
                break;
            case 2:
                $result = '省(直辖市)属';
                break;
            case 3:
                $result = '地区级的院校';
                break;
            case 4:
                $result = '市管(省级)院校';
                break;
            default:
                $result = '未知';
                break;
        }

        return $result;
    }

    public function getLevelTypeDesc($levelType)
    {
        $result = '';
        switch ($levelType) {
            case 1:
                $result = '985工程';
                break;
            case 2:
                $result = '211工程';
                break;
            case 3:
                $result = '重点';
                break;
            case 4:
                $result = '一般';
                break;
            default:
                $result = '未知';
                break;
        }

        return $result;
    }
}