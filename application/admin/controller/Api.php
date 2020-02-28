<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\admin\model\Admin;
use app\admin\model\wxUsers;
use app\admin\model\Room;
use app\common\controller\CommonController;
class Api extends CommonController
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

            return $this->returns($list);
        }
        //  详情页
        if(request()->param('info')){
            $rid = request()->param('rid');
            $info = Db::name('roominfo')->where('r_id',$rid)->findOrEmpty();
            if(!$info){
                return $this->json_return('param invalid',$info,'000-1');
            }
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
        $param = $request->param();
        // var_dump($param);

        // collected
        $users = new wxUsers;
        $uid = $param['uid'];
        // var_dump($uid);
        // die;
        if(empty($uid)){return $this->json_return('uid为空','','002004');}
        $param = $request->param();

        switch ($param['type']) {
            /*---------用户关注接口--------------------------*/
            case 'userinfo':
            $data = $param['userinfo'];
            // return json_encode($data);
            // 添加用户详情
            $result = $users->where('id','=',$uid)->update($data);
            if($result){
                return $this->json_return('success','','0000012');
            }else{
                return $this->json_return('noEdit','','0000013');
            }
                break;

            /*---------收藏关注接口--------------------------*/
            case 'collected':
            /*
            *  @ uid
            *  @ rid
            */
                $rid = $param['rid'];
                $this->check($rid);
                #先查询数据是否存在，如果存在则追加，没有就插入数
                $isnow = Db::name('collected')->where('uid',$uid)->find();
                if($isnow){
                    // 检查是否已存在（去重）
                    $colls = rtrim($isnow['collected'],',');
                    $colls = explode(',',$colls);
                    $isexist = in_array($rid,$colls);
                    if($isexist){//如果已经存在，返回已关注
                        return $this->json_return('已关注','','0000212');
                    }  
                    $sql = "UPDATE wxp_collected SET `collected`=CONCAT(collected,'$rid".','."') WHERE `uid`=$uid";
                    $res = Db::name('collected')->query($sql);
                    return $this->json_return('关注了',$rid,'000001');
                }else{
                    $rid.=",";
                    $res = Db::name('collected')->insert(['uid'=>$uid,'collected'=>$rid]);
                }
                if($res){
                    $this->json_return('添加成功');
                }

            break;
            default:
                return $this->json_return('other','','000101');
                break;
        }
       
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
        return $this->returns($datas);
        
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
            $id = $users->insertGetId([
                'onlyid'  => $onlyid,
                'sessionkey' =>$session_key,
                'login_datetime'=>date('Y-m-d H:i:s',time())
            ]);
            return $this->json_return('success',['uid'=>$id],'000001');
        }else{
            $id = $exist['id'];
            $result = $users->where('onlyid',$onlyid)->update([
                'sessionkey' =>$session_key,
                'login_datetime'=>date('Y-m-d H:i:s',time()),
            ]);
            return $this->json_return('success',['uid'=>$id],'000002');
        }
        
        if(!$result){//如果输入失败
            $wxdata=json_encode(request()->param());
            $wxdata .=json_encode($_SERVER['REMOTE_ADDR']);
            $log = fopen('./log/wxlogin.log','a');
            fwrite($log,$wxlogin);
            fclose($log);
        }
        
    }


    // /-------------描述de关键字搜索功能------------------/
    public function descsearch(){
        $param = request()->param();
        $gjz = $param['gjz'];
        $this->check($gjz);
        $datas =Room::field('r_id, r_desc,sell_price,unit_price,house_type,acreage,pics,oriented,dispark')->order('r_id', 'desc')->where('r_desc','like',"%{$gjz}%")->select();
        $datas = $this->fpic($datas);
        return $this->returns($datas);

    }

    //获取用户关注列表
    public function getcoll(){
        $params =  request()->param();
        //检测是否有参数为空
        // $this->isempty($params);
        $uid = @$params['uid'];
        if(isset($uid) && !empty($uid)){
            $collects = Db::name('collected')->where('uid',$uid)->field('collected')->find();
            $collects = $collects['collected'];
            //去掉最右边的逗号                                                        
            $collects = rtrim($collects,',');                                       
            //以逗号为符号分割字符串
            $colls = explode(',',$collects);
            $colls = (count($colls) == 1 ?  $colls[0] : $colls );
            $datas = Db::name('roominfo')->field('r_id, r_desc,sell_price,unit_price,house_type,acreage,pics,oriented,dispark')->where(['r_id'=>$colls])->select();
            #获取第一张图片
            $datas = $this->fpic($datas);
            return $this->returns($datas);
             /*
                      w为什么要弄这些处理呢，查询到的数据是有"数字+逗号" 组成的字符串，这些字符串可能是多个，可能是一个
                    1，如果是一个的情况，则用where条件即可，where('r_id',$rids)
                    2，如果是多个字符串，那么查询条件就应该用in  tp5.1 并没有特地写个in函数，而是直接在where里做了加工：
                    如果数组的值为数组类型，那么查询条件变为in,写法是where(['r_id'=>$rids]);
                      #rids 是一个索引数组 ，索引数组，索引数组
             */
        }else{
            // 参数非法
            return $this->json_return('参数非法','','err');
        }


    }


/*---------辅助函数---------------------------------------*/

    // 判断都是否不为空
    
    // protected function isempty($data){
    //     foreach ($data as $key => $value) {
    //         if(empty($value)){
    //             return $this->json_return($key.'为空','','err');
    //         }
    //     }
        
    // }

    //获取第一张图片
    protected function fpic($datas){
         foreach ($datas as $key => $item) {
           $datas[$key]['pics'] = explode(',',$item['pics'])[0];
        }
        return $datas;
    }
    
   
}
