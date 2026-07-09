<x-layouts.app>
    <div x-data="videoEdit">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="{{ route('videos.index') }}" class="text-primary hover:underline">Videos</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Edit Video</span>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6">
                <h5 class="text-xl font-semibold dark:text-white-light">Edit Video</h5>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update video details</p>
            </div>

            <form @submit.prevent="submit">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Video URL --}}
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Video URL <span class="text-danger">*</span>
                        </label>
                        <input type="url" id="url" name="url" x-model="formData.url" class="form-input" placeholder="https://www.youtube.com/embed/..." required>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter the embed URL of the video (YouTube, Vimeo, etc.)</p>
                        <template x-if="errors.url">
                            <p class="text-danger text-xs mt-1" x-text="errors.url[0]"></p>
                        </template>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Video Title
                        </label>
                        <input type="text" id="title" name="title" x-model="formData.title" class="form-input" placeholder="Enter video title (optional)">
                        <template x-if="errors.title">
                            <p class="text-danger text-xs mt-1" x-text="errors.title[0]"></p>
                        </template>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status" x-model="formData.status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <template x-if="errors.status">
                            <p class="text-danger text-xs mt-1" x-text="errors.status[0]"></p>
                        </template>
                    </div>

                    {{-- Video Preview --}}
                    <div x-show="formData.url" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Video Preview
                        </label>
                        <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <iframe 
                                :src="formData.url" 
                                class="w-full h-full"
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('videos.index') }}" class="btn btn-outline-danger">
                        <i class="fa-solid fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <i class="fa-solid fa-save mr-2"></i>
                        <span x-text="submitting ? 'Updating...' : 'Update Video'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('videoEdit', () => ({
                formData: {
                    title: '{{ $video->title }}',
                    url: '{{ $video->url }}',
                    status: '{{ $video->status }}'
                },
                errors: {},
                submitting: false,

                submit() {
                    this.submitting = true;
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('title', this.formData.title);
                    formData.append('url', this.formData.url);
                    formData.append('status', this.formData.status);

                    submitFormData('{{ route('videos.update', $video) }}', formData).finally(() => {
                        this.submitting = false;
                    });
                }
            }));
        });
    </script>
</x-layouts.app>