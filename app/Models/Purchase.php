<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory,SoftDeletes; 
    protected $table = 'purchases';
    protected $primaryKey = 'id';
    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'net_amount',
        'pay_amount',
        'due_amount',
        'paid_by',
        'attach_file',
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
            $q->orWhere('purchase_date', 'LIKE', $term);
            $q->orWhereHas('supplier',function($q) use($term){
                $q->where('supplier_name','LIKE',$term);
            });
        });
    }
    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed()->withDefault(['value'=>'']);
    }

    function purchaseDetails()
    {
        // return $this->hasMany(PurchaseDetail::class);
        return $this->hasMany(PurchaseDetail::class,'purchase_id','id')->orderBy('book_id');
    }

    function activePurchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class)->where('status', 1);
    }

    public function latestPurchaseDetails(): HasOne
    {
        return $this->hasOne(PurchaseDetail::class)->latestOfMany();
    }

    public function oldestPurchaseDetails(): HasOne
    {
        return $this->hasOne(PurchaseDetail::class)->oldestOfMany();
    }

    public function largestPurchaseDetails(): HasOne
    {
        return $this->hasOne(PurchaseDetail::class)->ofMany('id', 'max');
    }

    public function currentPurchaseDetails(): HasOne
    {
        return $this->hasOne(PurchaseDetail::class)->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function (Builder $query) {
            $query->where('published_at', '<', now());
        });
    }
}
