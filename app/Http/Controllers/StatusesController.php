<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusesController extends Controller
{
    // 过滤请求
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 创建微故事
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        session()->flash('success', '发布成功！');
        return redirect()->back();
    }

    // 删除 『隐性路由模型绑定』功能，Laravel 会自动查找并注入对应 ID 的实例对象 $status，如果找不到就会抛出异常。
    public function destroy(Status $status)
    {
        // 删除授权监测
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '删除成功！');
        return redirect()->back();
    }
}
