<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('category');
            $table->string('image')->nullable();
            $table->boolean('availability_status')->default(true);
            $table->timestamps();
        });

        // Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('order_type', ['Immediate', 'Scheduled']);
            $table->dateTime('scheduled_datetime')->nullable();
            $table->enum('status', ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Completed', 'Cancelled'])->default('Pending');
            $table->enum('payment_method', ['Cash', 'GCash']);
            $table->enum('payment_status', ['Unpaid', 'Pending Verification', 'Paid', 'Failed'])->default('Unpaid');
            $table->timestamps();
        });

        // Order Details
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status');
            $table->string('reference_number')->nullable();
            $table->string('proof_image')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
    }
};
