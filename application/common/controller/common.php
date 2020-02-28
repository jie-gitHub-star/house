<?php
namespace app\common\controller;

use think\Controller;

class CommonController extends Controller
{
	 //有数据返回成功，没有返回失败
    public function returns($data){
        if(!empty($data)){
            return $this->json_return('success',$data,'000001');
        }else{
            return $this->json_return('fail','','0004040');
        }
    }
    /*
    * 放回json数据
    *
    */
    public function json_return($message = '',$data = '',$code = 0)
    {
        $return['msg']  = $message;
        $return['data'] = $data;
        $return['code'] = $code;
        return json_encode($return);
    }
    // 检测sql注入
    public function check($sql_str){  
        $check=preg_match("/select|inert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|UNION|into|load_file|outfile/", $sql_str);   
        if($check){  
            return $this->json_return("<meta charset='utf8'><title>非法</title><b style='color:red'>请勿尝试SQL注入,IP[".$_SERVER['REMOTE_ADDR']."]已记录！</b>",'','11111');  //sql注入
        }else{  
            return strip_tags($sql_str);  
        }  
    } 

    /*
            curl函数
    */
    public function getCurl($url, $post = 0, $cookie = 0, $header = 0, $nobaody = 0)
    {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $klsf[] = 'Accept:*/*';
            $klsf[] = 'Accept-Language:zh-cn';
            //$klsf[] = 'Content-Type:application/json';
            $klsf[] = 'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 11_2_1 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C153 MicroMessenger/6.6.1 NetType/WIFI Language/zh_CN';
            $klsf[] = 'Referer:'.$url;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $klsf);
            if ($post) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            if ($header) {
                curl_setopt($ch, CURLOPT_HEADER, true);
            }
            if ($cookie) {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            }
            if ($nobaody) {
                curl_setopt($ch, CURLOPT_NOBODY, 1);
            }
            curl_setopt($ch, CURLOPT_TIMEOUT,60);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $ret = curl_exec($ch);
            curl_close($ch);
            return $ret;
    }
        // 日志写入方法
    public function wlog($data='',$file='./log/wxlogin.log'){
        $log = fopen($file,'a');
        fwrite($log,$data);
        fclose($log);
    }
}
