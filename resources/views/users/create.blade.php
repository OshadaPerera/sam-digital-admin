<div>
    <div class="fixed inset-0 bg-[black]/60 z-[999] hidden overflow-y-auto" :class="open && '!block'">
        <div class="flex items-start justify-center min-h-screen px-4" @click.self="open = false">
            <div x-show="open" x-transition x-transition.duration.300 class="panel border-0 p-0 rounded-lg overflow-hidden my-auto w-full max-w-lg">
                <div class="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                    <div class="font-bold text-lg">Add User</div>
                    <button type="button" class="text-white-dark hover:text-dark" @click="toggle">
                         <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-5">
                    <form action="{{ route('users.store') }}" method="POST" id="createUserForm" class="space-y-5">
                        @csrf
                        <div>
                            <label for="username">Username<span class="text-danger">*</span></label>
                            <input id="username" type="text" name="username" placeholder="Enter username" class="form-input" />
                            <span class="text-danger error-message username_err"></span>
                        </div>
                        <div>
                            <label for="email">Email<span class="text-danger">*</span></label>
                            <input id="email" type="text" name="email" placeholder="Enter email" class="form-input" />
                            <span class="text-danger error-message email_err"></span>
                        </div>
                        <div x-data="passwordHandler">
                            <div>
                                <label for="password">Password<span class="text-danger">*</span></label>
                                <div class="relative">
                                    <input id="password" 
                                        :type="showPassword ? 'text' : 'password'" 
                                        name="password" 
                                        x-model="password"
                                        @input="validatePassword"
                                        placeholder="Enter password" 
                                        class="form-input pr-10" />
                                    <button type="button" 
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" 
                                        @click="togglePassword('password')">
                                        <i class="far" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <span class="text-danger error-message password_err"></span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Password must be at least 8 characters long and contain:
                                    <ul class="list-disc mx-4">
                                        <li :class="validations.length ? 'text-green-500' : 'text-red-500'">Minimum 8 characters</li>
                                        <li :class="validations.uppercase ? 'text-green-500' : 'text-red-500'">One uppercase letter</li>
                                        <li :class="validations.lowercase ? 'text-green-500' : 'text-red-500'">One lowercase letter</li>
                                        <li :class="validations.number ? 'text-green-500' : 'text-red-500'">One number</li>
                                        <li :class="validations.special ? 'text-green-500' : 'text-red-500'">One special character</li>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation">Confirm Password<span class="text-danger">*</span></label>
                                <div class="relative">
                                    <input id="password_confirmation" 
                                        :type="showConfirmPassword ? 'text' : 'password'" 
                                        name="password_confirmation" 
                                        x-model="passwordConfirm"
                                        @input="validatePassword"
                                        placeholder="Confirm password" 
                                        class="form-input pr-10" />
                                    <button type="button" 
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" 
                                        @click="togglePassword('confirm')">
                                        <i class="far" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <span class="text-danger error-message password_confirmation_err" 
                                    x-show="passwordConfirm && !validations.match"
                                    x-text="'Passwords do not match'"></span>
                            </div>
                        </div>
                        <div>
                            <label for="role">User Role<span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-input">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end items-center mt-8">
                            <button type="button" class="btn btn-outline-danger" @click="toggle">Cancel</button>
                            <button type="button" class="btn btn-primary ltr:ml-4 rtl:mr-4" @click.prevent="submitForm">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
