<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model
{
    use HasFactory,SoftDeletes;
    public $fillable = [
        'purchase_id',
        'book_id',
        'quantity',
        'unit_price',
        'sub_total',
        'discount_percentage',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'net_sub_total',
        'flag',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];
    
    public function book() : BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed()->withDefault(['value'=>'']);
    }
    public function purchase() : BelongsTo
    {
        return $this->belongsTo(Purchase::class)->withTrashed()->withDefault(['value'=>'']);
    }
}
