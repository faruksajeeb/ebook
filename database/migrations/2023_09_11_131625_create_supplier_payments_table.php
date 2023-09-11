<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned();
            $table->bigInteger('purchase_id')->unsigned();
            $table->date('payment_date');
            $table->decimal('payment_amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('paid_by')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('file')->comment('If any invoice want to attached');
            $table->dateTimeTz('created_at', $precision = 0);
            $table->dateTimeTz('updated_at', $precision = 0);
            $table->softDeletesTz('deleted_at', $precision = 0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
