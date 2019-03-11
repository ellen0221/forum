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


}
