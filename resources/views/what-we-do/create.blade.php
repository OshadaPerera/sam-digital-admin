<x-layouts.app>
    <div x-data="albumCreate">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="{{ route('what-we-do.index') }}" class="text-primary hover:underline">What We Do</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Create Album</span>
            </li>
        </ul>

        <div class="panel">
            <div class="mb-6">
                <h5 class="text-xl font-semibold dark:text-white-light">Create New Album</h5>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new album to What We Do section</p>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Album Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="title" name="title" x-model="formData.title" class="form-input" placeholder="Enter album title" required>
                        <template x-if="errors.title">
                            <p class="text-danger text-xs mt-1" x-text="errors.title[0]"></p>
                        </template>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" x-model="formData.description" rows="4" class="form-textarea" placeholder="Enter album description"></textarea>
                        <template x-if="errors.description">
                            <p class="text-danger text-xs mt-1" x-text="errors.description[0]"></p>
                        </template>
                    </div>

                    {{-- Cover Image --}}
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cover Image
                        </label>
                        <div class="relative">
                            <input type="file" id="cover_image" name="cover_image" @change="handleCoverImage" accept="image/*" class="form-input">
                            <template x-if="errors.cover_image">
                                <p class="text-danger text-xs mt-1" x-text="errors.cover_image[0]"></p>
                            </template>
                        </div>

                        {{-- Image Preview --}}
                        <template x-if="coverImagePreview">
                            <div class="mt-3">
                                <div class="relative inline-block">
                                    <img :src="coverImagePreview" alt="Cover preview" class="h-32 w-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                    <button type="button" @click="removeCoverImage" class="absolute -top-2 -right-2 bg-danger text-white rounded-full h-6 w-6 flex items-center justify-center hover:bg-danger/80">
                                        <i class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
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
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('what-we-do.index') }}" class="btn btn-outline-danger">
                        <i class="fa-solid fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <i class="fa-solid fa-save mr-2"></i>
                        <span x-text="submitting ? 'Creating...' : 'Create Album'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('albumCreate', () => ({
                formData: {
                    title: '',
                    description: '',
                    status: 'active'
                },
                coverImageFile: null,
                coverImagePreview: null,
                errors: {},
                submitting: false,

                handleCoverImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.coverImageFile = file;
                        this.coverImagePreview = URL.createObjectURL(file);
                        this.errors.cover_image = null;
                    }
                },

                removeCoverImage() {
                    this.coverImageFile = null;
                    this.coverImagePreview = null;
                    document.getElementById('cover_image').value = '';
                },

                submit() {
                    this.submitting = true;
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('title', this.formData.title);
                    formData.append('description', this.formData.description);
                    formData.append('status', this.formData.status);

                    if (this.coverImageFile) {
                        formData.append('cover_image', this.coverImageFile);
                    }

                    submitFormData('{{ route('what-we-do.store') }}', formData).finally(() => {
                        this.submitting = false;
                    });
                }
            }));
        });
    </script>
</x-layouts.app>
