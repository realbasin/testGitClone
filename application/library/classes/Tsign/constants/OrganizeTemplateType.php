<?php
/**
 * User: wanglf
 * Date: 2016/12/14
 */

namespace Tsign\constants;


class OrganizeTemplateType
{
    const STAR = 'star';
    const OVAL = 'oval';

    /**
     * 企业模板
     * star-标准公章，oval-椭圆形印章
     * @return array
     */
    public static function getArray()
    {
        return array(self::STAR, self::OVAL);
    }
}