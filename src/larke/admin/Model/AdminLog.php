<?php

namespace Larke\Admin\Model;

use Illuminate\Database\Eloquent\Model;

/*
 * 登陆日志
 *
 * @create 2020-10-19
 * @author deatil
 */
class AdminLog extends Model
{
    protected $table = 'larke_admin_log';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $timestamps = false;
    
    /**
     * 日志用户
     *
     * @create 2020-10-19
     * @author deatil
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }
    
    /**
     * 记录日志
     *
     * @create 2020-10-19
     * @author deatil
     */
    public static function record($data = [])
    {
        $data = array_merge([
            'id' => md5(mt_rand(100000, 999999).microtime()),
            'method' => app()->request->method(),
            'url' => urldecode(request()->getUri()),
            'ip' => request()->ip(),
            'useragent' => request()->server('HTTP_USER_AGENT'),
        ], $data);
        self::insert($data);
    }

}