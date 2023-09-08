<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;
    public $guard_name = 'api';
    protected $table = 'sub_categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_id',
        'sub_category_name',
        'created_by',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];


    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed()->withDefault(['value'=>'']);
    }

    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            // $q->where('id','LIKE',$term);
            $q->where('sub_category_name','LIKE',$term);
            // $q->orWhere('description','LIKE',$term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }



}
