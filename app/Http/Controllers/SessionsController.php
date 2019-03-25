<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    public function __construct()
    {
        // 只让未登录的用户访问登录页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // 登录
    public function create()
    {
        return view('sessions.create');
    }

    // 验证登录
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:225',
            'password' => 'required'
        ]);

//        dd(Auth::attempt($credentials, $request->has('remember')));
//        dd(Auth::check());

        // Auth::attempt() 方法可接收两个参数，第一个参数为需要进行用户身份认证的数组，第二个参数为是否为用户开启『记住我』功能的布尔值。
        if (Auth::attempt($credentials, $request->has('remember'))) {
            if (Auth::user()->activated) {
                // 登录成功
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            } else {
                Auth::logout();
                session()->flash('warning', '您的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }

        } else {
            // 登录失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput(); // 使用 withInput() 后模板里 old('email') 将能获取到上一次用户提交的内容，这样用户就无需再次输入邮箱等内容：
        }
    }

    // 退出登录
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
