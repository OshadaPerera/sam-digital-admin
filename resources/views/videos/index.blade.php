<x-layouts.app>
    <div x-data="videoList">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline font-semibold">Videos</a>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="text-xl font-semibold dark:text-white-light">Video Management</h5>

                <div class="flex items-center gap-3">
                    {{-- Status Filter --}}
                    <div class="flex items-center gap-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Filter:</label>
                        <select id="status-filter" x-model="status" @change="filterVideos" class="form-select w-40">
                            <option value="all">All Videos</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    @can('create videos')
                        <a href="{{ route('videos.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Video
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Video List --}}
            @if ($videos->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-video text-gray-300 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Videos Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if ($status !== 'all')
                            No {{ $status }} videos available. Try changing the filter.
                        @else
                            Start by adding your first video.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($videos as $video)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-lg border border-gray-200 dark:border-gray-700">
                            {{-- Video Player --}}
                            <div class="relative w-full aspect-video bg-gray-100 dark:bg-gray-700">
                                @if ($video->url)
                                    <iframe width="560" height="315" src="{{ $video->url }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fa-solid fa-video text-gray-300 dark:text-gray-600 text-5xl"></i>
                                    </div>
                                @endif

                                {{-- Status Badge --}}
                                <div class="absolute top-2 right-2 z-10">
                                    @if ($video->status === 'active')
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
                            </div>

                            {{-- Video Details --}}
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-1">
                                    {{ $video->title ?: 'Untitled Video' }}
                                </h3>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-1 break-all">
                                    {{ $video->url }}
                                </p>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    @can('edit videos')
                                        <a href="{{ route('videos.edit', $video) }}" class="flex-1 btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-pen mr-1"></i>
                                            Edit
                                        </a>
                                    @endcan

                                    @if ($video->status === 'active')
                                        @can('deactivate videos')
                                            <button type="button" class="btn btn-outline-warning btn-sm" x-tooltip.raw="Deactivate" @click="deactivate('{{ route('videos.deactivate', $video->id) }}')">
                                                <i class="fa-solid fa-toggle-on"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('activate videos')
                                            <button type="button" class="btn btn-outline-success btn-sm" x-tooltip.raw="Activate" @click="activate('{{ route('videos.activate', $video->id) }}')">
                                                <i class="fa-solid fa-toggle-off"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    @can('delete videos')
                                        <button type="button" class="btn btn-outline-danger btn-sm" x-tooltip.raw="Delete" @click="destroy('{{ route('videos.destroy', $video->id) }}')">
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
            Alpine.data('videoList', () => ({
                status: '{{ $status }}',

                filterVideos() {
                    window.location.href = '{{ route('videos.index') }}?status=' + this.status;
                },

                destroy(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This video will be deleted permanently!",
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
                        text: "This video will be activated.",
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
                        text: "This video will be deactivated.",
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
