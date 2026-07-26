<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmikomEventHub - Temukan Event Seru!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }

        /* Hide HTML5 Up and Down arrows on number inputs for clean manual typing */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav
        class="glass sticky top-8 z-40 mx-4 mt-4 px-6 py-4 rounded-2xl border border-white/20 shadow-lg flex justify-between items-center">
        <a href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-90 transition">
            <div
                class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md shadow-indigo-200">
                AH</div>
            <span class="text-xl font-bold tracking-tight text-slate-900">AmikomEventHub</span>
        </a>
        <div class="hidden md:flex items-center gap-8 font-medium nav-links">
            <a href="{{ url('/#jelajahi') }}" data-section="jelajahi" class="{{ request()->routeIs('home') ? 'text-indigo-600 font-bold active-nav' : 'text-slate-600 hover:text-indigo-600 transition' }}">Jelajahi</a>
            <a href="{{ url('/#kategori') }}" data-section="kategori" class="text-slate-600 hover:text-indigo-600 transition">Kategori</a>
            <a href="{{ url('/#footer') }}" data-section="footer" class="text-slate-600 hover:text-indigo-600 transition">Tentang Kami</a>
            <a href="{{ route('my-tickets') }}" class="{{ request()->routeIs('my-tickets*') || request()->routeIs('ticket*') ? 'text-indigo-600 font-bold active-nav' : 'text-slate-600 hover:text-indigo-600 transition' }}">E-Ticket</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <div class="flex items-center gap-3 pl-2 border-l border-slate-200/60">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs uppercase shadow-sm flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-semibold text-slate-700 max-w-[140px] sm:max-w-none truncate">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl font-semibold transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-slate-200/70 transition text-sm sm:text-base">Login</a>
                <a href="{{ route('register') }}"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition text-sm sm:text-base">Daftar</a>
            @endauth
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer id="footer" class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-12">
            <div class="space-y-4 col-span-1 md:col-span-2">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">
                        AH</div>
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-xs text-indigo-300">Platform reservasi tiket event online terbaik untuk mahasiswa dan penyelenggara profesional.</p>
            </div>

            <div>
                <h4 class="text-white font-bold mb-6">Navigasi</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="hover:text-white transition">Home</a></li>
                    <li><a href="#" class="hover:text-white transition">Semua Event</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Bayar</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-6">Kategori</h4>
                <ul class="space-y-4">
                    @foreach(\App\Models\Category::take(5)->get() as $footerCategory)
                    <li><a href="/?category={{ $footerCategory->slug }}" class="hover:text-white transition">{{ $footerCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li><a href="mailto:support@eventtiket.com" class="hover:text-white transition">support@eventtiket.com</a></li>
                    <li><a href="https://wa.me/6281234567890" class="hover:text-white transition">+62 812 3456 7890</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2024 AmikomEventHub. Built with Laravel & Tailwind CSS.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.nav-links a[data-section]');
            const isHomePage = window.location.pathname === '/' || window.location.pathname === '/index.php';

            if (!isHomePage || navLinks.length === 0) return;

            const setActiveLink = (sectionId) => {
                navLinks.forEach(link => {
                    if (link.getAttribute('data-section') === sectionId) {
                        link.className = 'text-indigo-600 font-bold active-nav transition';
                    } else {
                        link.className = 'text-slate-600 hover:text-indigo-600 transition';
                    }
                });
            };

            // Klik handler: langsung ganti warna saat diklik
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const sectionId = this.getAttribute('data-section');
                    if (sectionId) setActiveLink(sectionId);
                });
            });

            // Scroll spy: ganti warna otomatis saat gulir ke bagian tertentu
            const sections = ['jelajahi', 'kategori', 'footer'].map(id => document.getElementById(id)).filter(Boolean);
            if (sections.length > 0) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            setActiveLink(entry.target.id);
                        }
                    });
                }, { threshold: 0.3, rootMargin: '-80px 0px -40% 0px' });

                sections.forEach(sec => observer.observe(sec));
            }
        });
    </script>
</body>

</html>