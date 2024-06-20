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
        Schema::create('partial_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->text('stripe_token');
            $table->string('name_on_card');
            $table->string('card_number');
            $table->string('cvc');
            $table->integer('exp_month');
            $table->integer('exp_year');
            $table->integer('total_receipts');
            $table->integer('total_receipts_paid_for');
            $table->integer('remaing_receipts');
            $table->float('total_amount');
            $table->float('total_paid_amount');
            $table->float('paid_amount');
            $table->float('remaining_amount');
            $table->text('user_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partial_payment');
    }
};
