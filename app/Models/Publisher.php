<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
   
    protected $table = 'publishers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'publisher_name',
        'publisher_email',
        'publisher_phone',
        'publisher_address',
        'publisher_photo',
        'publisher_country',
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

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        $query->where(function ($q) use ($term) {
            // $q->where('id','LIKE',$term);
            $q->where('publisher_name', 'LIKE', $term);
            $q->orWhere('publisher_email', 'LIKE', $term);
            $q->orWhere('publisher_phone', 'LIKE', $term);
            $q->orWhere('publisher_address', 'LIKE', $term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }
}
