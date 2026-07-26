<!DOCTYPE html>
<html>
<head>
    <title>Beri Ulasan untuk {{ $transaction->event->title }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w-xl mx-auto p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 style="color: #4f46e5;">Halo {{ $transaction->customer_name }}!</h2>
        <p>Terima kasih telah hadir di <strong>{{ $transaction->event->title }}</strong>.</p>
        <p>Kami harap Anda mendapatkan pengalaman yang luar biasa. Bantu kami dan penyelenggara ({{ $transaction->event->partner ? $transaction->event->partner->name : 'Penyelenggara' }}) untuk terus menjadi lebih baik dengan memberikan ulasan Anda.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/reviews/create/' . $transaction->order_id) }}" style="background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                Beri Ulasan & Bintang
            </a>
        </div>
        
        <p style="font-size: 0.9em; color: #666;">
            Sistem Ulasan Amikom Event Hub
        </p>
    </div>
</body>
</html>
