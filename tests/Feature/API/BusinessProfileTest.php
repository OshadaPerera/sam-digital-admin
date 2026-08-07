<?php

use App\Models\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('business profile api returns 404 when no profile exists', function () {
    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Business profile not found',
        ]);
});

test('business profile api returns profile data when exists', function () {
    $businessProfile = BusinessProfile::factory()->create([
        'name' => 'Test Business',
        'email' => 'test@example.com',
        'phone' => '+1234567890',
        'address' => '123 Test Street',
    ]);

    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Business profile retrieved successfully',
        ])
        ->assertJsonPath('data.business_profile.name', 'Test Business')
        ->assertJsonPath('data.business_profile.email', 'test@example.com')
        ->assertJsonPath('data.business_profile.phone', '+1234567890')
        ->assertJsonPath('data.business_profile.address', '123 Test Street');
});

test('business profile api includes logo_url when logo exists', function () {
    // Mock storage URL
    Storage::fake('public');

    $businessProfile = BusinessProfile::factory()->create([
        'name' => 'Test Business',
        'logo' => 'business_logos/test-logo.png',
    ]);

    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.business_profile.logo', 'business_logos/test-logo.png')
        ->assertJsonPath('data.business_profile.logo_url', Storage::url('business_logos/test-logo.png'));
});

test('business profile api includes social links', function () {
    $businessProfile = BusinessProfile::factory()->create([
        'name' => 'Test Business',
        'facebook_url' => 'https://facebook.com/test',
        'youtube_url' => 'https://youtube.com/test',
        'instagram_url' => 'https://instagram.com/test',
        'whatsapp_number' => '+1234567890',
    ]);

    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.business_profile.social_links.facebook', 'https://facebook.com/test')
        ->assertJsonPath('data.business_profile.social_links.youtube', 'https://youtube.com/test')
        ->assertJsonPath('data.business_profile.social_links.instagram', 'https://instagram.com/test')
        ->assertJsonPath('data.business_profile.social_links.whatsapp', 'https://wa.me/1234567890');
});

test('business profile api formats whatsapp number correctly', function () {
    $businessProfile = BusinessProfile::factory()->create([
        'name' => 'Test Business',
        'whatsapp_number' => '+44 7414 549888',
    ]);

    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.business_profile.social_links.whatsapp', 'https://wa.me/447414549888');
});

test('business profile api returns null for missing social links', function () {
    $businessProfile = BusinessProfile::factory()->create([
        'name' => 'Test Business',
    ]);

    $response = $this->getJson('/api/business-profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.business_profile.social_links.facebook', null)
        ->assertJsonPath('data.business_profile.social_links.youtube', null)
        ->assertJsonPath('data.business_profile.social_links.instagram', null)
        ->assertJsonPath('data.business_profile.social_links.whatsapp', null);
});
