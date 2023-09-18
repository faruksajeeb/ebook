<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    use HasFactory;
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
    ];

    
    public function book() : BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed()->withDefault(['value'=>'']);
    }
    public function purchase() : BelongsTo
    {
        return $this->belongsTo(Purchase::class)->withTrashed()->withDefault(['value'=>'']);
    }
}
