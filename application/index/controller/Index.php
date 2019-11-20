<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return "前台首页";
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
