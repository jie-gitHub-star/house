<?php

namespace app\http\middleware;
use think\Controller;
use think\facade\Session;
class Check
{
    public function handle($request, \Closure $next)
    {
    			//session_unset();
		//验证登录

		if(!Session::has('uid')){
			//$this->error('请先登录！','index.php/index/user/login',1,1);
			// $this->redirect('login/login');
			return redirect('/login');
			
		}
		

		// $this->uid = $_SESSION['uid'];
		

		// if(!$this->user){
		// 	unset($_SESSION['uid']);
		// 	$this->redirect('login/login?token='.$this->token);
		// }
		return $next($request);
    }
}
