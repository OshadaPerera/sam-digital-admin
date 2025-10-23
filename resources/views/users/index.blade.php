<x-layouts.app>
    <div x-data="userList">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Users</a>
            </li>
        </ul>

        <div class="panel mt-6">
            <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">User List</h5>
            @can('create user')
                {{-- Create user modal --}}
                <div x-data="createUserModal" class="mb-5">
                    <div class="flex items-center justify-end">
                        <button type="button" class="btn btn-primary" @click="toggle"><i class="fa-solid fa-plus "></i>Add User</button>
                    </div>
                    <template x-if="open">
                        {{-- @include('users.create', ['roles' => $roles]) --}}
                        @include('users.create')
                    </template>
                </div>
            @endcan
            <table id="userList" class="table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ ucwords($user->name) }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->first()->name ?? 'No Role Assigned' }}</td>
                            <td> <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span></td>
                            <td>
                                <div class="flex gap-4">
                                    @can('edit user')
                                        {{-- Edit user modal --}}
                                        <div x-data="editUserModal('{{ $user }}')">
                                            <div class="flex items-center justify-end">
                                                <button type="button" class="text-success" x-tooltip="Edit User" @click="toggle"><i class="fa-solid fa-pencil fa-lg"></i></button>
                                            </div>
                                            <template x-if="open">
                                                @include('users.edit')
                                            </template>
                                        </div>
                                    @endcan
                                    @can('delete user')
                                        <div x-data="deleteUserHandler">
                                            <button type="button" class="text-danger" x-tooltip="Delete User" @click="deleteUser('{{ route('users.destroy', ['user' => $user->id]) }}')">
                                                <i class="fa-solid fa-trash-can fa-lg"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data('userList', () => ({
                init() {
                    const tableOptions = {
                        sortable: true,
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        firstLast: true,
                        firstText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        lastText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        prevText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        nextText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        labels: {
                            perPage: '{select}',
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}',
                        },
                    };

                    const table = new simpleDatatables.DataTable('#userList', tableOptions);
                },
            }));

            // Create user modal
            Alpine.data("createUserModal", (initialOpenState = false) => ({
                open: initialOpenState,
                message: '',
                errors: {},

                toggle() {
                    this.open = !this.open;
                },

                submitForm() {
                    const formData = new FormData(document.getElementById('createUserForm'));
                    submitFormData("{{ route('users.store') }}", formData);
                },
            }));

            // Edit user modal
            Alpine.data("editUserModal", () => ({
                open: false,

                toggle() {
                    this.open = !this.open;
                },

                submitForm() {
                    const form = document.getElementById('editUserForm');
                    const url = form.getAttribute('action');

                    const formData = new FormData(form);
                    formData.append('_method', 'PUT');
                    submitFormData(url, formData);
                }
            }));

            // Password handling
            Alpine.data('passwordHandler', () => ({
                showPassword: false,
                showConfirmPassword: false,
                password: '',
                passwordConfirm: '',
                validations: {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false,
                    special: false,
                    match: false
                },

                togglePassword(field) {
                    if (field === 'password') {
                        this.showPassword = !this.showPassword;
                    } else {
                        this.showConfirmPassword = !this.showConfirmPassword;
                    }
                },

                validatePassword() {
                    const password = this.password;
                    this.validations = {
                        length: password.length >= 8,
                        uppercase: /[A-Z]/.test(password),
                        lowercase: /[a-z]/.test(password),
                        number: /\d/.test(password),
                        special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
                        match: this.password === this.passwordConfirm
                    };
                }
            }));

            // Delete user handler
            Alpine.data("deleteUserHandler", () => ({
                deleteUser(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
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
