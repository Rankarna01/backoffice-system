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
        // 1. Membership / Subscription Plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('duration'); // duration, lifetime
            $table->unsignedInteger('duration_days')->nullable(); // e.g. 30, 90, 365, null for lifetime
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('compare_at_price', 15, 2)->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->json('features')->nullable();
            $table->boolean('includes_all_courses')->default(false);
            $table->boolean('includes_signals')->default(true);
            $table->unsignedInteger('ai_monthly_quota')->default(50);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });

        // 2. Specific Course Inclusions for Plans
        Schema::create('plan_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unique(['plan_id', 'course_id']);
        });

        // 3. User Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('status')->default('active'); // pending, active, grace, cancelled, ended, refunded
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('grace_ends_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->unsignedBigInteger('latest_order_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('ends_at');
        });

        // 4. Coupons & Vouchers
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->string('type')->default('percent'); // percent, fixed
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->decimal('min_order', 15, 2)->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('max_redemptions')->default(0); // 0 = unlimited
            $table->unsignedInteger('redemptions_count')->default(0);
            $table->unsignedInteger('max_per_user')->default(1);
            $table->string('applies_to')->default('all'); // all, courses, plans
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'code']);
        });

        // 5. Affiliates
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('status')->default('approved'); // pending, approved, suspended
            $table->decimal('commission_rate', 5, 2)->default(20.00); // 20%
            $table->json('payout_details')->nullable(); // bank_name, account_number, account_holder
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'code']);
        });

        // 6. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, paid, failed, expired, refunded, partially_refunded
            $table->string('currency', 10)->default('IDR');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->foreignId('affiliate_id')->nullable()->constrained('affiliates')->nullOnDelete();
            $table->string('referral_code')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // 7. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('purchasable_type'); // course, plan
            $table->unsignedBigInteger('purchasable_id');
            $table->string('name');
            $table->decimal('unit_price', 15, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['purchasable_type', 'purchasable_id']);
        });

        // 8. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway')->default('midtrans'); // midtrans, xendit, manual
            $table->string('gateway_ref')->nullable();
            $table->string('method')->nullable(); // qris, bank_transfer, gopay, credit_card, manual_transfer
            $table->string('status')->default('pending'); // pending, paid, failed, expired, refunded
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->string('payment_url', 500)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['gateway', 'gateway_ref']);
        });

        // 9. Coupon Redemptions
        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('discount_amount', 15, 2);
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['coupon_id', 'order_id']);
            $table->index(['coupon_id', 'user_id']);
        });

        // 10. Commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('rate', 5, 2);
            $table->string('status')->default('pending'); // pending, approved, paid, void
            $table->dateTime('available_at')->nullable();
            $table->unsignedBigInteger('payout_id')->nullable();
            $table->timestamps();

            $table->unique(['order_id']);
            $table->index(['affiliate_id', 'status']);
        });

        // 11. Payouts
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('method')->default('bank_transfer');
            $table->string('reference')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->dateTime('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });

        // 12. Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->string('status')->default('requested'); // requested, approved, rejected, processed
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->string('gateway_ref')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plan_course');
        Schema::dropIfExists('plans');
    }
};
