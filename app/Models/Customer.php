<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_photo',
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
            $q->where('customer_name', 'LIKE', $term);
            $q->orWhere('customer_email', 'LIKE', $term);
            $q->orWhere('customer_phone', 'LIKE', $term);
            $q->orWhere('customer_address', 'LIKE', $term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }

}
