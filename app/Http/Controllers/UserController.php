<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $roles = Role::where('id', '!=', 1)->get();
        $users = User::where('id', '!=', 1)->orderBy('is_active', 'desc')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'password_confirmation' => 'required',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            return Response::jsonResponse(true, 'User Created Successfully', [
                'redirect' => route('users.index'),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creating user: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            // Check if any changes were made
            $hasChanges = false;
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'is_active' => (bool) $request->is_active,
            ];

            foreach ($userData as $key => $value) {
                if ($value != $user->$key) {
                    $hasChanges = true;
                    break;
                }
            }

            // Check if role has changed
            $roleChanged = false;
            if ($user->roles->isNotEmpty() && $request->role) {
                if ($user->roles->first()->name !== $request->role) {
                    $roleChanged = true;
                    $hasChanges = true;
                }
            }

            // Prevent users from changing their own role
            if ($roleChanged && $user->id === auth()->user()->id) {
                return Response::jsonResponse(false, 'You cannot change your own role.', [], 403);
            }

            if (! $hasChanges) {
                return Response::jsonResponse(false, 'No changes were made to update', [], 422);
            }

            $user->update($userData);

            // Only sync roles if the user is not updating their own account or if roles haven't changed
            if (! ($user->id === auth()->user()->id && $roleChanged)) {
                $user->syncRoles($request->role);
            }

            return Response::jsonResponse(true, 'User Updated Successfully', [
                'redirect' => route('users.index'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating user: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    public function destroy(User $user)
    {

        try {
            if ($user->id === auth()->user()->id) {
                return Response::jsonResponse(false, 'Cannot delete the logged-in user.', [], 422);
            }

            // Check for related records
            $relatedRecords = $user->hasRelatedRecords();
            if (! empty($relatedRecords)) {
                $message = 'Cannot delete user. They have related records';

                return Response::jsonResponse(false, $message, ['related_records' => $relatedRecords], 422);
            }

            $user->delete();

            return Response::jsonResponse(true, 'User Deleted Successfully', [
                'redirect' => route('users.index'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting user: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}
