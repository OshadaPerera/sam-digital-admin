<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    /**
     * Display the business profile.
     */
    public function index()
    {
        try {
            $businessProfile = BusinessProfile::first();

            if (!$businessProfile) {
                return Response::jsonResponse(false, 'Business profile not found', [
                    'error' => 'No business profile has been configured',
                ], 404);
            }

            // Add storage URL for logo if exists
            $profileData = $businessProfile->toArray();
            if ($businessProfile->logo) {
                $profileData['logo_url'] = Storage::url($businessProfile->logo);
            } else {
                $profileData['logo_url'] = null;
            }

            // Include social media links in response
            $profileData['social_links'] = [
                'facebook' => $businessProfile->facebook_url,
                'youtube' => $businessProfile->youtube_url,
                'instagram' => $businessProfile->instagram_url,
                'whatsapp' => $businessProfile->whatsapp_number ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $businessProfile->whatsapp_number) : null,
            ];

            return Response::jsonResponse(true, 'Business profile retrieved successfully', [
                'business_profile' => $profileData,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving business profile: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve business profile', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
