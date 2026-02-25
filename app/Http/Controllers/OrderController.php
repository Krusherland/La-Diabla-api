<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create new order (public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|in:efectivo,tarjeta,transferencia,mercadopago',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            // Validate products and calculate subtotal
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Product with ID {$item['product_id']} not found");
                }

                if (!$product->is_active) {
                    throw new \Exception("Product '{$product->name}' is not available");
                }

                if (!$product->hasStock($item['quantity'])) {
                    throw new \Exception("Insufficient stock for product '{$product->name}'");
                }

                $itemPrice = $product->final_price;
                $itemSubtotal = $itemPrice * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $deliveryFee = $request->delivery_fee ?? 0;
            $total = $subtotal + $deliveryFee;

            // Create order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'delivery_address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pendiente',
                'order_status' => 'pendiente',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $request->notes,
            ]);

            // Create order items and reduce stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce product stock
                $item['product']->reduceStock($item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => Order::with('orderItems.product')->find($order->id)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all orders (admin)
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product']);

        // Filter by order status
        if ($request->has('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by customer name, email, or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
            'statistics' => [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('order_status', 'pendiente')->count(),
                'preparing_orders' => Order::where('order_status', 'preparando')->count(),
                'shipped_orders' => Order::where('order_status', 'enviado')->count(),
                'delivered_orders' => Order::where('order_status', 'entregado')->count(),
                'total_revenue' => Order::where('payment_status', 'pagado')->sum('total'),
                'pending_payments' => Order::where('payment_status', 'pendiente')->sum('total'),
            ]
        ], 200);
    }

    /**
     * Get single order details
     */
    public function show($id)
    {
        $order = Order::with(['orderItems.product.category'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }

    /**
     * Track order by ID or email (public)
     */
    public function track(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderId' => 'nullable|integer',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $query = Order::with(['orderItems.product']);

        if ($request->has('orderId')) {
            $query->where('id', $request->orderId);
        }

        $query->where('customer_email', $request->email);

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }

    /**
     * Get user orders by email (authenticated user)
     */
    public function getUserOrders($email)
    {
        $orders = Order::with(['orderItems.product'])
            ->where('customer_email', $email)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    /**
     * Update order status (admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'order_status' => 'nullable|in:pendiente,preparando,enviado,entregado,cancelado',
            'payment_status' => 'nullable|in:pendiente,pagado,rechazado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        if ($request->has('order_status')) {
            $order->updateStatus($request->order_status);
        }

        if ($request->has('payment_status')) {
            $order->updatePaymentStatus($request->payment_status);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order updated successfully',
            'order' => $order->fresh()
        ], 200);
    }

    /**
     * Delete order (admin)
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        // Only allow deletion of cancelled orders
        if ($order->order_status !== 'cancelado') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only cancelled orders can be deleted'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Order deleted successfully'
        ], 200);
    }

    /**
     * Get order statistics (admin)
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now());

        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        return response()->json([
            'status' => 'success',
            'statistics' => [
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->where('payment_status', 'pagado')->sum('total'),
                'average_order_value' => $orders->avg('total'),
                'by_status' => [
                    'pendiente' => Order::where('order_status', 'pendiente')->count(),
                    'preparando' => Order::where('order_status', 'preparando')->count(),
                    'enviado' => Order::where('order_status', 'enviado')->count(),
                    'entregado' => Order::where('order_status', 'entregado')->count(),
                    'cancelado' => Order::where('order_status', 'cancelado')->count(),
                ],
                'by_payment_method' => [
                    'efectivo' => Order::where('payment_method', 'efectivo')->count(),
                    'tarjeta' => Order::where('payment_method', 'tarjeta')->count(),
                    'transferencia' => Order::where('payment_method', 'transferencia')->count(),
                ],
                'by_payment_status' => [
                    'pendiente' => Order::where('payment_status', 'pendiente')->count(),
                    'pagado' => Order::where('payment_status', 'pagado')->count(),
                    'rechazado' => Order::where('payment_status', 'rechazado')->count(),
                ],
            ]
        ], 200);
    }
}
