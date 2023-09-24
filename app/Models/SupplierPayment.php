<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'supplier_payments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'supplier_id',
        'purchase_id',
        'payment_date',
        'payment_amount',
        'payment_method',
        'paid_by',
        'payment_description',
        'file',
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

    public function payment_method()
    {
       return $this->belongsTo(Option::class, 'id', 'payment_method')->withTrashed()->withDefault(['name' => '']);
    }
}
