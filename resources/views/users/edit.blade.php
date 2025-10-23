<div>
    <div class="fixed inset-0 bg-[black]/60 z-[999] hidden overflow-y-auto" :class="open && '!block'">
        <div class="flex items-start justify-center min-h-screen px-4" @click.self="open = false">
            <div x-show="open" x-transition x-transition.duration.300 class="panel border-0 p-0 rounded-lg overflow-hidden my-auto w-full max-w-lg">
                <div class="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                    <div class="font-bold text-lg">Edit User</div>
                    <button type="button" class="text-white-dark hover:text-dark" @click="toggle">
                         <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-5">
                    <form action="{{ route('users.update', $user) }}" method="POST" id="editUserForm" class="space-y-5">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label for="username">Username<span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-input" value="{{ old('username', $user->username) }}">
                            <span class="text-danger error-message username_err"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email<span class="text-danger">*</span></label>
                            <input type="text" name="email" class="form-input" value="{{ old('email', $user->email) }}">
                            <span class="text-danger error-message email_err"></span>
                        </div>
                        <div>
                            <label for="role">User Role<span class="text-danger">*</span></label>
                            
                            @if ($user->id === auth()->user()->id)
                                <select id="role" name="role" class="form-input" disabled>
                                    <option value="{{ optional($user->roles->first())->name }}">{{ optional($user->roles->first())->name }}</option>
                                </select>
                                <input type="hidden" name="role" value="{{ optional($user->roles->first())->name }}" />
                                <p class="text-danger text-sm mt-1">You cannot change your own role.</p>
                            @else
                                <select id="role" name="role" class="form-input">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}" {{ $role->name == optional($user->roles->first())->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <label id="active_status" class="ml-2">
                                Active
                            </label>
                            <label for="is_active_switch" class="w-12 h-6 relative">
                                <!-- Hidden input to ensure 0 is sent when checkbox is unchecked -->
                                <input type="hidden" name="is_active" value="0" />

                                <!-- Checkbox switch -->
                                <input type="checkbox" class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer" id="is_active_switch" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} />

                                <span class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full before:absolute before:left-1 before:bg-white dark:before:bg-white-dark dark:peer-checked:before:bg-white before:bottom-1 before:w-4 before:h-4 before:rounded-full peer-checked:before:left-7 peer-checked:bg-primary before:transition-all before:duration-300"></span>
                            </label>
                            <span class="text-danger error-message is_active_err"></span>
                        </div>

                        <div class="flex justify-end items-center mt-8">
                            <button type="button" class="btn btn-outline-danger" @click="toggle">Cancel</button>
                            <button type="button" class="btn btn-primary ltr:ml-4 rtl:mr-4" @click.prevent="submitForm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
