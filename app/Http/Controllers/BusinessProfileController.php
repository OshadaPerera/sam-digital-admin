<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;

class BusinessProfileController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Validation and storage logic here
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|string|max:255',
        ]);

        $businessProfile = BusinessProfile::create($request->all());
    }

    public function show($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }
}
