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
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('order_type', ['pickup', 'delivery', 'pos'])->default('pickup');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('final_amount', 14, 2)->default(0);
            $table->enum('status', [
                'pending_payment',
                'confirmed',
                'processing',
                'ready_for_pickup',
                'on_delivery',
                'completed',
                'cancelled'
            ])->default('pending_payment');
            $table->enum('payment_status', [
                'pending',
                'waiting_verification',
                'paid',
                'rejected',
                'failed',
                'refunded'
            ])->default('pending');
            $table->enum('payment_method', ['qris', 'cash'])->default('qris');
            $table->text('notes')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

