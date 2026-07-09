<x-layouts.app>
    <div x-data="reviewList">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline font-semibold">Reviews</a>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="text-xl font-semibold dark:text-white-light">Customer Reviews</h5>
                
                {{-- Status Filter --}}
                <div class="flex items-center gap-2">
                    <label for="status-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Filter by Status:</label>
                    <select id="status-filter" x-model="status" @change="filterReviews" class="form-select w-40">
                        <option value="all">All Reviews</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Review List --}}
            @if($reviews->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-star text-gray-300 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Reviews Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($status !== 'all')
                            No {{ $status }} reviews available. Try changing the filter.
                        @else
                            There are no reviews yet.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Review Card --}}
                    @foreach ($reviews as $review)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md border border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                {{-- Header Section --}}
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xl font-bold flex-shrink-0">
                                            {{ strtoupper(substr($review->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3 min-w-0 flex-1">
                                            <div class="font-semibold text-base text-gray-900 dark:text-white truncate">{{ $review->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                    
                                    {{-- Action Buttons --}}
                                    <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                                        @if ($review->status === 'active')
                                            @can('deactivate review')
                                                <button type="button" 
                                                    class="p-2 rounded-lg text-success hover:bg-success/10 transition-all duration-200" 
                                                    x-tooltip.raw="Deactivate Review"
                                                    @click="deactivate('{{ route('reviews.deactivate', $review->id) }}')">
                                                    <i class="fa-solid fa-check text-lg"></i>
                                                </button>
                                            @endcan
                                        @else
                                            @can('activate review')
                                                <button type="button" 
                                                    class="p-2 rounded-lg text-warning hover:bg-warning/10 transition-all duration-200" 
                                                    x-tooltip.raw="Activate Review"
                                                    @click="activate('{{ route('reviews.activate', $review->id) }}')">
                                                    <i class="fa-solid fa-star text-lg"></i>
                                                </button>
                                            @endcan
                                        @endif
                                        
                                        @can('delete review')
                                            <button type="button" 
                                                class="p-2 rounded-lg text-danger hover:bg-danger/10 transition-all duration-200" 
                                                x-tooltip.raw="Delete Review"
                                                @click="destroy('{{ route('reviews.destroy', $review->id) }}')">
                                                <i class="fa-solid fa-trash text-lg"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="mb-3">
                                    @if ($review->status === 'active')
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
                                </div>

                                {{-- Review Message --}}
                                <div x-data="{ expanded: false }">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" :class="expanded ? '' : 'line-clamp-3'">
                                        {{ $review->message }}
                                    </p>
                                    @if (strlen($review->message) > 150)
                                        <button @click="expanded = !expanded" class="text-xs text-primary mt-2 hover:underline font-medium inline-flex items-center">
                                            <span x-text="expanded ? 'Show less' : 'Show more'"></span>
                                            <i class="fa-solid fa-chevron-down text-[10px] ml-1 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"></i>
                                        </button>
                                    @endif
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
