<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'file',
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
            // $q->where('id','LIKE',$term);
            $q->where('title', 'LIKE', $term);
            $q->orWhere('isbn', 'LIKE', $term);
            $q->orWhere('genre', 'LIKE', $term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
        });
    }
    public function publisher() : BelongsTo
    {
        return $this->belongsTo(Publisher::class)->withTrashed()->withDefault(['value'=>'']);
    }
    public function author() : BelongsTo
    {
        return $this->belongsTo(Author::class)->withTrashed()->withDefault(['value'=>'']);
    }
    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed()->withDefault(['value'=>'']);
    }
    public function sub_category() : BelongsTo
    {
        return $this->belongsTo(SubCategory::class)->withTrashed()->withDefault(['value'=>'']);
    }
}
