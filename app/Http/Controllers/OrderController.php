<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function create()
    {
        // ENHANCED: Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk melanjutkan checkout.');
        }

        $cart = session()->get('cart');

        // ENHANCED: Better cart validation
        if (!$cart || !is_array($cart) || count($cart) == 0) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang belanja Anda kosong. Silakan tambahkan menu terlebih dahulu.');
        }

        // ENHANCED: Validate cart items still exist and available
        $validCart = [];
        $hasInvalidItems = false;

        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);

            if (!$menu) {
                $hasInvalidItems = true;
                session()->forget("cart.{$id}");
                continue;
            }

            if (!$menu->isAvailable) {
                $hasInvalidItems = true;
                session()->forget("cart.{$id}");
                continue;
            }

            // Check if price changed
            if ($menu->price != $details['price']) {
                $details['price'] = $menu->price;
                $cart[$id] = $details;
            }

            $validCart[$id] = $details;
        }

        // Update cart with valid items only
        session()->put('cart', $validCart);

        if (empty($validCart)) {
            return redirect()->route('menus.index')
                ->with('error', 'Menu yang Anda pilih tidak tersedia. Silakan pilih menu lain.');
        }

        if ($hasInvalidItems) {
            session()->flash('warning', 'Beberapa item di keranjang tidak tersedia dan telah dihapus.');
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($validCart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = 15000;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Process checkout - ENHANCED ERROR HANDLING
     */
    public function store(Request $request)
    {
        // ENHANCED: Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // ENHANCED: Validate request
        try {
            $validated = $request->validate([
                'pickup_date' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                    'before:' . now()->addMonths(1)->format('Y-m-d'), // Max 1 month ahead
                ],
                'payment_proof' => [
                    'required',
                    'image',
                    'mimes:jpeg,jpg,png,webp',
                    'max:2048', // 2MB
                    'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000'
                ],
                'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9]+$/',
                'notes' => 'nullable|string|max:500'
            ], [
                'pickup_date.required' => 'Tanggal pengambilan wajib diisi.',
                'pickup_date.after_or_equal' => 'Tanggal pengambilan minimal hari ini.',
                'pickup_date.before' => 'Tanggal pengambilan maksimal 1 bulan ke depan.',
                'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
                'payment_proof.image' => 'File harus berupa gambar.',
                'payment_proof.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WEBP.',
                'payment_proof.max' => 'Ukuran file maksimal 2MB.',
                'payment_proof.dimensions' => 'Dimensi gambar minimal 100x100px dan maksimal 5000x5000px.',
                'phone.min' => 'Nomor telepon minimal 10 digit.',
                'phone.max' => 'Nomor telepon maksimal 15 digit.',
                'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
                'notes.max' => 'Catatan maksimal 500 karakter.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // ENHANCED: Validate cart
        $cart = session()->get('cart');

        if (!$cart || !is_array($cart) || count($cart) == 0) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang Anda kosong. Transaksi dibatalkan.');
        }

        $pickupDate = $request->pickup_date;

        // ENHANCED: Validate menu availability and quota
        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);

            // Check if menu exists
            if (!$menu) {
                return back()->withErrors([
                    'cart' => "Menu dengan ID {$id} tidak ditemukan. Silakan refresh keranjang."
                ])->withInput();
            }

            // Check if menu is available
            if (!$menu->isAvailable) {
                return back()->withErrors([
                    'cart' => "Menu '{$menu->name}' saat ini tidak tersedia."
                ])->withInput();
            }

            // ENHANCED: Check quota with pessimistic locking (inside transaction later)
            // This validation will be done again inside transaction for safety
            $bookedQty = OrderItem::where('menu_id', $id)
                ->whereHas('order', function ($q) use ($pickupDate) {
                    $q->whereDate('pickup_date', $pickupDate)
                        ->whereIn('status', ['payment_pending', 'ready_for_pickup']);
                })
                ->sum('quantity');

            $dailyLimit = $menu->daily_limit ?? 50;
            $requestedQty = $details['quantity'];

            if (($bookedQty + $requestedQty) > $dailyLimit) {
                $sisa = max(0, $dailyLimit - $bookedQty);
                return back()->withErrors([
                    'pickup_date' => "Menu '{$menu->name}' untuk tanggal tersebut sudah penuh. Kuota tersisa: {$sisa} porsi."
                ])->withInput();
            }
        }

        // ENHANCED: Process order with transaction
        DB::beginTransaction();
        try {
            // Calculate total
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = 15000;
            $total = $subtotal + $shipping;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => 'payment_pending',
                'pickup_date' => $pickupDate,
                'notes' => $request->notes,
                'total_price' => $total,
                'customer_name' => Auth::user()->name,
                'customer_phone' => $request->phone ?? Auth::user()->phone ?? '-',
            ]);

            // ENHANCED: Create order items with quota recheck (pessimistic locking for race condition fix)
            foreach ($cart as $id => $details) {
                // Lock the menu row for this transaction to prevent race conditions
                $menu = Menu::where('id', $id)->lockForUpdate()->first();

                if (!$menu) {
                    throw new \Exception("Menu dengan ID {$id} tidak ditemukan saat pembuatan order.");
                }

                // Recheck quota inside transaction with lock
                $bookedQtyInTransaction = OrderItem::where('menu_id', $id)
                    ->whereHas('order', function ($q) use ($pickupDate) {
                        $q->whereDate('pickup_date', $pickupDate)
                            ->whereIn('status', ['payment_pending', 'ready_for_pickup']);
                    })
                    ->lockForUpdate() // Lock related order items
                    ->sum('quantity');

                $dailyLimit = $menu->daily_limit ?? 50;

                if (($bookedQtyInTransaction + $details['quantity']) > $dailyLimit) {
                    throw new \Exception("Race condition detected: Menu '{$menu->name}' sudah penuh saat pembuatan order.");
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_purchase' => $details['price'],
                ]);
            }

            // ENHANCED: Safe file upload
            if ($request->hasFile('payment_proof')) {
                try {
                    $file = $request->file('payment_proof');

                    // Additional file validation
                    if (!$file->isValid()) {
                        throw new \Exception('File upload gagal. File corrupt atau tidak valid.');
                    }

                    // Sanitize filename
                    $extension = $file->extension();
                    $filename = 'payment_' . $order->id . '_' . uniqid() . '_' . time() . '.' . $extension;

                    // Store with error handling
                    $path = $file->storeAs('payments', $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Gagal menyimpan file bukti pembayaran.');
                    }

                    // Verify file was saved
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception('File bukti pembayaran tidak tersimpan dengan benar.');
                    }

                    // Update order
                    $order->payment_proof = $path;
                    $order->save();

                    // Create payment record
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $total,
                        'proof_image' => $path,
                        'status' => 'pending',
                    ]);

                } catch (\Exception $e) {
                    // Log file upload error
                    Log::error('Payment proof upload failed', [
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage()
                    ]);

                    throw new \Exception('Gagal mengupload bukti pembayaran: ' . $e->getMessage());
                }
            }

            // Clear cart
            session()->forget('cart');

            // Commit transaction
            DB::commit();

            // Log successful order
            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'invoice_code' => $order->invoice_code,
                'user_id' => Auth::id(),
                'total' => $total
            ]);

            // ENHANCED: Send order confirmation email
            try {
                \Mail::to(Auth::user()->email)->send(new \App\Mail\OrderConfirmation($order));
                Log::info('Order confirmation email sent', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                // Don't fail if email fails, just log it
                Log::warning('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();

            // ENHANCED: Detailed error logging
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pickup_date' => $pickupDate ?? null,
                'total_items' => count($cart ?? []),
            ]);

            // ENHANCED: User-friendly error message
            $errorMessage = 'Terjadi kesalahan saat memproses pesanan Anda. ';

            if (str_contains($e->getMessage(), 'upload') || str_contains($e->getMessage(), 'file')) {
                $errorMessage .= 'Kemungkinan masalah dengan upload bukti pembayaran. Pastikan file valid dan tidak corrupt.';
            } elseif (str_contains($e->getMessage(), 'quota') || str_contains($e->getMessage(), 'penuh')) {
                $errorMessage .= 'Menu yang Anda pilih mungkin sudah penuh.';
            } else {
                $errorMessage .= 'Silakan coba lagi atau hubungi customer service.';
            }

            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Show success page - ENHANCED
     */
    public function success($id)
    {
        // ENHANCED: Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login untuk melihat detail pesanan.');
        }

        try {
            $order = Order::with('items.menu')->findOrFail($id);

            // ENHANCED: Authorization check
            if ($order->user_id !== Auth::id()) {
                Log::warning('Unauthorized order access attempt', [
                    'order_id' => $id,
                    'order_owner' => $order->user_id,
                    'accessor' => Auth::id()
                ]);
                abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
            }

            return view('checkout.success', compact('order'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found', [
                'order_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('menus.index')
                ->with('error', 'Pesanan tidak ditemukan.');
        }
    }
}
