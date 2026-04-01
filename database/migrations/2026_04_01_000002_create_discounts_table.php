<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            // Human-readable label for internal reference
            $table->string('name');

            // The unique code tenants/admins enter (e.g. TESDA2025)
            $table->string('code')->unique();

            // Discount value
            $table->enum('type', ['percentage', 'fixed']);   // percentage off or fixed ₱ off
            $table->decimal('value', 10, 2);                 // e.g. 20 (%) or 500.00 (₱)

            // Which plans this code is valid for — null means all plans
            $table->json('applicable_plans')->nullable();    // ["basic","standard","premium"]

            // Which actions trigger this discount — null means all
            $table->json('applicable_actions')->nullable();  // ["approve","upgrade_superadmin","upgrade_admin","renewal"]

            // Specific tenant restriction — null means any tenant can use it
            $table->string('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Validity window
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            // Usage limits
            $table->unsignedInteger('max_uses')->nullable();   // null = unlimited
            $table->unsignedInteger('uses_count')->default(0); // how many times used so far

            // Minimum plan price to qualify (optional gate)
            $table->decimal('minimum_price', 10, 2)->nullable();

            $table->boolean('is_active')->default(true);

            // Who created this discount
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });

        // Track which tenant used which discount and when
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->string('action');                         // approve | upgrade_superadmin | upgrade_admin | renewal
            $table->string('plan_slug');                      // the plan it was applied to
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->unsignedBigInteger('applied_by')->nullable(); // superadmin user id
            $table->foreign('applied_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['discount_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
        Schema::dropIfExists('discounts');
    }
};
