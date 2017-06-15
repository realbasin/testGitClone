<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * api模块控制器基类
 * api控制器均要继承此类
 */
class controller_apiBase extends Controller {

    /**
     * 失败响应返回
     * @param $errorMsg
     * @param $errorCode
     * @return string
     */
    protected function responseFail( $errorMsg , $errorCode )
    {
        if ( $errorMsg == '' )
        {
            $errorMsg = 'Unknowed Error!';
            $errorCode = '9999';
        }

        $err = [
            'errorMsg' => "[{$errorCode}]{$errorMsg}",
            'errorCode' => $errorCode,
            'errorType' => 3,
            'returnStatusCode' => 200
        ];

        return $this->_response( null , $err );
    }

    /**
     * 成功响应返回
     * @param $data
     * @return string
     */
    protected function responseSuccess( $data )
    {
        return $this->_response( $data );
    }

    private function _response( $data , array $error = [] )
    {

        if ( $error )
        {
            $statusCode = $error['returnStatusCode'];
            $code = $error['errorCode'];
            $msg = $error['errorMsg'];
            $type = $error['errorType'];
        } else
        {
            $statusCode = 200;
            $code = '0000';
            $type = '0';
            $msg = 'Success';
        }
        if(!headers_sent())
        {
            header( 'HTTP/1.1 ' . $statusCode . ' ' . $this->_getStatusCodeMessage( $statusCode ) );
            header( 'Content-type: application/json' );
        }

        $result = [
            'statusCode'   => $statusCode ,
            'responseBody' => [
                'responseInfo' => [
                    'reasons' => [
                        'code' => $code ,
                        'type' => $type ,
                        'msg'  => $msg
                    ]
                ] ,
                'data'         => $data
            ]
        ];

        return json_encode($result);
    }

    /**
     * Gets the message for a status code
     *
     * @param mixed $status
     * @access private
     * @return string
     */
    private function _getStatusCodeMessage( $status )
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = [
            100 => 'Continue' ,
            101 => 'Switching Protocols' ,
            200 => 'OK' ,
            201 => 'Created' ,
            202 => 'Accepted' ,
            203 => 'Non-Authoritative Information' ,
            204 => 'No Content' ,
            205 => 'Reset Content' ,
            206 => 'Partial Content' ,
            300 => 'Multiple Choices' ,
            301 => 'Moved Permanently' ,
            302 => 'Found' ,
            303 => 'See Other' ,
            304 => 'Not Modified' ,
            305 => 'Use Proxy' ,
            306 => '(Unused)' ,
            307 => 'Temporary Redirect' ,
            400 => 'Bad Request' ,
            401 => 'Unauthorized' ,
            402 => 'Payment Required' ,
            403 => 'Forbidden' ,
            404 => 'Not Found' ,
            405 => 'Method Not Allowed' ,
            406 => 'Not Acceptable' ,
            407 => 'Proxy Authentication Required' ,
            408 => 'Request Timeout' ,
            409 => 'Conflict' ,
            410 => 'Gone' ,
            411 => 'Length Required' ,
            412 => 'Precondition Failed' ,
            413 => 'Request Entity Too Large' ,
            414 => 'Request-URI Too Long' ,
            415 => 'Unsupported Media Type' ,
            416 => 'Requested Range Not Satisfiable' ,
            417 => 'Expectation Failed' ,
            500 => 'Internal Server Error' ,
            501 => 'Not Implemented' ,
            502 => 'Bad Gateway' ,
            503 => 'Service Unavailable' ,
            504 => 'Gateway Timeout' ,
            505 => 'HTTP Version Not Supported'
        ];

        return (isset($codes[$status])) ? $codes[$status] : '';
    }


}