<x-layouts.app>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}</p>
    </div>

    @if (count($stats) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($stats), 4) }} gap-6 mb-6">
            @foreach ($stats as $key => $stat)
                @php
                    $iconMap = [
                        'star' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                        'video' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                        'image' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                        'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    ];

                    $colorClasses = [
                        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-500 dark:text-blue-300', 'badge' => 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400'],
                        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-500 dark:text-purple-300', 'badge' => 'bg-purple-50 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400'],
                        'green' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-500 dark:text-green-300', 'badge' => 'bg-green-50 dark:bg-green-900/50 text-green-600 dark:text-green-400'],
                        'orange' => ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-500 dark:text-orange-300', 'badge' => 'bg-orange-50 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400'],
                        'indigo' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900', 'text' => 'text-indigo-500 dark:text-indigo-300', 'badge' => 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400'],
                    ];

                    $colors = $colorClasses[$stat['color']] ?? $colorClasses['blue'];
                    $iconPath = $iconMap[$stat['icon']] ?? $iconMap['star'];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($stat['title']) }}</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($stat['count']) }}</p>
                            @if (isset($stat['active_count']))
                                <div class="mt-3 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors['badge'] }}">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $stat['active_count'] }} {{ __('Active') }}
                                    </span>
                                    @if ($stat['count'] - $stat['active_count'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            {{ $stat['count'] - $stat['active_count'] }} {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="{{ $colors['bg'] }} p-4 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 {{ $colors['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 border border-gray-200 dark:border-gray-700 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No Statistics Available') }}</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('You don\'t have permission to view any dashboard statistics.') }}</p>
        </div>
    @endif

    @if (count($stats) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Quick Actions') }}</h2>
                <div class="space-y-3">
                    @can('view reviews')
                        <a href="{{ route('reviews.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-500 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manage Reviews') }}</span>
                        </a>
                    @endcan
                    @can('view videos')
                        <a href="{{ route('videos.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-purple-500 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manage Videos') }}</span>
                        </a>
                    @endcan
                    @can('view gallery')
                        <a href="{{ route('gallery.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manage Gallery') }}</span>
                        </a>
                    @endcan
                    @can('view what we do')
                        <a href="{{ route('what-we-do.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="bg-orange-100 dark:bg-orange-900 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-orange-500 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manage What We Do') }}</span>
                        </a>
                    @endcan
                    @can('view users')
                        <a href="{{ route('users.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="bg-indigo-100 dark:bg-indigo-900 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-500 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manage Users') }}</span>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- System Info Card -->
            @can('view roles')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('System Information') }}</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Laravel Version') }}</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('PHP Version') }}</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Environment') }}</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ ucfirst(app()->environment()) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Your Role') }}</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ auth()->user()->roles->first()->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    @endif

</x-layouts.app>
