<?php

namespace app\admin\controller;

use think\Controller;
use think\Cookie;
use think\Request;
use think\Session;

class Base extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if (!Session::has(SESSION_NAME)) {
            $this->redirect('admin/login/login');
        }
    }
}
