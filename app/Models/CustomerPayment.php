<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPayment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'customer_payments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'sale_id',
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
            $q->where('customer_name', 'LIKE', $term);
            $q->orWhere('customer_email', 'LIKE', $term);
            $q->orWhere('customer_phone', 'LIKE', $term);
            $q->orWhere('customer_address', 'LIKE', $term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentmethod()
    {
    //    return $this->belongsTo(Option::class,  'payment_method','id')->withTrashed()->withDefault(['name' => '']);
       return $this->hasOne(Option::class, 'id', 'payment_method')->withTrashed()->withDefault(['name' => '']);
    }

}
