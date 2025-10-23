<x-layouts.app>
    <!-- Breadcrumbs -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('business-profile.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Business Profile') }}</a>
        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Business Profile') }}</span> --}}
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Business Profile') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Update your business details and contact information') }}</p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">

            <!-- Main Content -->
            <div class="flex-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <form id="businessProfileForm" x-data="businessProfile" @submit.prevent="submitForm" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
                            @csrf
                            @if ($businessProfile)
                                @method('PUT')
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Business Name -->
                                <x-forms.input label="Business Name" name="name" type="text" value="{{ old('name', $businessProfile?->name) }}" required />

                                <!-- Contact Number -->
                                <x-forms.input label="Contact Number" name="phone" type="text" value="{{ old('phone', $businessProfile?->phone) }}" numberOnly required />

                                <!-- Business Email -->
                                <x-forms.input label="Business Email" name="email" type="email" value="{{ old('email', $businessProfile?->email) }}" required />

                                <!-- Website -->
                                <x-forms.input label="Website" name="website" type="url" value="{{ old('website', $businessProfile?->website) }}" />

                                <!-- Business Logo -->
                                <x-forms.input label="Business Logo" name="logo" type="file" />
                                @if ($businessProfile?->logo)
                                    <img src="{{ asset('storage/' . $businessProfile->logo) }}" alt="Current Logo" class="mt-3 max-w-[150px] rounded-md shadow-sm border border-gray-200">
                                @endif

                                <!-- Address -->
                                <x-forms.textarea label="Address" name="address" rows="3">
                                    {{ old('address', $businessProfile?->address) }}
                                </x-forms.textarea>
                            </div>

                            <!-- Submit Button -->
                            @can('update business profile')
                                <div class="flex justify-end mt-4">
                                    <x-button type="primary">{{ __('Save') }}</x-button>
                                </div>
                            @endcan
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("businessProfile", () => ({
                submitForm() {
                    const formData = new FormData(document.getElementById('businessProfileForm'));
                    const businessProfileId = '{{ $businessProfile?->id }}';
                    const url = businessProfileId ?
                        '{{ route('business-profile.update', ['id' => '__id__']) }}'.replace('__id__', businessProfileId) :
                        '{{ route('business-profile.store') }}';
                    submitFormData(url, formData);
                },
            }));
        });
    </script>
</x-layouts.app>
