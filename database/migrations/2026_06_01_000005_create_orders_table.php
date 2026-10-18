<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('table_id')->constrained('tables');
            $table->enum('status', [
                'menunggu_konfirmasi',
                'dikonfirmasi',
                'diproses',
                'siap',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_konfirmasi');
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->enum('payment_status', ['menunggu', 'dikonfirmasi', 'dibatalkan'])->default('menunggu');
            $table->boolean('customer_paid_claim')->default(false);
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users');
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->string('cancellation_reason', 200)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
