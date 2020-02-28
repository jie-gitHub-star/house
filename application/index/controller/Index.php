<?php
namespace app\index\controller;
use think\Controller;
use xunsms\lib\Ucpaas;
use PHPMailer\PHPMailer\src\PHPMailer;
use think\Db;
class Index extends Controller
{
    public function index()
    {
        $options['accountsid']='1277db5c7f9952d8dec6518097f0a632';
//填写在开发者控制台首页上的Auth Token
$options['token']='cd1d54c9ca1ccf6911c12d19ef853f1c';
$appid = "423d3a481041486db58ebd3702fca3b7";	//应用的ID，可在开发者控制台内的短信产品下查看
$templateid = "501131";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
// $templateid = "501214";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
$uid =0;
// $mobile=18145758446;
// $param = 12345;
//初始化 $options必填
$ucpass = new Ucpaas($options);
        $ucpass->hello();

    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
    public function mail()
    {
      	
    	// $res = vendor('phpmailer\phpmailer\src\PHPMailer');
    	// var_dump($res);
      	$mail = new \PHPMailer();
    }


    //
    public function test(){
    	$data = json_encode(['success','name'=>'kervi','age'=>'18']);

    	echo "callback(".$data.")";
    	// echo $data;



    	// ------------
//     	header('Content-type: application/json');
// //获取回调函数名
// $jsoncallback = htmlspecialchars($_REQUEST ['jsoncallback']);
// //json数据
// $json_data = '["customername1","customername2","customername3"]';
// //输出jsonp格式的数据
// echo $jsoncallback . "(" . $json_data . ")";


    }

}
