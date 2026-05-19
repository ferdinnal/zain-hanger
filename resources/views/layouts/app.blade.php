<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_name', 'Zain Hanger') }} — {{ $pageTitle ?? 'Premium Wooden Hanger' }}</title>
    <meta name="description" content="{{ setting('site_description', 'Penyedia hanger kayu premium dan perabot berkualitas tinggi') }}">
    <meta name="orders-quick-url" content="{{ route('orders.quick') }}">
    <meta name="cart-add-url" content="{{ route('cart.add') }}">
    <meta name="login-url" content="{{ route('login') }}">
    <meta name="wa-number" content="{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '6282291409209')) }}">
    <meta name="site-name" content="{{ setting('site_name', 'Zain Hanger') }}">
    <meta name="is-auth" content="{{ auth()->check() ? '1' : '0' }}">
    {{-- Favicon --}}
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ Storage::url(setting('site_favicon')) }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <nav class="navbar glass" id="navbar">
        <div class="container mx-auto px-5">
            <div class="flex items-center justify-between w-full">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex flex-col leading-none cursor-pointer">
                    @if(setting('site_logo'))
                        <img src="{{ Storage::url(setting('site_logo')) }}" alt="{{ setting('site_name', 'Zain Hanger') }}" class="h-10 object-contain">
                    @else
                        <span class="logo-text">{{ setting('site_name_short', 'ZAIN') }}</span>
                        <span class="logo-subtext">{{ setting('site_name_sub', 'HANGER') }}</span>
                    @endif
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden md:flex items-center gap-8" id="nav-links">
                    <a href="{{ route('home') }}"
                       class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        Home
                    </a>

                    {{-- Dropdown Katalog --}}
                    <div class="relative group">
                        <a href="{{ route('catalog.index') }}"
                           class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                            Katalog
                            <svg class="inline w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                        <div class="absolute top-full left-0 mt-3 bg-white rounded-lg shadow-lg min-w-[160px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            @foreach(\App\Models\Category::active()->get() as $cat)
                            <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}"
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-warm-50 hover:text-primary transition-colors">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="#about" class="nav-link">Tentang Kami</a>
                    <a href="#contact" class="nav-link">Kontak</a>
                </div>

                {{-- Nav Actions --}}
                <div class="flex items-center gap-5">
                    {{-- Search --}}
                    <div class="hidden md:flex items-center bg-black/5 rounded-full px-4 py-2 gap-2">
                        <input type="text"
                               placeholder="Cari hanger..."
                               class="bg-transparent border-none outline-none text-sm w-36 font-outfit"
                               onkeydown="if(event.key==='Enter') window.location='/catalog?q='+this.value">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>

                    {{-- User / Login --}}
                    @auth
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-sm font-medium">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=5d4037&color=fff' }}"
                                     class="w-8 h-8 rounded-full object-cover" alt="Avatar">
                            </button>
                            <div class="absolute top-full right-0 mt-3 bg-white rounded-lg shadow-lg min-w-[160px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-warm-50">Pesanan Saya</a>
                                <a href="{{ route('cart.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-warm-50">Keranjang</a>
                                <hr class="border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-primary hover:text-primary-light transition-colors">
                            Login
                        </a>
                    @endauth

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 10H4L5 9z"/>
                        </svg>
                        @auth
                            @php $cartCount = auth()->user()->cartItems()->count() @endphp
                            @if($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        @endauth
                    </a>

                    {{-- Mobile Menu Toggle --}}
                    <button class="md:hidden text-gray-700" id="menu-toggle" onclick="toggleMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </div>

            {{-- Mobile Nav --}}
            <div class="md:hidden hidden flex-col gap-4 pt-4 pb-4" id="mobile-nav">
                <a href="{{ route('home') }}" class="nav-link">Home</a>
                <a href="{{ route('catalog.index') }}" class="nav-link">Katalog</a>
                <a href="#about" class="nav-link">Tentang Kami</a>
                <a href="#contact" class="nav-link">Kontak</a>
            </div>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
        @if(session('success'))
            <div class="container mx-auto px-5 pt-4">
                <div class="alert-success">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mx-auto px-5 pt-4">
                <div class="alert-error">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer" id="contact">
        <div class="container mx-auto px-5 py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-16">

                {{-- Brand --}}
                <div>
                    <div class="mb-5">
                        @if(setting('site_logo'))
                            <img src="{{ Storage::url(setting('site_logo')) }}" alt="{{ setting('site_name') }}" class="h-10 object-contain brightness-0 invert">
                        @else
                            <div class="flex flex-col leading-none">
                                <span class="text-white text-2xl font-bold tracking-widest">{{ setting('site_name_short', 'ZAIN') }}</span>
                                <span style="color: var(--secondary); font-size: 10px; letter-spacing: 4px;">{{ setting('site_name_sub', 'HANGER') }}</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-white/70 text-sm mb-6 max-w-xs">
                        {{ setting('site_description', 'Penyedia hanger kayu premium dan perabot berkualitas tinggi untuk kebutuhan rumah tangga dan bisnis Anda.') }}
                    </p>
                    <div class="flex gap-4 text-sm" style="color: var(--secondary)">
                        @if(setting('social_instagram'))
                            <a href="{{ setting('social_instagram') }}" target="_blank" class="hover:opacity-80 transition-opacity">Instagram</a>
                        @endif
                        @if(setting('social_facebook'))
                            <a href="{{ setting('social_facebook') }}" target="_blank" class="hover:opacity-80 transition-opacity">Facebook</a>
                        @endif
                        @if(setting('social_tiktok'))
                            <a href="{{ setting('social_tiktok') }}" target="_blank" class="hover:opacity-80 transition-opacity">TikTok</a>
                        @endif
                    </div>
                </div>

                {{-- Kategori --}}
                <div>
                    <h3 class="text-lg mb-6" style="color: var(--secondary)">Kategori</h3>
                    <ul class="space-y-3">
                        @foreach(\App\Models\Category::active()->get() as $cat)
                            <li>
                                <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}"
                                   class="text-sm text-white/70 hover:text-white hover:opacity-100 transition-opacity">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Bantuan --}}
                <div>
                    <h3 class="text-lg mb-6" style="color: var(--secondary)">Bantuan</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-white/70 hover:text-white transition-opacity">Cara Pesan</a></li>
                        <li><a href="#" class="text-sm text-white/70 hover:text-white transition-opacity">Pengiriman</a></li>
                        <li><a href="#" class="text-sm text-white/70 hover:text-white transition-opacity">FAQ</a></li>
                    </ul>
                </div>

                {{-- Kontak --}}
                <div id="contact">
                    <h3 class="text-lg mb-6" style="color: var(--secondary)">Hubungi Kami</h3>
                    <div class="space-y-3 text-sm text-white/70">
                        @if(setting('contact_address'))
                            <p>📍 {{ setting('contact_address') }}</p>
                        @endif
                        @if(setting('contact_whatsapp'))
                            <p>📞 {{ setting('contact_whatsapp') }}</p>
                        @endif
                        @if(setting('contact_email'))
                            <p>📧 {{ setting('contact_email') }}</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="border-t border-white/10 py-8 text-center text-xs text-white/50">
            <p>{{ setting('site_copyright', '© '.date('Y').' Zain Hanger. All rights reserved.') }}</p>
        </div>
    </footer>

    {{-- ===== WA FLOATING BUTTON ===== --}}
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '6282291409209')) }}"
       class="wa-floating"
       target="_blank"
       rel="noreferrer"
       title="Chat WhatsApp">
        <div class="wa-tooltip">Ada pertanyaan? Chat kami!</div>
        <svg class="w-7 h-7" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    {{-- ===== SCRIPTS ===== --}}
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });

        // Mobile menu toggle
        function toggleMenu() {
            const mobileNav = document.getElementById('mobile-nav');
            mobileNav.classList.toggle('hidden');
            mobileNav.classList.toggle('flex');
        }
    </script>

    @stack('scripts')
</body>
</html>
