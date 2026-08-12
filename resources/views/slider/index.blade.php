<x-layouts.app>
    <div x-data="slideList">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline font-semibold">Slider</a>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="text-xl font-semibold dark:text-white-light">Slider Slides</h5>

                <div class="flex items-center gap-3">
                    {{-- Status Filter --}}
                    <div class="flex items-center gap-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Filter:</label>
                        <select id="status-filter" x-model="status" @change="filterSlides" class="form-select w-40">
                            <option value="all">All Slides</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    @can('create slider')
                        <a href="{{ route('slider.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Slide
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Slide List --}}
            @if ($slides->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-images text-gray-300 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Slides Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if ($status !== 'all')
                            No {{ $status }} slides available. Try changing the filter.
                        @else
                            Start by creating your first slide.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($slides as $slide)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-lg border border-gray-200 dark:border-gray-700">
                            {{-- Media Preview --}}
                            <div class="relative w-full aspect-video bg-gray-100 dark:bg-gray-700" style="height: 12rem;">
                                @if ($slide->type === 'image')
                                    @if ($slide->media_path)
                                        <img src="{{ asset('storage/' . $slide->media_path) }}" alt="{{ $slide->alt_text ?? 'Slide image' }}" class="w-full h-full object-cover" style="height: 12rem;">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center" style="height: 12rem;">
                                            <i class="fa-solid fa-image text-gray-300 dark:text-gray-600 text-5xl"></i>
                                        </div>
                                    @endif
                                @else
                                    @if ($slide->media_path)
                                        <video src="{{ asset('storage/' . $slide->media_path) }}" class="w-full h-full object-cover" style="height: 12rem;" muted></video>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center" style="height: 12rem;">
                                            <i class="fa-solid fa-video text-gray-300 dark:text-gray-600 text-5xl"></i>
                                        </div>
                                    @endif
                                @endif

                                {{-- Status Badge --}}
                                <div class="absolute top-2 right-2 z-10">
                                    @if ($slide->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-success text-white font-medium shadow-sm">
                                            <i class="fa-solid fa-circle text-[6px] mr-1.5"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-gray-500 text-white font-medium shadow-sm">
                                            <i class="fa-solid fa-circle text-[6px] mr-1.5"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </div>

                                {{-- Screen Size Badge --}}
                                <div class="absolute bottom-2 left-2 z-10">
                                    @if ($slide->screen_size === 'small')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-full bg-blue-500/80 text-white font-medium backdrop-blur-sm">
                                            <i class="fa-solid fa-mobile-screen text-xs mr-1.5"></i>
                                            Small Screen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-full bg-purple-500/80 text-white font-medium backdrop-blur-sm">
                                            <i class="fa-solid fa-desktop text-xs mr-1.5"></i>
                                            Large Screen
                                        </span>
                                    @endif
                                </div>

                                {{-- Type Badge --}}
                                <div class="absolute top-2 left-2 z-10">
                                    @if ($slide->type === 'image')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-primary/80 text-white font-medium backdrop-blur-sm">
                                            <i class="fa-solid fa-image text-[10px] mr-1"></i>
                                            Image
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-warning/80 text-white font-medium backdrop-blur-sm">
                                            <i class="fa-solid fa-video text-[10px] mr-1"></i>
                                            Video
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Slide Details --}}
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Slide #{{ $slide->order }}
                                    </h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $slide->formatted_date }}
                                    </span>
                                </div>

                                @if ($slide->alt_text)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                        {{ $slide->alt_text }}
                                    </p>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    @can('view slider')
                                        <a href="{{ route('slider.show', $slide) }}" class="flex-1 btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-eye mr-1"></i>
                                            View
                                        </a>
                                    @endcan

                                    @can('edit slider')
                                        <a href="{{ route('slider.edit', $slide) }}" class="btn btn-outline-info btn-sm" x-tooltip.raw="Edit Slide">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endcan

                                    @if ($slide->status === 'active')
                                        @can('deactivate slider')
                                            <button type="button" class="btn btn-outline-warning btn-sm" x-tooltip.raw="Deactivate" @click="deactivate('{{ route('slider.deactivate', $slide->id) }}')">
                                                <i class="fa-solid fa-toggle-on"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('activate slider')
                                            <button type="button" class="btn btn-outline-success btn-sm" x-tooltip.raw="Activate" @click="activate('{{ route('slider.activate', $slide->id) }}')">
                                                <i class="fa-solid fa-toggle-off"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    @can('delete slider')
                                        <button type="button" class="btn btn-outline-danger btn-sm" x-tooltip.raw="Delete" @click="destroy('{{ route('slider.destroy', $slide->id) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slideList', () => ({
                status: '{{ $status }}',

                filterSlides() {
                    window.location.href = '{{ route('slider.index') }}?status=' + this.status;
                },

                destroy(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This slide will be deleted permanently!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_method', 'DELETE');
                            formData.append('_token', '{{ csrf_token() }}');
                            submitFormData(url, formData);
                        }
                    });
                },

                activate(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This slide will be activated.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, activate it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_method', 'PATCH');
                            formData.append('_token', '{{ csrf_token() }}');
                            submitFormData(url, formData);
                        }
                    });
                },

                deactivate(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This slide will be deactivated.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, deactivate it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_method', 'PATCH');
                            formData.append('_token', '{{ csrf_token() }}');
                            submitFormData(url, formData);
                        }
                    });
                }
            }));
        });
    </script>
</x-layouts.app>