<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BusinessProfileController extends Controller
{
    public function index()
    {
        $businessProfile = BusinessProfile::first();

        return view('business-profile.index', compact('businessProfile'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Business name is required',
            'email.email' => 'Please enter a valid email address',
            'logo.image' => 'The logo must be an image',
            'logo.mimes' => 'The logo must be a jpeg, png, or jpg file',
            'logo.max' => 'The logo must not be larger than 2MB',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except('logo');

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoPath = $logo->store('business_logos', 'public');
                $data['logo'] = $logoPath;
            }

            $businessProfile = BusinessProfile::create($data);

            return Response::jsonResponse(true, 'Business Profile Created Successfully', [
                'business_profile' => $businessProfile,
                'redirect' => route('dashboard', $businessProfile->id),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error creating business profile: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $businessProfile = BusinessProfile::findOrFail($id);

    //         return view('business-profiles.show', compact('businessProfile'));
    //     } catch (\Throwable $e) {
    //         return redirect()->route('business-profile.index')
    //             ->with('error', 'Business profile not found.');
    //     }
    // }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Business name is required',
            'email.email' => 'Please enter a valid email address',
            'logo.image' => 'The logo must be an image',
            'logo.mimes' => 'The logo must be a jpeg, png, or jpg file',
            'logo.max' => 'The logo must not be larger than 2MB',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $businessProfile = BusinessProfile::findOrFail($id);

            // Check if any changes were made
            $hasChanges = false;
            $updateData = $request->except('logo');

            foreach ($updateData as $key => $value) {
                if ($value != $businessProfile->$key) {
                    $hasChanges = true;
                    break;
                }
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $hasChanges = true;
                if ($businessProfile->logo) {
                    Storage::disk('public')->delete($businessProfile->logo);
                }
                $logo = $request->file('logo');
                $logoPath = $logo->store('business_logos', 'public');
                $updateData['logo'] = $logoPath;
            }

            if (! $hasChanges) {
                return Response::jsonResponse(false, 'No changes were made to update', [], 422);
            }

            $businessProfile->update($updateData);

            return Response::jsonResponse(true, 'Business Profile Updated Successfully', [
                'business_profile' => $businessProfile,
                'redirect' => route('dashboard'),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error updating business profile: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $businessProfile = BusinessProfile::findOrFail($id);

            // Delete the logo file if it exists
            if ($businessProfile->logo) {
                Storage::disk('public')->delete($businessProfile->logo);
            }

            $businessProfile->delete();

            return Response::jsonResponse(true, 'Business Profile Deleted Successfully', [
                'redirect' => route('business-profile.index'),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error deleting business profile: '.$e->getMessage());

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}
