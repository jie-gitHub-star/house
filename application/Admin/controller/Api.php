<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\Admin\model\Admin;

class Api extends Controller
{
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
            return $this->json_return('success',$list,'0000');

        }
        //  详情页
        if(request()->param('info')){
            $rid = request()->param('rid');
            $info = Db::name('roominfo')->where('r_id',$rid)->findOrEmpty();
            $picsx = explode(',',$info['pics']);
            $info["pics"] = $picsx;
            return $this->json_return('success',$info,'0000');
        }

      
        return $this->json_return('请输入指定参数','','0404');
        
     

    }
    public function json_return($message = '',$data = '',$code = 0)
    {
        $return['msg']  = $message;
        $return['data'] = $data;
        $return['code'] = $code;
        return json_encode($return);
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
}
