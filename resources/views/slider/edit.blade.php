<x-layouts.app>
    <div x-data="slideEdit">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="{{ route('slider.index') }}" class="text-primary hover:underline">Slider</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <a href="{{ route('slider.show', $slide) }}" class="text-primary hover:underline">Slide #{{ $slide->order }}</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Edit</span>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6">
                <h5 class="text-xl font-semibold dark:text-white-light">Edit Slide</h5>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update slide details</p>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Media Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Media Type <span class="text-danger">*</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="type" value="image" x-model="formData.type" @change="handleTypeChange" class="form-radio" required>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Image</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="video" x-model="formData.type" @change="handleTypeChange" class="form-radio" required>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Video</span>
                            </label>
                        </div>
                        <template x-if="errors.type">
                            <p class="text-danger text-xs mt-1" x-text="errors.type[0]"></p>
                        </template>
                    </div>

                    {{-- Media File --}}
                    <div>
                        <label for="media_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span x-text="formData.type === 'image' ? 'Image File' : 'Video File'"></span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            Maximum file size: <span x-text="formData.type === 'image' ? '10MB' : '25MB'"></span>
                            <template x-if="formData.type === 'image'">
                                <span class="text-green-600 ml-2">(Images will be compressed)</span>
                            </template>
                        </p>

                        {{-- Current Media --}}
                        @if($slide->media_path)
                            <div class="mb-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current Media:</p>
                                <div class="relative inline-block" x-show="!mediaPreview">
                                    @if($slide->type === 'image')
                                        <img src="{{ asset('storage/' . $slide->media_path) }}" 
                                             alt="{{ $slide->alt_text ?? 'Current slide' }}" 
                                             class="h-32 w-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                    @else
                                        <video src="{{ asset('storage/' . $slide->media_path) }}" 
                                               class="h-32 w-auto rounded-lg border border-gray-300 dark:border-gray-600" 
                                               muted></video>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="relative">
                            <input type="file" 
                                   id="media_file" 
                                   name="media_file"
                                   @change="handleMediaFile"
                                   :accept="formData.type === 'image' ? 'image/*' : 'video/*'"
                                   class="form-input">
                            <template x-if="errors.media_file">
                                <p class="text-danger text-xs mt-1" x-text="errors.media_file[0]"></p>
                            </template>
                        </div>
                        
                        {{-- File Info --}}
                        <template x-if="mediaFile">
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span x-text="mediaFile.name"></span> 
                                <span class="text-gray-400">(<span x-text="(mediaFile.size / 1024 / 1024).toFixed(2)"></span> MB)</span>
                            </div>
                        </template>

                        {{-- New Media Preview --}}
                        <template x-if="mediaPreview">
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">New Media:</p>
                                <div class="relative inline-block">
                                    <template x-if="formData.type === 'image'">
                                        <img :src="mediaPreview" alt="New media preview" class="h-32 w-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                    </template>
                                    <template x-if="formData.type === 'video'">
                                        <video :src="mediaPreview" class="h-32 w-auto rounded-lg border border-gray-300 dark:border-gray-600" muted></video>
                                    </template>
                                    <button type="button" 
                                            @click="removeMedia"
                                            class="absolute -top-2 -right-2 bg-danger text-white rounded-full h-6 w-6 flex items-center justify-center hover:bg-danger/80">
                                        <i class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Alt Text --}}
                    <div>
                        <label for="alt_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alt Text / Description
                        </label>
                        <input type="text" 
                               id="alt_text" 
                               name="alt_text"
                               x-model="formData.alt_text"
                               class="form-input"
                               placeholder="Enter alt text for accessibility">
                        <template x-if="errors.alt_text">
                            <p class="text-danger text-xs mt-1" x-text="errors.alt_text[0]"></p>
                        </template>
                    </div>

                    {{-- Screen Size --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Screen Size <span class="text-danger">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <input type="radio" name="screen_size" value="small" x-model="formData.screen_size" class="form-radio" required>
                                <div class="ml-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">For smaller screens (≤768px)</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This slide will be displayed on mobile devices and tablets</p>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <input type="radio" name="screen_size" value="large" x-model="formData.screen_size" class="form-radio" required>
                                <div class="ml-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">For larger screens (>768px)</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This slide will be displayed on desktop computers</p>
                                </div>
                            </label>
                        </div>
                        <template x-if="errors.screen_size">
                            <p class="text-danger text-xs mt-1" x-text="errors.screen_size[0]"></p>
                        </template>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select id="status" 
                                name="status"
                                x-model="formData.status"
                                class="form-select"
                                required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <template x-if="errors.status">
                            <p class="text-danger text-xs mt-1" x-text="errors.status[0]"></p>
                        </template>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('slider.show', $slide) }}" class="btn btn-outline-danger">
                        <i class="fa-solid fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="btn btn-primary"
                            :disabled="submitting">
                        <i class="fa-solid fa-save mr-2"></i>
                        <span x-text="submitting ? 'Updating...' : 'Update Slide'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slideEdit', () => ({
                formData: {
                    type: '{{ $slide->type }}',
                    alt_text: '{{ $slide->alt_text ?? '' }}',
                    screen_size: '{{ $slide->screen_size }}',
                    status: '{{ $slide->status }}'
                },
                typeBeforeChange: '{{ $slide->type }}',
                mediaFile: null,
                mediaPreview: null,
                errors: {},
                submitting: false,

                handleTypeChange() {
                    const oldType = this.typeBeforeChange || this.formData.type;
                    this.typeBeforeChange = this.formData.type;

                    // If type is actually changing, clear media file and show warning
                    if (oldType !== this.formData.type) {
                        this.removeMedia();
                        this.errors.type = null;
                    }
                },

                handleMediaFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file size based on type
                        // PHP limits increased to 50MB via Herd configuration
                        const maxSize = this.formData.type === 'image' ? 10 * 1024 * 1024 : 25 * 1024 * 1024; // 10MB for images, 25MB for videos
                        const maxSizeMB = this.formData.type === 'image' ? '10MB' : '25MB';

                        if (file.size > maxSize) {
                            this.errors.media_file = [`File size exceeds ${maxSizeMB} limit. Your file is ${(file.size / 1024 / 1024).toFixed(2)}MB`];
                            this.mediaFile = null;
                            this.mediaPreview = null;
                            event.target.value = '';
                            return;
                        }

                        // Validate file type matches selected media type
                        if (this.formData.type === 'image' && !file.type.startsWith('image/')) {
                            this.errors.media_file = ['Please select a valid image file'];
                            this.mediaFile = null;
                            this.mediaPreview = null;
                            event.target.value = '';
                            return;
                        }

                        if (this.formData.type === 'video' && !file.type.startsWith('video/')) {
                            this.errors.media_file = ['Please select a valid video file'];
                            this.mediaFile = null;
                            this.mediaPreview = null;
                            event.target.value = '';
                            return;
                        }

                        this.mediaFile = file;
                        this.mediaPreview = URL.createObjectURL(file);
                        this.errors.media_file = null;
                    }
                },

                removeMedia() {
                    this.mediaFile = null;
                    this.mediaPreview = null;
                    document.getElementById('media_file').value = '';
                },

                submit() {
                    this.submitting = true;
                    this.errors = {};

                    // Validate that if type changed, a new file is provided
                    if (this.typeBeforeChange !== this.formData.type && !this.mediaFile) {
                        this.errors.media_file = ['When changing media type, a new file must be uploaded.'];
                        this.submitting = false;
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('type', this.formData.type);
                    formData.append('alt_text', this.formData.alt_text);
                    formData.append('screen_size', this.formData.screen_size);
                    formData.append('status', this.formData.status);

                    if (this.mediaFile) {
                        formData.append('media_file', this.mediaFile);
                    }

                    submitFormData('{{ route('slider.update', $slide) }}', formData).finally(() => {
                        this.submitting = false;
                    });
                }
            }));
        });
    </script>
</x-layouts.app>