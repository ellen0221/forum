<?php

namespace App\Models;

use function foo\func;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

//        creating 用于监听模型被创建之前的事件，created 用于监听模型被创建之后的事件。
//        接下来我们要生成的用户激活令牌需要在用户模型创建之前生成，因此需要监听的是 creating 方法。
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 生成用户头像
    public function gravatar($size = '100')
    {
        // Gravatar 为 “全球通用头像”，当你在 Gravatar 的服务器上放置了自己的头像后，
        // 可通过将自己的 Gravatar 登录邮箱进行 MD5 转码，并与 Gravatar 的 URL 进行拼接来获取到自己的 Gravatar 头像。
        // $this->attributes['email'] 获取到用户的邮箱
        // trim 方法剔除邮箱的前后空白内容
        // 将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    // 与微故事模型一对多关联
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    // 获取用户发布的微故事
    public function feed()
    {
        return $this->statuses()->orderBy('created_at', 'desc');
    }

    // 与粉丝多对多
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 与关注对象多对多
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    // 关注
    public function follow($user_ids)
    {
        if (! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        // sync 和 detach 会自动获取数组中的 id
        $this->followings()->sync($user_ids, false);
    }

    // 取消关注
    public function unfollow($user_ids)
    {
        if (! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        // sync 和 detach 会自动获取数组中的 id
        $this->followings()->detach($user_ids);
    }

    // 判断是否已经关注该对象
    public function isFollowing($user_id)
    {
        return $this->followings()->contains($user_id);
    }

}
