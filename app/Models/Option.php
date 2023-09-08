<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory,SoftDeletes;
    public $guard_name = 'api';
    protected $table = 'options';
    protected $primaryKey = 'id';
    protected $fillable = [
        'option_group_id',
        'name',
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


    public function option_group() : BelongsTo
    {
        return $this->belongsTo(OptionGroup::class)->withTrashed()->withDefault(['value'=>'']);
    }

    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            // $q->where('id','LIKE',$term);
            $q->where('name','LIKE',$term);
            // $q->orWhere('description','LIKE',$term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }



}
