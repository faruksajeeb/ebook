<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'supplier_name',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'supplier_photo',
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
            $q->where('supplier_name', 'LIKE', $term);
            $q->orWhere('supplier_email', 'LIKE', $term);
            $q->orWhere('supplier_phone', 'LIKE', $term);
            $q->orWhere('supplier_address', 'LIKE', $term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }
}
