<?php
namespace app\Admin\controller;

use think\View;
use think\Controller;
use app\Admin\model\Admin;
use think\Request;
use think\facade\Cookie;
/**
 *
 */
class Index extends Controller
{
    public function index()
    {

        return $this->fetch();
    }
}
