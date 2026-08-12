<x-layouts.app>
    <div x-data="slideShow">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="{{ route('slider.index') }}" class="text-primary hover:underline">Slider</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Slide #{{ $slide->order }}</span>
            </li>
        </ul>

        {{-- Slide Details --}}
        <div class="panel mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                {{-- Media Preview --}}
                <div class="md:w-1/3">
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($slide->type === 'image')
                            @if($slide->media_path)
                                <img src="{{ asset('storage/' . $slide->media_path) }}" 
                                     alt="{{ $slide->alt_text ?? 'Slide image' }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-300 dark:text-gray-600 text-6xl"></i>
                                </div>
                            @endif
                        @else
                            @if($slide->media_path)
                                <video src="{{ asset('storage/' . $slide->media_path) }}" 
                                       class="w-full h-full object-cover" 
                                       controls></video>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-video text-gray-300 dark:text-gray-600 text-6xl"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Slide Info --}}
                <div class="md:w-2/3">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h5 class="text-2xl font-semibold dark:text-white-light mb-2">Slide #{{ $slide->order }}</h5>
                            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 flex-wrap">
                                <span>
                                    @if ($slide->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-success/10 text-success font-medium">
                                            <i class="fa-solid fa-circle text-[6px] mr-1.5"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-medium">
                                            <i class="fa-solid fa-circle text-[6px] mr-1.5"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </span>
                                <span>
                                    @if ($slide->type === 'image')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-primary/10 text-primary font-medium">
                                            <i class="fa-solid fa-image text-[10px] mr-1"></i>
                                            Image
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-warning/10 text-warning font-medium">
                                            <i class="fa-solid fa-video text-[10px] mr-1"></i>
                                            Video
                                        </span>
                                    @endif
                                </span>
                                <span>
                                    @if ($slide->screen_size === 'small')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-blue-500/10 text-blue-500 font-medium">
                                            <i class="fa-solid fa-mobile-screen text-[10px] mr-1"></i>
                                            Small Screen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-purple-500/10 text-purple-500 font-medium">
                                            <i class="fa-solid fa-desktop text-[10px] mr-1"></i>
                                            Large Screen
                                        </span>
                                    @endif
                                </span>
                                <span><i class="fa-solid fa-calendar mr-1"></i> {{ $slide->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @can('edit slider')
                                <a href="{{ route('slider.edit', $slide) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-pen mr-1"></i> Edit
                                </a>
                            @endcan
                        </div>
                    </div>

                    @if($slide->alt_text)
                        <div class="mb-4">
                            <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alt Text / Description</h6>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $slide->alt_text }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Media Path</h6>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $slide->media_path }}</p>
                        </div>
                        <div>
                            <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Order</h6>
                            <p class="text-gray-600 dark:text-gray-400">{{ $slide->order }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('slider.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Slides
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slideShow', () => ({}));
        });
    </script>
</x-layouts.app>