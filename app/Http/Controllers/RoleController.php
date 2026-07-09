<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all roles except Super Admin (id = 1)
        $roles = Role::where('id', '!=', 1)->with('permissions')->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groupedPermissions = config('custom.permissions');

        return view('roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|min:3',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($request->permissions);

            return Response::jsonResponse(true, 'Role Created Successfully', [
                'redirect' => route('roles.index'),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creating role: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Prevent editing Super Admin role
        if ($role->id === 1) {
            return redirect()->route('roles.index')->with('error', 'Cannot edit Super Admin role');
        }

        $groupedPermissions = config('custom.permissions');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing Super Admin role
        if ($role->id === 1) {
            return Response::jsonResponse(false, 'Cannot edit Super Admin role', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:roles,name,'.$role->id,
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            // Check if any changes were made
            $hasChanges = false;
            
            if ($role->name !== $request->name) {
                $hasChanges = true;
            }

            // Check if permissions have changed
            $currentPermissions = $role->permissions->pluck('name')->sort()->values()->toArray();
            $newPermissions = collect($request->permissions)->sort()->values()->toArray();
            
            if ($currentPermissions !== $newPermissions) {
                $hasChanges = true;
            }

            if (!$hasChanges) {
                return Response::jsonResponse(false, 'No changes were made to update', [], 422);
            }

            $role->update([
                'name' => $request->name,
            ]);

            $role->syncPermissions($request->permissions);

            return Response::jsonResponse(true, 'Role Updated Successfully', [
                'redirect' => route('roles.index'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating role: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting Super Admin role
        if ($role->id === 1) {
            return Response::jsonResponse(false, 'Cannot delete Super Admin role', [], 403);
        }

        try {
            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                return Response::jsonResponse(false, 'Cannot delete role. It is assigned to '.$role->users()->count().' user(s).', [], 422);
            }

            $role->delete();

            return Response::jsonResponse(true, 'Role Deleted Successfully', [
                'redirect' => route('roles.index'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting role: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}
