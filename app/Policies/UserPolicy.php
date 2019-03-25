<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        //
//    }

    // 使用授权策略需要注意以下两点：
    //
    // 1. 我们并不需要检查 $currentUser 是不是 NULL。未登录用户，框架会自动为其 所有权限 返回 false；
    // 2. 调用时，默认情况下，我们 不需要 传递当前登录用户至该方法内，因为框架会自动加载当前登录用户；

    public function update(User $currentUser, User $user)
    {
//        update 方法接收两个参数，第一个参数默认为当前登录用户实例，第二个参数则为要进行授权的用户实例。
        return $currentUser->id === $user->id;
    }

    public function destroy(User $currentUser, User $user)
    {
        // 删除的用户不能是自己,删除用户的只能是管理员
        return $currentUser->is_admin && ($currentUser->id !== $user->id);
    }
}
