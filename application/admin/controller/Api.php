<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\admin\model\Admin;
use app\admin\model\wxUsers;

class Api extends Controller
{
    private $appid='wx581d3e61cb49a511';
    private $secret='bdc820c9cd42b5330d4599b891ebc79c';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->param('index')){//如果是首页
            $dispark=['1'=>'随时看房','2'=>'电话访问'];
            $dispark=['1'=>'随时看房','2'=>'电话访问'];
            /* each(这里的函数是闭包函数，需要用return来返回数据) */
            $list = Db::name('roominfo')->field('r_id, r_desc,sell_price,unit_price,house_type,acreage,pics,oriented,dispark')->order('r_id', 'desc')->paginate(5)->each(function($item, $key){
                    // //数据处理
                    // $item['sell_price'] .= '万元';
                    // $item['unit_price'] .= '元';
                    // $item['acreage'] .= 'm^2';
                    $item['pics'] = explode(',',$item['pics'])[0];

                    foreach ($item as $key => $value) {//遍历出来存储
                        $data[$key] = $value;
                    }
                    return $data; //闭包函数，return回调数据
                });
            return $this->json_return('success',$list,'00000');

        }
        //  详情页
        if(request()->param('info')){
            $rid = request()->param('rid');
            $info = Db::name('roominfo')->where('r_id',$rid)->findOrEmpty();
            $picsx = explode(',',$info['pics']);
            $info["pics"] = $picsx;
            return $this->json_return('success',$info,'00000');
        }

      
        return $this->json_return('请输入指定参数','','00404');
        
     

    } 
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
    public function cates()
    {
        $list = Db::name('roominfo')->field('unit_price,house_type,location')->select();
        $areas = [];
        $prices = [];
        $tps = [];
        $data = [];
        /* 泪目 ( Ĭ ^ Ĭ )  下列是分组*/
        foreach ($list as $key => $value) {
            if(!in_array($value['unit_price'],$prices)){
                $prices[] = $value['unit_price'];
            }
            if(!in_array($value['house_type'],$tps)){
                $tps[] = $value['house_type'];
            }
            if(!in_array($value['location'],$areas)){
                $areas[] = $value['location'];
            }
        }
        $data[] = $prices;
        $data[] = $tps;
        $data[] = $areas;

        return $this->json_return('success',$data,'00001');

    }

    //搜索接口
    public function searchs(){
        $param =request()->param(); 
        $params = [];
        foreach ($param as $key => $value) {
            //防注入
            $this->check($value);
            if($value == 'area'){
                $params[$key] = 'location';
            }elseif($value== 'price'){
                $params[$key] = 'sell_price';
            }elseif($value== 'housetp'){
                $params[$key] = 'house_type';
            }else{
                $params[$key] = $value;
            }
            // echo $key.":".$value;
        }
        //获取数据--------------------------------
        $datas = Db::name('roominfo')->field('r_id, r_desc,sell_price,unit_price,house_type,acreage,pics,oriented,dispark')->order('r_id', 'desc')->where($params['type'],'like',"%{$params['index']}%")->select();
          //处理了图片，只要第一张
        foreach ($datas as $key => $item) {
           $datas[$key]['pics'] = explode(',',$item['pics'])[0];
        }

        if(!empty($datas)){
            return $this->json_return('success',$datas,'00001');
        }else{
            return $this->json_return('无数据','','00004');            //查不到数据  0004
        }
    }


    /*
        用户登录接口 opendId 
    ------------------------------------------
          ------------  param
       @   code    微信调用wxlogin的时候返回的code值；有效期5分钟
          ------------  return
       @   openid       用户唯一标识
       @   session_key  会话密钥
       @   unionid      用户在开放平台的唯一标识符。本字段在满足一定条件的情况下才返回。具体参看UnionID机制说明
    */

    public function opendId(){//获取用户ID

        $code=$_GET["code"];
        $appid=$this->appid;
        $secret=$this->secret;
        //检测注入
        $this->check($code);
        if(empty($code)){
            return '参数为空';
        }



        $c= $this->getCurl("https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code");

        // return $c;//对JSON格式的字符串进行编码
        $us = json_decode($c,1);

        //检测参数是否过期
        if(@$us['errcode']==40163)return $this->json_return('code参数过期','','040163');
        $onlyid = $us['openid'];
        $session_key = $us['session_key'];


        // 将登录状态保存到后台数据库
        $exist = wxUsers::where('onlyid',$onlyid)->find();
        //如果用户存在则修改sessionid；不存在则添加
        $users = new wxUsers;
        if(!$exist){
            $result = $users->save([
                'onlyid'  => $onlyid,
                'sessionkey' =>$session_key,
                'login_datetime'=>date('Y-m-d H:i:s',time())
            ]);
        }else{
            $result = $users->where('onlyid',$onlyid)->update([
                'sessionkey' =>$session_key,
                'login_datetime'=>date('Y-m-d H:i:s',time()),
            ]);
        }
        
        if(!$result){//如果输入失败
            $wxdata=json_encode(request()->param());
            $wxdata .=json_encode($_SERVER['REMOTE_ADDR']);
            $log = fopen('./log/wxlogin.log','a');
            fwrite($log,$wxlogin);
            fclose($log);
        }
        
    }



/*---------辅助函数---------------------------------------*/
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
    protected function check($sql_str){  
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
     function getCurl($url, $post = 0, $cookie = 0, $header = 0, $nobaody = 0)
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
}
