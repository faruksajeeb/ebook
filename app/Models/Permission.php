<?php
namespace App\Models;
use Spatie\Permission\Models\Permission as OriginalPermission;
class Permission extends OriginalPermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'group_name',
        'updated_at',
        'created_at',
    ];

    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            // $q->where('id','LIKE',$term);
            $q->where('name','LIKE',$term);
            $q->orWhere('group_name','LIKE',$term);
            // $q->orWhere('price','LIKE',$term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
            // $q->orWhereHas('sub_categories',function($q) use($term){
            //     $q->where('sub_category_name','LIKE',$term);
            // });
        });
    }
}