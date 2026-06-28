<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left Side -->
            <div class="flex">
                <!-- Logo / App Name -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-gray-800">
                        {{ config('app.name', 'Zanis TV') }}
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex items-center">

                    <a href="{{ route('admin.dashboard') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Dashboard
                    </a>

                    <!-- Admin Links -->
                    <a href="{{ route('admin.categories.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Categories
                    </a>

                    <a href="{{ route('admin.videos.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.videos.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Videos
                    </a>

                    <a href="{{ route('admin.live-channels.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.live-channels.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Live Channels
                    </a>

                    
                    <a href="{{ route('admin.sliders.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.sliders.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Sliders
                    </a>

                    <a href="{{ route('admin.ads.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('admin.ads.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                        Ads
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Mobile Button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-4">

            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.categories.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Categories
            </a>

            <a href="{{ route('admin.videos.index') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.videos.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Videos
            </a>

            <a href="{{ route('admin.live-channels.index') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.live-channels.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Live Channels
            </a>

            
            <a href="{{ route('admin.sliders.index') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.sliders.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Sliders
            </a>

            <a href="{{ route('admin.ads.index') }}"
               class="block px-3 py-2 rounded text-base font-medium {{ request()->routeIs('admin.ads.*') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }}">
                Ads
            </a>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 px-4">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 rounded text-base font-medium text-gray-700 hover:text-blue-600">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 rounded text-base font-medium text-gray-700 hover:text-blue-600">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
