<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory,SoftDeletes; 
    protected $table = 'sale_returns';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'sale_return_date',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'shipping_amount',
        'net_amount',
        'attach_file',
        'sale_return_note',
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
            $q->orWhere('sale_return_date', 'LIKE', $term);
            $q->orWhereHas('customer',function($q) use($term){
                $q->where('customer_name','LIKE',$term);
            });
        });
    }
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed()->withDefault(['value'=>'']);
    }

    function saleReturnDetails()
    {
        // return $this->hasMany(SaleReturnetail::class);
        return $this->hasMany(SaleReturnetail::class,'sale_id','id')->orderBy('book_id');
    }

    function activeSaleReturnDetails()
    {
        return $this->hasMany(SaleReturnetail::class)->where('status', 1);
    }

    public function latestSaleReturnDetails(): HasOne
    {
        return $this->hasOne(SaleReturnetail::class)->latestOfMany();
    }

    public function oldestSaleReturnDetails(): HasOne
    {
        return $this->hasOne(SaleReturnetail::class)->oldestOfMany();
    }

    public function largestSaleReturnDetails(): HasOne
    {
        return $this->hasOne(SaleReturnetail::class)->ofMany('id', 'max');
    }

    public function currentSaleReturnDetails(): HasOne
    {
        return $this->hasOne(SaleReturnetail::class)->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function (Builder $query) {
            $query->where('published_at', '<', now());
        });
    }
}
