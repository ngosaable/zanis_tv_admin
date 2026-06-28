<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Zanis TV') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen flex">

    <!-- Sidebar (Desktop) -->
    <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex md:flex-col">
        <!-- Brand -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-gray-800">
                {{ config('app.name', 'Zanis TV') }}
            </a>
        </div>

        <!-- Links -->
        <nav class="flex-1 px-4 py-4 space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Content
                </p>
            </div>

            <!-- Categories -->
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Categories</span>
            </a>

            <!-- Videos -->
            <a href="{{ route('admin.videos.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.videos.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 4h10l1 3h3v4h-2l1 10H4L5 11H3V7h3l1-3z" />
                </svg>
                <span>Videos</span>
            </a>

            <!-- Live Channels -->
            <a href="{{ route('admin.live-channels.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.live-channels.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 6h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                </svg>
                <span>Live Channels</span>
            </a>

            
            <!-- Sliders -->
            <a href="{{ route('admin.sliders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.sliders.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16v12H4V6zm3 3h4v3H7V9zm6 0h4v6h-4V9z" />
                </svg>
                <span>Sliders</span>
            </a>

            <!-- Ads -->
            <a href="{{ route('admin.ads.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('admin.ads.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5h2m-1 0v14m-7-7h14" />
                </svg>
                <span>Ads</span>
            </a>

            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Account
                </p>
            </div>

            <!-- Profile -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
               {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A10 10 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Profile</span>
            </a>

        </nav>

        <!-- Sidebar footer -->
        <div class="border-t border-gray-200 p-4">
            <div class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 text-left px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

        <!-- Top bar -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6">

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')"
                        class="p-2 rounded-md text-gray-600 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="text-sm text-gray-600 hidden md:block">
                Admin Panel
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:underline">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Sidebar (Mobile) -->
        <div id="mobileSidebar" class="hidden md:hidden bg-white border-b border-gray-200">
            <div class="px-4 py-3 space-y-2">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span>Categories</span>
                </a>

                <a href="{{ route('admin.videos.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.videos.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 4h10l1 3h3v4h-2l1 10H4L5 11H3V7h3l1-3z" />
                    </svg>
                    <span>Videos</span>
                </a>

                <a href="{{ route('admin.live-channels.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.live-channels.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 6h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                    </svg>
                    <span>Live Channels</span>
                </a>

                
                <a href="{{ route('admin.sliders.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.sliders.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16v12H4V6zm3 3h4v3H7V9zm6 0h4v6h-4V9z" />
                    </svg>
                    <span>Sliders</span>
                </a>

                <a href="{{ route('admin.ads.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('admin.ads.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5h2m-1 0v14m-7-7h14" />
                    </svg>
                    <span>Ads</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A10 10 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Profile</span>
                </a>

            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>

    </div>
</div>
</body>
</html>
