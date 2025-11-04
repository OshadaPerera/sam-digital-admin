<x-layouts.app>
    <div x-data="albumShow">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="{{ route('gallery.index') }}" class="text-primary hover:underline">Gallery</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>{{ $album->title }}</span>
            </li>
        </ul>

        {{-- Album Details --}}
        <div class="panel mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                {{-- Cover Image --}}
                <div class="md:w-1/3">
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($album->cover_image)
                            <img src="{{ asset('storage/' . $album->cover_image) }}" 
                                 alt="{{ $album->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-image text-gray-300 dark:text-gray-600 text-6xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Album Info --}}
                <div class="md:w-2/3">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h5 class="text-2xl font-semibold dark:text-white-light mb-2">{{ $album->title }}</h5>
                            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                <span>
                                    @if ($album->status === 'active')
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
                                <span><i class="fa-solid fa-calendar mr-1"></i> {{ $album->created_at->format('M d, Y') }}</span>
                                <span><i class="fa-solid fa-images mr-1"></i> {{ $album->images->count() }} {{ Str::plural('image', $album->images->count()) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @can('edit gallery')
                                <a href="{{ route('gallery.edit', $album) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-pen mr-1"></i> Edit
                                </a>
                            @endcan
                        </div>
                    </div>

                    @if($album->description)
                        <div class="mb-4">
                            <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h6>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $album->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Album Images --}}
        <div class="panel">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="text-xl font-semibold dark:text-white-light">Album Images</h5>
                
                @can('add gallery images')
                    <button @click="openAddImagesModal" class="btn btn-primary">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Add Images
                    </button>
                @endcan
            </div>

            @if($album->images->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-images text-gray-300 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Images Yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Start by adding images to this album.
                    </p>
                    @can('add gallery images')
                        <button @click="openAddImagesModal" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Images
                        </button>
                    @endcan
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($album->images as $image)
                        <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="Album image" 
                                 class="w-full h-full object-cover">
                            
                            {{-- Hover Overlay --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2">
                                <button @click="viewImage('{{ asset('storage/' . $image->image_path) }}')" 
                                        class="btn btn-sm bg-white/20 hover:bg-white/30 text-white border-0">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @can('delete gallery images')
                                    <button @click="deleteImage('{{ route('gallery.delete-image', [$album, $image]) }}')" 
                                            class="btn btn-sm bg-danger/80 hover:bg-danger text-white border-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endcan
                            </div>

                            {{-- Order Badge --}}
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-black/70 text-white text-xs font-medium">
                                    {{ $image->order }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Add Images Modal --}}
        <div x-show="showAddImagesModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             @keydown.escape.window="showAddImagesModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                {{-- Backdrop --}}
                <div x-show="showAddImagesModal" 
                     @click="showAddImagesModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/50 transition-opacity"></div>

                {{-- Modal Content --}}
                <div x-show="showAddImagesModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    
                    <form @submit.prevent="submitImages">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Images to Album</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Images <span class="text-danger">*</span>
                                </label>
                                <input type="file" 
                                       @change="handleImages"
                                       accept="image/*"
                                       multiple
                                       class="form-input">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You can select multiple images at once</p>
                                <template x-if="imageErrors">
                                    <p class="text-danger text-xs mt-1" x-text="imageErrors"></p>
                                </template>
                            </div>

                            {{-- Image Previews --}}
                            <template x-if="imagePreviews.length > 0">
                                <div class="grid grid-cols-4 gap-3 mb-4">
                                    <template x-for="(preview, index) in imagePreviews" :key="index">
                                        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                            <img :src="preview" alt="Preview" class="w-full h-full object-cover">
                                            <button type="button" 
                                                    @click="removeImage(index)"
                                                    class="absolute -top-1 -right-1 bg-danger text-white rounded-full h-5 w-5 flex items-center justify-center hover:bg-danger/80">
                                                <i class="fa-solid fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex items-center justify-end gap-3">
                            <button type="button" 
                                    @click="closeAddImagesModal"
                                    class="btn btn-outline-danger">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="btn btn-primary"
                                    :disabled="submitting || imagePreviews.length === 0">
                                <span x-text="submitting ? 'Adding...' : 'Add Images'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- View Image Modal --}}
        <div x-show="showViewImageModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             @keydown.escape.window="showViewImageModal = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="showViewImageModal" 
                     @click="showViewImageModal = false"
                     class="fixed inset-0 bg-black/80 transition-opacity"></div>

                <div x-show="showViewImageModal" class="relative max-w-4xl w-full">
                    <button @click="showViewImageModal = false"
                            class="absolute -top-10 right-0 text-white hover:text-gray-300">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                    <img :src="viewingImage" alt="Full size" class="w-full rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('albumShow', () => ({
                showAddImagesModal: false,
                showViewImageModal: false,
                selectedImages: [],
                imagePreviews: [],
                imageErrors: null,
                submitting: false,
                viewingImage: null,

                openAddImagesModal() {
                    this.showAddImagesModal = true;
                },

                closeAddImagesModal() {
                    this.showAddImagesModal = false;
                    this.selectedImages = [];
                    this.imagePreviews = [];
                    this.imageErrors = null;
                },

                handleImages(event) {
                    const files = Array.from(event.target.files);
                    this.selectedImages = files;
                    this.imagePreviews = [];
                    
                    files.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreviews.push(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    });
                },

                removeImage(index) {
                    this.selectedImages.splice(index, 1);
                    this.imagePreviews.splice(index, 1);
                },

                submitImages() {
                    this.submitting = true;
                    this.imageErrors = null;

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    this.selectedImages.forEach((image, index) => {
                        formData.append(`images[${index}]`, image);
                    });

                    submitFormData('{{ route('gallery.add-images', $album) }}', formData).finally(() => {
                        this.submitting = false;
                    });
                },

                viewImage(url) {
                    this.viewingImage = url;
                    this.showViewImageModal = true;
                },

                deleteImage(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This image will be deleted permanently!",
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
                }
            }));
        });
    </script>
</x-layouts.app>
