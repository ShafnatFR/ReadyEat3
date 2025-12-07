<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->menu = Menu::factory()->create([
            'name' => 'Test Menu',
            'price' => 50000,
            'isAvailable' => true,
            'daily_limit' => 10
        ]);
    }

    /** @test */
    public function guest_cannot_access_checkout()
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_checkout_with_cart()
    {
        // Add item to cart
        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 2,
                'image' => $this->menu->image
            ]
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');
        $response->assertViewHas('cart');
        $response->assertViewHas('total');
    }

    /** @test */
    public function checkout_redirects_if_cart_is_empty()
    {
        $response = $this->actingAs($this->user)->get(route('checkout.index'));

        $response->assertRedirect(route('menus.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_place_order_successfully()
    {
        Storage::fake('public');

        // Add to cart
        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 2,
                'image' => $this->menu->image
            ]
        ]);

        $file = UploadedFile::fake()->image('payment.jpg', 200, 200);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'pickup_date' => now()->addDays(1)->format('Y-m-d'),
            'payment_proof' => $file,
            'phone' => '081234567890',
            'notes' => 'Test order'
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'payment_pending',
            'customer_name' => $this->user->name,
            'customer_phone' => '081234567890',
            'notes' => 'Test order'
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_id' => $this->menu->id,
            'quantity' => 2,
            'price' => $this->menu->price
        ]);

        $order = Order::latest()->first();
        $response->assertRedirect(route('checkout.success', $order->id));

        // Check cart is cleared
        $this->assertNull(session('cart'));
    }

    /** @test */
    public function checkout_requires_payment_proof()
    {
        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 1,
                'image' => $this->menu->image
            ]
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'pickup_date' => now()->addDays(1)->format('Y-m-d'),
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors('payment_proof');
    }

    /** @test */
    public function checkout_validates_pickup_date()
    {
        Storage::fake('public');

        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 1,
                'image' => $this->menu->image
            ]
        ]);

        $file = UploadedFile::fake()->image('payment.jpg');

        // Test past date
        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'pickup_date' => now()->subDays(1)->format('Y-m-d'),
            'payment_proof' => $file,
        ]);

        $response->assertSessionHasErrors('pickup_date');
    }

    /** @test */
    public function checkout_checks_menu_quota()
    {
        Storage::fake('public');

        // Set menu limit to 5
        $this->menu->update(['daily_limit' => 5]);

        // Create existing orders that fill 4 slots
        Order::factory()->create([
            'pickup_date' => now()->addDays(1)->format('Y-m-d'),
            'status' => 'payment_pending'
        ])->items()->create([
                    'menu_id' => $this->menu->id,
                    'quantity' => 4,
                    'price' => $this->menu->price
                ]);

        // Try to order 3 (total would be 7, exceeds limit of 5)
        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 3,
                'image' => $this->menu->image
            ]
        ]);

        $file = UploadedFile::fake()->image('payment.jpg');

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'pickup_date' => now()->addDays(1)->format('Y-m-d'),
            'payment_proof' => $file,
        ]);

        $response->assertSessionHasErrors('pickup_date');
    }

    /** @test */
    public function checkout_validates_file_type()
    {
        Storage::fake('public');

        session()->put('cart', [
            $this->menu->id => [
                'name' => $this->menu->name,
                'price' => $this->menu->price,
                'quantity' => 1,
                'image' => $this->menu->image
            ]
        ]);

        $file = UploadedFile::fake()->create('document.pdf');

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'pickup_date' => now()->addDays(1)->format('Y-m-d'),
            'payment_proof' => $file,
        ]);

        $response->assertSessionHasErrors('payment_proof');
    }

    /** @test */
    public function only_order_owner_can_view_success_page()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id
        ]);

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->get(route('checkout.success', $order->id));

        $response->assertStatus(403);
    }
}
