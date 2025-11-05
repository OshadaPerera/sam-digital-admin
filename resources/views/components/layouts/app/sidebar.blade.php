            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }" class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house' :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                            {{-- Review Management --}}
                            @can('view reviews')
                                <x-layouts.sidebar-link href="{{ route('reviews.index') }}" icon='fas-star' :active="request()->routeIs('reviews*')">Reviews</x-layouts.sidebar-link>
                            @endcan

                            {{-- What We Do --}}
                            @can('view what we do')
                                <x-layouts.sidebar-link href="{{ route('what-we-do.index') }}" icon='fas-briefcase' :active="request()->routeIs('what-we-do*')">What We Do</x-layouts.sidebar-link>
                            @endcan

                            {{-- Gallery --}}
                            @can('view gallery')
                                <x-layouts.sidebar-link href="{{ route('gallery.index') }}" icon='fas-images' :active="request()->routeIs('gallery*')">Gallery</x-layouts.sidebar-link>
                            @endcan

                            {{-- Business Profile --}}
                            @can('view business profile')
                                <x-layouts.sidebar-link href="{{ route('business-profile.index') }}" icon='fas-building' :active="request()->routeIs('business.profile')">Business Profile</x-layouts.sidebar-link>
                            @endcan

                            {{-- User Management --}}
                            @can('view users')
                                <x-layouts.sidebar-link href="{{ route('users.index') }}" icon='fas-users' :active="request()->routeIs('users*')">User Management</x-layouts.sidebar-link>
                            @endcan

                            <!-- Example two level -->
                            {{-- <x-layouts.sidebar-two-level-link-parent title="Example two level" icon="fas-house" :active="request()->routeIs('two-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house' :active="request()->routeIs('two-level*')">Child</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent> --}}

                            <!-- Example three level -->
                            {{-- <x-layouts.sidebar-two-level-link-parent title="Example three level" icon="fas-house" :active="request()->routeIs('three-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house' :active="request()->routeIs('three-level*')">Single Link</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-three-level-parent title="Third Level" icon="fas-house" :active="request()->routeIs('three-level*')">
                                    <x-layouts.sidebar-three-level-link href="#" :active="request()->routeIs('three-level*')">
                                        Third Level Link
                                    </x-layouts.sidebar-three-level-link>
                                </x-layouts.sidebar-three-level-parent>
                            </x-layouts.sidebar-two-level-link-parent> --}}
                        </ul>
                    </nav>
                </div>
            </aside>
