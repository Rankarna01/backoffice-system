<?php

namespace Tests\Feature;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Domain\Billing\Enums\CouponType;
use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Affiliate;
use App\Domain\Billing\Models\Coupon;
use App\Domain\Billing\Models\Order;
use App\Domain\Billing\Models\Payment;
use App\Domain\Billing\Models\Subscription;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonetizationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_subscriptions_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/subscriptions');
        $response->assertSuccessful();
        $response->assertSee('Subscription');
        $response->assertSee('Langganan Aktif');
    }

    public function test_admin_can_access_orders_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/orders');
        $response->assertSuccessful();
        $response->assertSee('Orders');
        $response->assertSee('Total Penjualan Lunas');
    }

    public function test_admin_can_access_payments_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/payments');
        $response->assertSuccessful();
        $response->assertSee('Payments');
        $response->assertSee('Total Dana Diterima');
    }

    public function test_admin_can_access_coupons_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/coupons');
        $response->assertSuccessful();
        $response->assertSee('Coupons');
        $response->assertSee('TRADINGPRO20');
        $response->assertSee('Kupon Aktif');
    }

    public function test_admin_can_access_affiliates_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/affiliates');
        $response->assertSuccessful();
        $response->assertSee('Affiliate');
        $response->assertSee('ALEX10');
        $response->assertSee('Mitra Afiliasi Aktif');
    }

    public function test_subscription_active_scope_and_extension(): void
    {
        $subscription = Subscription::where('status', SubscriptionStatus::Active)->whereNotNull('ends_at')->first();
        $this->assertNotNull($subscription);

        $oldEndsAt = $subscription->ends_at;
        $subscription->update(['ends_at' => $oldEndsAt->copy()->addDays(30)]);

        $this->assertTrue($subscription->fresh()->ends_at->gt($oldEndsAt));
        $this->assertTrue($subscription->fresh()->isActive());
    }

    public function test_order_manual_mark_as_paid(): void
    {
        $order = Order::where('status', OrderStatus::Pending)->first();
        $this->assertNotNull($order);

        $order->update([
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ]);

        $this->assertTrue($order->fresh()->isPaid());
        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_coupon_discount_calculation(): void
    {
        $percentCoupon = Coupon::where('code', 'TRADINGPRO20')->first();
        $this->assertNotNull($percentCoupon);
        $this->assertTrue($percentCoupon->isValid(500000));

        // 20% of 1.000.000 = 200.000 (under 500.000 max)
        $this->assertEquals(200000, $percentCoupon->calculateDiscount(1000000));

        // 20% of 4.000.000 = 800.000, capped at max_discount 500.000
        $this->assertEquals(500000, $percentCoupon->calculateDiscount(4000000));

        $fixedCoupon = Coupon::where('code', 'HEMAT100RB')->first();
        $this->assertEquals(100000, $fixedCoupon->calculateDiscount(500000));
    }

    public function test_affiliate_unpaid_balance_and_payout(): void
    {
        $affiliate = Affiliate::where('code', 'ALEX10')->first();
        $this->assertNotNull($affiliate);

        $this->assertEquals(1599600, (float) $affiliate->total_earnings);
        $this->assertEquals(1000000, (float) $affiliate->total_paid);
        $this->assertEquals(599600, $affiliate->unpaidBalance());
    }
}
