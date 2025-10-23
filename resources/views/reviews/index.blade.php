<x-layouts.app>
    <div x-data="reviewList">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Reviews</a>
            </li>
        </ul>

        <div class="panel mt-6">
            <h5 class="mb-5 text-lg font-semibold dark:text-white-light">Reviews</h5>

            {{-- Status Filter --}}
            <div class="mb-4">
                <label for="status-filter" class="block text-sm font-medium text-gray-700">Filter</label>
                <select id="status-filter" x-model="status" @change="filterReviews" class="form-select w-1/2">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            {{-- review list --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- review card --}}
                @foreach ($reviews as $review)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-all hover:shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary text-lg font-bold">
                                        {{ strtoupper(substr($review->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-semibold text-base">{{ $review->name }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($review->created_at)->format('d M, Y') }}</div>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    @can('delete review')
                                        <button type="button" class="text-danger hover:text-red-600 transition-colors" x-tooltip='Delete Review' @click="destroy('{{ route('reviews.destroy', $review->id) }}')">
                                            <i class="fa-solid fa-trash fa-lg"></i>
                                        </button>
                                    @endcan
                                    @if ($review->status === 'active')
                                        @can('deactivate review')
                                            <button type="button" class="text-success hover:text-green-600 transition-colors" x-tooltip='Deactivate Review' @click="deactivate('{{ route('reviews.deactivate', $review->id) }}')">
                                                <i class="fa-solid fa-check fa-lg"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('activate review')
                                            <button type="button" class="text-warning hover:text-yellow-500 transition-colors" x-tooltip='Activate Review' @click="activate('{{ route('reviews.activate', $review->id) }}')">
                                                <i class="fa-solid fa-star"></i>
                                            </button>
                                        @endcan
                                    @endif

                                </div>
                            </div>

                            <div class="flex items-center mb-2">
                                {{-- @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= ($review->rating ?? 5))
                                        <i class="fa-solid fa-star text-yellow-400"></i>
                                    @else
                                        <i class="fa-regular fa-star text-yellow-400"></i>
                                    @endif
                                @endfor --}}
                                @if ($review->status === 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-success/10 text-success font-medium">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-danger/20 text-gray-600 dark:text-gray-400 font-medium">Inactive</span>
                                @endif
                            </div>

                            <div x-data="{ expanded: false }">
                                <p class="text-sm text-gray-600 dark:text-gray-400" :class="expanded ? '' : 'line-clamp-2'">
                                    {{ $review->message }}
                                </p>
                                @if (strlen($review->message) > 120)
                                    <button @click="expanded = !expanded" class="text-xs text-primary mt-1 hover:underline">
                                        <span x-text="expanded ? 'Read less' : 'Read more'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('reviewList', () => ({
                status: '{{ $status }}', // Initialize the status filter

                filterReviews() {
                    // Redirect to the same page with the selected status filter
                    window.location.href = '{{ route('reviews.index') }}?status=' + this.status;
                },

                destroy(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This review will be deleted permanently!",
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
                        text: "This review will be activated.",
                        icon: 'warning',
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
                        text: "This review will be deactivated.",
                        icon: 'warning',
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
