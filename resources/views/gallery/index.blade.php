<x-layouts.app>
    <div x-data="albumList">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline font-semibold">Gallery</a>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="text-xl font-semibold dark:text-white-light">Gallery Albums</h5>

                <div class="flex items-center gap-3">
                    {{-- Status Filter --}}
                    <div class="flex items-center gap-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Filter:</label>
                        <select id="status-filter" x-model="status" @change="filterAlbums" class="form-select w-40">
                            <option value="all">All Albums</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    @can('create gallery')
                        <a href="{{ route('gallery.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Album
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Album List --}}
            @if ($albums->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-images text-gray-300 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Albums Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if ($status !== 'all')
                            No {{ $status }} albums available. Try changing the filter.
                        @else
                            Start by creating your first album.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($albums as $album)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-lg border border-gray-200 dark:border-gray-700">
                            {{-- Cover Image --}}
                            <div class="relative w-full aspect-video bg-gray-100 dark:bg-gray-700" style="height: 12rem;">
                                @if ($album->cover_image)
                                    <img src="{{ asset('storage/' . $album->cover_image) }}" alt="{{ $album->title }}" class="w-full h-full object-contain" style="height: 12rem;">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="height: 12rem;">
                                        <i class="fa-solid fa-image text-gray-300 dark:text-gray-600 text-5xl"></i>
                                    </div>
                                @endif

                                {{-- Status Badge --}}
                                <div class="absolute top-2 right-2 z-10">
                                    @if ($album->status === 'active')
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

                                {{-- Image Count Badge --}}
                                <div class="absolute bottom-2 left-2 z-10">
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-full bg-black/70 text-white font-medium backdrop-blur-sm">
                                        <i class="fa-solid fa-images text-xs mr-1.5"></i>
                                        {{ $album->images->count() }} {{ Str::plural('image', $album->images->count()) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Album Details --}}
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-1">
                                    {{ $album->title }}
                                </h3>

                                @if ($album->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                        {{ $album->description }}
                                    </p>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    @can('view gallery')
                                        <a href="{{ route('gallery.show', $album) }}" class="flex-1 btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-eye mr-1"></i>
                                            View
                                        </a>
                                    @endcan

                                    @can('edit gallery')
                                        <a href="{{ route('gallery.edit', $album) }}" class="btn btn-outline-info btn-sm" x-tooltip.raw="Edit Album">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endcan

                                    @if ($album->status === 'active')
                                        @can('deactivate gallery')
                                            <button type="button" class="btn btn-outline-warning btn-sm" x-tooltip.raw="Deactivate" @click="deactivate('{{ route('gallery.deactivate', $album->id) }}')">
                                                <i class="fa-solid fa-toggle-on"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('activate gallery')
                                            <button type="button" class="btn btn-outline-success btn-sm" x-tooltip.raw="Activate" @click="activate('{{ route('gallery.activate', $album->id) }}')">
                                                <i class="fa-solid fa-toggle-off"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    @can('delete gallery')
                                        <button type="button" class="btn btn-outline-danger btn-sm" x-tooltip.raw="Delete" @click="destroy('{{ route('gallery.destroy', $album->id) }}')">
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
            Alpine.data('albumList', () => ({
                status: '{{ $status }}',

                filterAlbums() {
                    window.location.href = '{{ route('gallery.index') }}?status=' + this.status;
                },

                destroy(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This album and all its images will be deleted permanently!",
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
                        text: "This album will be activated.",
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
                        text: "This album will be deactivated.",
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
