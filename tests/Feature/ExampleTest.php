<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_free_event_checkout_bypasses_midtrans(): void
    {
        $category = Category::create(['name' => 'Seminar', 'slug' => 'seminar']);
        
        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Webinar Gratis AI 2026',
            'description' => 'Belajar AI gratis untuk semua',
            'date' => '2026-08-10 10:00:00',
            'location' => 'Online Zoom',
            'price' => 0, // Acara Gratis
            'stock' => 100,
            'poster_path' => 'posters/free.png',
        ]);

        $response = $this->post(route('checkout.store', $event->id), [
            'customer_name' => 'Budi Gratis',
            'customer_email' => 'budi@gmail.com',
            'customer_phone' => '08123456789',
        ]);

        // Cek stok langsung berkurang (100 -> 99)
        $this->assertEquals(99, $event->fresh()->stock);

        // Cek transaksi terekam dengan status 'success' dan total_price 0
        $transaction = Transaction::where('customer_email', 'budi@gmail.com')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('success', $transaction->status);
        $this->assertEquals(0, $transaction->total_price);

        // Cek redirect langsung ke halaman sukses (Bypass Midtrans)
        $response->assertRedirect(route('checkout.success', $transaction->order_id));
    }
}


