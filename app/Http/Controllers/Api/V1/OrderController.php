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
}
