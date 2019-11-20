<?php

namespace app\Admin\controller;

use think\Controller;
use think\Request;
// 引入模型类
use app\Admin\model\Admin;
use think\facade\Session;
use think\facade\Cookie;

class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Session::has('uid')){
            return $this->error('已登录');
       }
        
        //
        return $this->fetch();

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
    public function save(Request $request) //登录操作
    {
        //
       $user_name  = $request->usr_name;
       $user_pwd  = $request->usr_pwd;
        


       if(empty($user_name) || empty($user_pwd)){
            return $this->error('用户名或密码不能为空');
       }
       
       $result = Admin::where('name',$user_name)->findOrEmpty();
       if(empty($result)){
           return $this->error('账户或密码错误');
       }

       /**
        * password_verify 解密
        @  要解密的字符串
        @  加密后的字符串
        return  匹配一样返回true 不一样返回false

       */
        $_login = password_verify($user_pwd,$result->passwd);
        if(!$_login){
             return $this->error('账户或密码错误');
        }
        $Admin = new Admin;
        $Admin->save(['login_time'=>date('Y-m-d H:i:s',time())],['uid'=>$result->uid]);

        Session::set('uid',$result->uid);
        Cookie::set('user_info',$result,3600);
        return redirect('/admin');         
       




       /*forget pwd*/ 
       if(!empty($request->email)){ //忘记密码

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
}
