<?php

namespace app\admin\controller;

use think\Controller;
use think\Cookie;
use think\Request;
use think\Session;

class Login extends Controller
{
    //
    public function login()
    {
        //临时关闭全局模板布局
        $this->view->engine->layout(false);
        return view();
    }

    public function doLogin(Request $request)
    {
        $params = $request->param();
        $rule = [
            'username|用户名' => 'require',
            'password|密码' => 'require|length:6,16',
            'code|验证码' => 'require|captcha'
        ];
        $msg = [
            'password.length' => '密码长度必须在6到16个字符之间'
        ];
        $validate = $this->validate($params, $rule, $msg);
        if ($validate !== true) {
            $this->error($validate);
        }
        $password = encrypt_password($params['password']);
        $user = \app\admin\model\Manager::where('username', $params['username'])->where('password', $password)->find();
        if (!empty($user)) {
            Session::set(SESSION_NAME, $user->toArray());
            Cookie::set(session_name(),session_id(),3600);
            $this->success('登录成功','admin/index/index');
        }
    }

    /**
     * 退出
     * */
    public function logout()
    {
        Cookie::delete(session_name());
        Session::clear();
        $this->redirect('admin/login/login');
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        $manager = Session::get(SESSION_NAME);
    }

}
