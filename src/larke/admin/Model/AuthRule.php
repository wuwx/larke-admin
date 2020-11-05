<?php

namespace Larke\Admin\Model;

use Illuminate\Support\Facades\Cache;

/*
 * AuthRule
 *
 * @create 2020-10-20
 * @author deatil
 */
class AuthRule extends Base
{
    protected $table = 'larke_auth_rule';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    
    protected $guarded = [];
    
    public $incrementing = false;
    public $timestamps = false;
    
    /**
     * 规则的分组列表
     *
     * @create 2020-10-20
     * @author deatil
     */
    public function groups()
    {
        return $this->belongsToMany(AuthGroup::class, AuthRuleAccess::class, 'rule_id', 'group_id');
    }
    
    /**
     * 授权
     */
    public function ruleAccess()
    {
        return $this->hasOne(AuthRuleAccess::class, 'rule_id', 'id');
    }
    
    /**
     * 获取子模块
     */
    public function childrenModule()
    {
        return $this->hasMany($this, 'parentid', 'id');
    }
    
    /**
     * 递归获取子模块
     */
    public function children()
    {
        return $this->childrenModule()->with('children');
    }
    
    public function enable() 
    {
        return $this->update([
            'status' => 1,
        ]);
    }
    
    public function disable() 
    {
        return $this->update([
            'status' => 0,
        ]);
    }
    
    public static function getCacheStore()
    {
        $configStore = config('larke.cache.auth_rule.store');
        
        $cacheStore = Cache::store($configStore);
        
        return $cacheStore;
    }
    
    public static function getAuthRules()
    {
        $cacheStore = static::getCacheStore();
        
        $configKey = config('larke.cache.auth_rule.key');
        $rules = $cacheStore->get($configKey);
        if (!$rules) {
            $rules = self::all()->toArray();
            
            $configTtl = config('larke.cache.auth_rule.ttl');
            $cacheStore->put($configKey, $rules, $configTtl);
        }
        
        return $rules;
    }
    
}