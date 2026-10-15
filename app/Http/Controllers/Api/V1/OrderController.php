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

    public function index(Request $request)
    {
        $user = $request->user();
        
        $orders = Order::with(['items', 'payments'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                // If it's a course order, try to get the course slug from items
                $slug = null;
                $courseItem = collect($order->items)->firstWhere('purchasable_type', Course::class);
                if ($courseItem) {
                    $course = Course::find($courseItem->purchasable_id);
                    if ($course) {
                        $slug = $course->slug;
                    }
                }

                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => is_string($order->status) ? $order->status : ($order->status->value ?? 'pending'),
                    'total' => $order->total,
                    'created_at' => $order->created_at,
                    'items' => collect($order->items)->map(fn ($item) => [
                        'name' => $item->name,
                        'slug' => $slug,
                    ]),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
}
