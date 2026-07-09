<x-layouts.app>
    <div x-data="roleList">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Role Management</a>
            </li>
        </ul>

        <div class="panel mt-6">
            <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">Role List</h5>
            @can('create role')
                <div class="mb-5">
                    <div class="flex items-center justify-end">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Add Role
                        </a>
                    </div>
                </div>
            @endcan
            <table id="roleList" class="table-hover">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Permissions Count</th>
                        <th>Users Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ ucwords($role->name) }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $role->permissions->count() }} Permissions</span>
                            </td>
                            <td>{{ $role->users->count() }} Users</td>
                            <td>
                                <div class="flex gap-4">
                                    @can('edit role')
                                        <a href="{{ route('roles.edit', $role) }}" class="text-success" x-tooltip="Edit Role">
                                            <i class="fa-solid fa-pencil fa-lg"></i>
                                        </a>
                                    @endcan
                                    @can('delete role')
                                        <div x-data="deleteRoleHandler">
                                            <button type="button" class="text-danger" x-tooltip="Delete Role" @click="deleteRole('{{ route('roles.destroy', ['role' => $role->id]) }}')">
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
            Alpine.data('roleList', () => ({
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

                    const table = new simpleDatatables.DataTable('#roleList', tableOptions);
                },
            }));

            // Delete role handler
            Alpine.data("deleteRoleHandler", () => ({
                deleteRole(url) {
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
