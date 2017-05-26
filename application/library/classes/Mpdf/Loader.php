<?php
/**
 * 根据协议内容生成PDF文件
 * Created by PhpStorm.
 * User: maixh
 * Date: 2017/5/25
 * Time: 11:41
 */
namespace Mpdf;
defined("IN_XIAOSHU") or exit("Access Invalid!");

class Loader {
    protected $mpdf;

    public function __construct()
    {
        $this->mpdf = new mPDF("zh-CN");
        $this->mpdf->mirrorMargins = true;
        $this->mpdf->useAdobeCJK = true;
        $this->mpdf->autoLangToFont = true;
    }

    /**
     * 创建协议PDF文件
     * @param string $title 文件标题
     * @param string $content  文件内容
     * @param string $file_path   PDF文件保存的完整路径
     * @param int $load_user_count 投资用户超过6人，添加一页空白页
     * @param bool $update 是否更新旧文件
     */
    public function contractPdf($title, $content, $file_path, $load_user_count=6, $update=false) {
        if (!file_exists($file_path) || $update) {

            $this->mpdf->SetTitle($title);

            $content = str_replace("width:70%", "width:100%", $content);

            $this->mpdf->WriteHTML($content);

            if ($load_user_count > 6) {
                $this->mpdf->AddPage();
            }

            $this->mpdf->Output($file_path);
        }
    }
}