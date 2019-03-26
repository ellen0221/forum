<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    // 用户身份验证
    public function __construct()
    {
        // 对除了基础显示操作外的操作进行用户身份验证
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        // 只让未登录的用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);

    }

    // 用户列表--公共权限，允许游客访问
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

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

    // 用户注册数据验证
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

//        Auth::login($user); // 注册成功后自动登录
        // 当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法。
//        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//
//        return redirect()->route('users.show', [$user]);
        // route() 方法会自动获取 Model 的主键，也就是数据表 users 的主键 id
        // 以上代码等同于：redirect()->route('users.show', [$user->id]);
    }

    // 发送验证信息邮件
    public function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
//        $from = 'jat1014@163.com';
//        $name = 'xj';
        $to = $user->email;
        $subject = "感谢注册 WStory 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 邮箱激活功能
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    // 编辑信息
    public function edit(User $user)
    {
        // 权限控制
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    // 更新信息
    public function update(User $user, Request $request)
    {
        // 权限控制
        $this->authorize('update', $user);

        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '个人资料修改成功！');

        return redirect()->route('users.show', $user->id);
    }

    // 删除用户
    public function destroy(User $user)
    {
        // 权限控制
        $this->authorize('destroy', $user); // 对应UserPolicy中的destroy方法
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
