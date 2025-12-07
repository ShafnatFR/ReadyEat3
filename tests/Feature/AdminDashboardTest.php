<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function only_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function admin_can_filter_dashboard_by_time_period()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'filter_type' => 'weekly',
            'selected_date' => now()->format('o-\WW')
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /** @test */
    public function admin_can_verify_order()
    {
        Storage::fake('public');

        $order = Order::factory()->create([
            'status' => 'payment_pending',
            'pickup_date' => now()->addDays(1)->format('Y-m-d')
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.accept', $order), [
            'status' => 'ready_for_pickup',
            'pickup_date' => $order->pickup_date,
            'admin_note' => 'Payment verified'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'ready_for_pickup',
            'admin_note' => 'Payment verified'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function admin_can_reject_order()
    {
        $order = Order::factory()->create([
            'status' => 'payment_pending'
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.reject', $order), [
            'admin_note' => 'Invalid payment proof'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'admin_note' => 'Invalid payment proof'
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function admin_can_mark_order_as_completed()
    {
        $order = Order::factory()->create([
            'status' => 'ready_for_pickup'
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.complete', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'picked_up'
        ]);

        $response->assertRedirect();
    }
}
