<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    // 注册
    public function create()
    {
        return view('users.create');
    }

    // 用户页面
    public function show(User $user)
    {
        // 将用户对象 $user 通过 compact 方法转化为一个关联数组，并作为第二个参数传递给 view 方法，将数据与视图进行绑定。
        // 能在视图中使用 user 变量来访问通过 view 方法传递给视图的用户数据。
        return view('users.show', compact('user'));
    }

    // 用户数据验证
    public function store(Request $request)
    {
        // validator 由 App\Http\Controllers\Controller 类中的 ValidatesRequests 进行定义
        // validate 方法接收两个参数，第一个参数为用户的输入数据，第二个参数为该输入数据的验证规则。
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',  // unique:users 表示针对 users 表作唯一性验证
            'password' => 'required|confirmed|min:6'    // 可以使用 confirmed 来进行密码匹配验证。
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // 当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法。
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        return redirect()->route('users.show', [$user]);
        // route() 方法会自动获取 Model 的主键，也就是数据表 users 的主键 id
        // 以上代码等同于：redirect()->route('users.show', [$user->id]);
    }
}
