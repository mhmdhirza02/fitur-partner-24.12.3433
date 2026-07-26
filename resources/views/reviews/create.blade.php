@extends('layouts.app')

@section('title', 'Beri Ulasan - ' . $transaction->event->title)

@section('content')
<main class="max-w-3xl mx-auto px-6 py-20">
    <div class="bg-white rounded-3xl border border-slate-200 p-10 shadow-xl relative overflow-hidden">
        <!-- Decor -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

        <div class="text-center mb-10 relative z-10">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Bagaimana Pengalaman Anda?</h1>
            <p class="text-slate-500">Beri nilai untuk event <strong class="text-indigo-600">{{ $transaction->event->title }}</strong></p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('reviews.store', $transaction->order_id) }}" method="POST" class="relative z-10 space-y-8">
            @csrf
            
            <!-- Rating Bintang -->
            <div class="flex flex-col items-center">
                <label class="block text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Pilih Bintang</label>
                <div class="flex gap-2 flex-row-reverse justify-center group" id="star-rating">
                    <input type="radio" name="rating" id="star5" value="5" class="peer hidden" required>
                    <label for="star5" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>

                    <input type="radio" name="rating" id="star4" value="4" class="peer hidden">
                    <label for="star4" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>

                    <input type="radio" name="rating" id="star3" value="3" class="peer hidden">
                    <label for="star3" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>

                    <input type="radio" name="rating" id="star2" value="2" class="peer hidden">
                    <label for="star2" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>

                    <input type="radio" name="rating" id="star1" value="1" class="peer hidden">
                    <label for="star1" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>
                </div>
                <style>
                    /* Style to make subsequent siblings highlight on hover (CSS magic for star rating) */
                    #star-rating label:hover,
                    #star-rating label:hover ~ label {
                        color: #fbbf24 !important; /* amber-400 */
                    }
                </style>
            </div>

            <!-- Komentar -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Ceritakan pengalamanmu (Opsional)</label>
                <textarea name="comment" rows="4" placeholder="Apa yang paling berkesan dari acara ini?"
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 focus:bg-white outline-none transition font-medium resize-none"></textarea>
            </div>

            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                Kirim Ulasan
            </button>
        </form>
    </div>
</main>
@endsection
