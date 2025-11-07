<x-layouts.app>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="{{ route('roles.index') }}" class="text-primary hover:underline">Role Management</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Create Role</span>
            </li>
        </ul>

        <div class="panel mt-6">
            <div class="flex items-center justify-between mb-5">
                <h5 class="text-lg font-semibold dark:text-white-light">Create New Role</h5>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-danger">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </a>
            </div>

            <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm" x-data="roleForm">
                @csrf

                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="role_name" class="font-semibold">Role Name<span class="text-danger">*</span></label>
                            <input id="role_name" type="text" name="name" placeholder="Enter role name" class="form-input" value="{{ old('name') }}" />
                            <span class="text-danger error-message name_err"></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                        <label class="font-semibold text-lg mb-4 block">Assign Permissions<span class="text-danger">*</span></label>
                        <span class="text-danger error-message permissions_err block mb-3"></span>

                        @php
                            $groupedPermissions = config('custom.permissions');
                        @endphp

                        <div class="space-y-4">
                            @foreach ($groupedPermissions as $group => $permissions)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-800">
                                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                                        <h6 class="font-semibold text-base text-primary">{{ $group }}</h6>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" class="form-checkbox text-primary group-select-all" data-group="{{ Str::slug($group) }}" @change="toggleGroup('{{ Str::slug($group) }}')" />
                                            <span class="ml-2 text-sm font-medium">Select All</span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach ($permissions as $permission)
                                            <label class="flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-3 rounded transition-colors">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="form-checkbox text-primary permission-checkbox" data-group="{{ Str::slug($group) }}" @change="updateGroupState('{{ Str::slug($group) }}')" />
                                                <span class="ml-2 text-sm">{{ ucwords($permission) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end items-center gap-4 mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-danger">Cancel</a>
                        <button type="submit" @click.prevent="submitForm" class="btn btn-primary">
                            <i class="fa-solid fa-check mr-2"></i> Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('roleForm', () => ({
                init() {
                    this.updateAllGroupStates();
                },

                toggleGroup(group) {
                    const selectAllCheckbox = document.querySelector(`.group-select-all[data-group="${group}"]`);
                    const isChecked = selectAllCheckbox.checked;
                    const checkboxes = document.querySelectorAll(`input.permission-checkbox[data-group="${group}"]`);

                    checkboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                },

                updateGroupState(group) {
                    const checkboxes = document.querySelectorAll(`input.permission-checkbox[data-group="${group}"]`);
                    const selectAllCheckbox = document.querySelector(`.group-select-all[data-group="${group}"]`);

                    const total = checkboxes.length;
                    const checked = Array.from(checkboxes).filter(cb => cb.checked).length;

                    selectAllCheckbox.checked = checked === total;
                    selectAllCheckbox.indeterminate = checked > 0 && checked < total;
                },

                updateAllGroupStates() {
                    const groups = document.querySelectorAll('.group-select-all');
                    groups.forEach(selectAll => {
                        const group = selectAll.dataset.group;
                        this.updateGroupState(group);
                    });
                },

                submitForm() {
                    const formData = new FormData(document.getElementById('createRoleForm'));
                    submitFormData('{{ route('roles.store') }}', formData);
                }
            }));
        });
    </script>
</x-layouts.app>
