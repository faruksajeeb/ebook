<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as OriginalRole;
use Illuminate\Database\Eloquent\SoftDeletes;
class Role extends OriginalRole
{
    use HasFactory,SoftDeletes;
    public $guard_name = 'api';
    protected $fillable = [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

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