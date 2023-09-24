<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory,SoftDeletes; 
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'shipping_amount',
        'shipping_address',
        'net_amount',
        'pay_amount',
        'due_amount',
        'courtesy_total_amount',
        'attach_file',
        'sale_note',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
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
            $q->where('id','LIKE',$term);
            $q->orWhere('sale_date', 'LIKE', $term);
            $q->orWhereHas('customer',function($q) use($term){
                $q->where('customer_name','LIKE',$term);
            });
        });
    }
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed()->withDefault(['value'=>'']);
    }

    function saleDetails()
    {
        // return $this->hasMany(SaleDetail::class);
        return $this->hasMany(SaleDetail::class,'sale_id','id')->orderBy('book_id');
    }

    function activeSaleDetails()
    {
        return $this->hasMany(SaleDetail::class)->where('status', 1);
    }

    public function latestSaleDetails(): HasOne
    {
        return $this->hasOne(SaleDetail::class)->latestOfMany();
    }

    public function oldestSaleDetails(): HasOne
    {
        return $this->hasOne(SaleDetail::class)->oldestOfMany();
    }

    public function largestSaleDetails(): HasOne
    {
        return $this->hasOne(SaleDetail::class)->ofMany('id', 'max');
    }

    public function currentSaleDetails(): HasOne
    {
        return $this->hasOne(SaleDetail::class)->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function (Builder $query) {
            $query->where('published_at', '<', now());
        });
    }
}
