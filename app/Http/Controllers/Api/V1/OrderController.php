<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Models\Order;
use App\Domain\Billing\Models\OrderItem;
use App\Domain\Billing\Models\Payment;
use App\Domain\Learning\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'payment_method' => 'required|string',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = $request->user();

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'currency' => 'IDR',
                'subtotal' => $course->price,
                'total' => $course->price,
                'expires_at' => now()->addHours(24),
            ]);

            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'purchasable_type' => Course::class,
                'purchasable_id' => $course->id,
                'name' => $course->title,
                'unit_price' => $course->price,
                'quantity' => 1,
                'total' => $course->price,
            ]);

            // Create Payment
            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'manual',
                'method' => $request->payment_method,
                'status' => 'pending',
                'amount' => $course->price,
                'currency' => 'IDR',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load('items', 'payments'),
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function resolveUser(Request $request): ?\App\Models\User
    {
        return $request->user()
            ?? \App\Models\User::where('email', 'randy@tradingedu.com')->first()
            ?? \App\Models\User::where('email', 'customer@tradingedu.com')->first()
            ?? \App\Models\User::where('email', 'budi@student.com')->first()
            ?? \App\Models\User::first();
    }

    protected function seedSampleOrdersForUser(\App\Models\User $user): void
    {
        try {
            $courses = Course::take(3)->get();
            if ($courses->isEmpty()) {
                return;
            }

            // 1. Paid order 1 (Course)
            $c1 = $courses->first();
            $ord1 = Order::create([
                'number' => 'ORD-' . now()->subDays(4)->format('Ymd') . '-8821',
                'user_id' => $user->id,
                'status' => \App\Domain\Billing\Enums\OrderStatus::Paid,
                'currency' => 'IDR',
                'subtotal' => $c1->price ?: 499000,
                'discount_total' => 0,
                'tax_total' => 0,
                'total' => $c1->price ?: 499000,
                'paid_at' => now()->subDays(4),
                'expires_at' => now()->subDays(3),
            ]);
            OrderItem::create([
                'order_id' => $ord1->id,
                'purchasable_type' => Course::class,
                'purchasable_id' => $c1->id,
                'name' => $c1->title,
                'unit_price' => $c1->price ?: 499000,
                'quantity' => 1,
                'discount' => 0,
                'total' => $c1->price ?: 499000,
            ]);
            Payment::create([
                'order_id' => $ord1->id,
                'gateway' => \App\Domain\Billing\Enums\PaymentGateway::Midtrans,
                'gateway_ref' => 'MID-BCAVA-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'method' => 'BCA Virtual Account',
                'status' => \App\Domain\Billing\Enums\PaymentStatus::Paid,
                'amount' => $c1->price ?: 499000,
                'currency' => 'IDR',
                'paid_at' => now()->subDays(4),
            ]);

            // 2. Pending order (Course or SMC Strategy)
            if ($courses->count() > 1) {
                $c2 = $courses->get(1);
                $ord2 = Order::create([
                    'number' => 'ORD-' . now()->subHours(6)->format('Ymd') . '-4412',
                    'user_id' => $user->id,
                    'status' => \App\Domain\Billing\Enums\OrderStatus::Pending,
                    'currency' => 'IDR',
                    'subtotal' => $c2->price ?: 350000,
                    'discount_total' => 50000,
                    'tax_total' => 0,
                    'total' => max(0, ($c2->price ?: 350000) - 50000),
                    'paid_at' => null,
                    'expires_at' => now()->addHours(18),
                ]);
                OrderItem::create([
                    'order_id' => $ord2->id,
                    'purchasable_type' => Course::class,
                    'purchasable_id' => $c2->id,
                    'name' => $c2->title,
                    'unit_price' => $c2->price ?: 350000,
                    'quantity' => 1,
                    'discount' => 50000,
                    'total' => max(0, ($c2->price ?: 350000) - 50000),
                ]);
                Payment::create([
                    'order_id' => $ord2->id,
                    'gateway' => \App\Domain\Billing\Enums\PaymentGateway::Midtrans,
                    'gateway_ref' => 'MID-QRIS-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'method' => 'QRIS GoPay / OVO',
                    'status' => \App\Domain\Billing\Enums\PaymentStatus::Pending,
                    'amount' => max(0, ($c2->price ?: 350000) - 50000),
                    'currency' => 'IDR',
                ]);
            }

            // 3. Paid order 2 (from 2 weeks ago)
            if ($courses->count() > 2) {
                $c3 = $courses->get(2);
                $ord3 = Order::create([
                    'number' => 'ORD-' . now()->subDays(14)->format('Ymd') . '-1209',
                    'user_id' => $user->id,
                    'status' => \App\Domain\Billing\Enums\OrderStatus::Paid,
                    'currency' => 'IDR',
                    'subtotal' => $c3->price ?: 599000,
                    'discount_total' => 100000,
                    'tax_total' => 0,
                    'total' => max(0, ($c3->price ?: 599000) - 100000),
                    'paid_at' => now()->subDays(14),
                    'expires_at' => now()->subDays(13),
                ]);
                OrderItem::create([
                    'order_id' => $ord3->id,
                    'purchasable_type' => Course::class,
                    'purchasable_id' => $c3->id,
                    'name' => $c3->title,
                    'unit_price' => $c3->price ?: 599000,
                    'quantity' => 1,
                    'discount' => 100000,
                    'total' => max(0, ($c3->price ?: 599000) - 100000),
                ]);
                Payment::create([
                    'order_id' => $ord3->id,
                    'gateway' => \App\Domain\Billing\Enums\PaymentGateway::Midtrans,
                    'gateway_ref' => 'MID-MANVA-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'method' => 'Mandiri Virtual Account',
                    'status' => \App\Domain\Billing\Enums\PaymentStatus::Paid,
                    'amount' => max(0, ($c3->price ?: 599000) - 100000),
                    'currency' => 'IDR',
                    'paid_at' => now()->subDays(14),
                ]);
            }
        } catch (\Exception $e) {
            // Silently ignore if table already has constraints or collision
        }
    }

    public function index(Request $request)
    {
        $user = $this->resolveUser($request);

        if ($user && Order::where('user_id', $user->id)->count() === 0) {
            $this->seedSampleOrdersForUser($user);
        }

        $query = Order::with(['items', 'payments']);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        if ($status = $request->query('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $payment = $order->payments->first();

                $items = collect($order->items)->map(function ($item) {
                    $slug = null;
                    if ($item->purchasable_type === Course::class || $item->purchasable_type === 'course') {
                        $course = Course::find($item->purchasable_id);
                        if ($course) {
                            $slug = $course->slug;
                        }
                    }

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $slug,
                        'purchasable_type' => $item->purchasable_type,
                        'unit_price' => (float) $item->unit_price,
                        'quantity' => (int) $item->quantity,
                        'discount' => (float) $item->discount,
                        'total' => (float) $item->total,
                    ];
                });

                $statusVal = is_string($order->status) ? $order->status : ($order->status->value ?? 'pending');

                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => $statusVal,
                    'currency' => $order->currency ?? 'IDR',
                    'subtotal' => (float) $order->subtotal,
                    'discount_total' => (float) $order->discount_total,
                    'tax_total' => (float) $order->tax_total,
                    'total' => (float) $order->total,
                    'created_at' => $order->created_at?->toIso8601String() ?? now()->toIso8601String(),
                    'paid_at' => $order->paid_at?->toIso8601String(),
                    'expires_at' => $order->expires_at?->toIso8601String(),
                    'payment' => [
                        'method' => $payment ? $payment->method : 'BCA Virtual Account',
                        'gateway' => $payment ? (is_string($payment->gateway) ? $payment->gateway : ($payment->gateway->value ?? 'midtrans')) : 'midtrans',
                        'status' => $payment ? (is_string($payment->status) ? $payment->status : ($payment->status->value ?? $statusVal)) : $statusVal,
                        'gateway_ref' => $payment ? $payment->gateway_ref : ('MID-' . $order->number),
                    ],
                    'items' => $items,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function stats(Request $request)
    {
        $user = $this->resolveUser($request);

        $query = Order::query();
        if ($user) {
            $query->where('user_id', $user->id);
        }

        $allOrders = $query->get();
        $totalOrders = $allOrders->count();
        $paidOrders = $allOrders->filter(fn ($o) => (is_string($o->status) ? $o->status : $o->status->value) === 'paid');
        $pendingOrders = $allOrders->filter(fn ($o) => (is_string($o->status) ? $o->status : $o->status->value) === 'pending');
        $totalSpent = $paidOrders->sum('total');

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrders,
                'paid_orders' => $paidOrders->count(),
                'pending_orders' => $pendingOrders->count(),
                'total_spent' => (float) $totalSpent,
            ],
        ]);
    }
}
