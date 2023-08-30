<?php
namespace App\Models;
use Spatie\Permission\Models\Role as OriginalRole;
class Role extends OriginalRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
    ];

    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            // $q->where('id','LIKE',$term);
            $q->where('name','LIKE',$term);
            // $q->orWhere('description','LIKE',$term);
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