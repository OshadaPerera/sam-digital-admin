<?php

namespace App\Http\Controllers;

use App\Models\SliderSlide;
use App\Services\SliderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    /**
     * Display a listing of slider slides
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $slides = $this->sliderService->getAllSlides($status);

        return view('slider.index', compact('slides', 'status'));
    }

    /**
     * Show the form for creating a new slide
     */
    public function create()
    {
        return view('slider.create');
    }

    /**
     * Store a newly created slide
     */
    public function store(Request $request)
    {
        // Check for file upload errors first
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            if ($file->getError() !== UPLOAD_ERR_OK) {
                Log::error('File upload error', [
                    'error_code' => $file->getError(),
                    'error_message' => $file->getErrorMessage(),
                    'file_name' => $file->getClientOriginalName(),
                ]);
                return Response::jsonResponse(false, 'The media file failed to upload. Error: ' . $file->getErrorMessage(), [], 422);
            }
        }

        // Build validation rules based on type
        $rules = [
            'type' => 'required|in:image,video',
            'alt_text' => 'nullable|string|max:255',
            'screen_size' => 'required|in:small,large',
            'status' => 'required|in:active,inactive',
        ];

        // Add media file validation based on type
        // PHP limits increased to 50MB via Herd configuration
        if ($request->type === 'image') {
            $rules['media_file'] = 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240'; // 10MB max
        } else {
            $rules['media_file'] = 'required|file|mimes:mp4,webm,ogg|max:25600'; // 25MB max
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Slider slide validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->except('media_file'),
                'has_file' => $request->hasFile('media_file'),
                'file_info' => $request->hasFile('media_file') ? [
                    'original_name' => $request->file('media_file')->getClientOriginalName(),
                    'mime_type' => $request->file('media_file')->getMimeType(),
                    'size' => $request->file('media_file')->getSize(),
                    'error' => $request->file('media_file')->getError(),
                ] : null,
            ]);

            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $slide = $this->sliderService->createSlide(
                $request->only(['type', 'alt_text', 'screen_size', 'status']),
                $request->file('media_file')
            );

            return Response::jsonResponse(true, 'Slide created successfully', [
                'redirect' => route('slider.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create slider slide', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('media_file'),
            ]);

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Display the specified slide
     */
    public function show(SliderSlide $slider)
    {
        return view('slider.show', ['slide' => $slider]);
    }

    /**
     * Show the form for editing the specified slide
     */
    public function edit(SliderSlide $slider)
    {
        return view('slider.edit', ['slide' => $slider]);
    }

    /**
     * Update the specified slide
     */
    public function update(Request $request, SliderSlide $slider)
    {
        // Check for file upload errors first
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            if ($file->getError() !== UPLOAD_ERR_OK) {
                Log::error('File upload error', [
                    'error_code' => $file->getError(),
                    'error_message' => $file->getErrorMessage(),
                    'file_name' => $file->getClientOriginalName(),
                ]);
                return Response::jsonResponse(false, 'The media file failed to upload. Error: ' . $file->getErrorMessage(), [], 422);
            }
        }

        // Build validation rules based on type
        $rules = [
            'type' => 'required|in:image,video',
            'alt_text' => 'nullable|string|max:255',
            'screen_size' => 'required|in:small,large',
            'status' => 'required|in:active,inactive',
        ];

        // Add media file validation based on type if file is provided
        // PHP limits increased to 50MB via Herd configuration
        if ($request->hasFile('media_file')) {
            if ($request->type === 'image') {
                $rules['media_file'] = 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240'; // 10MB max
            } else {
                $rules['media_file'] = 'required|file|mimes:mp4,webm,ogg|max:25600'; // 25MB max
            }
        } else {
            // If no file is provided, media_file is not required
            $rules['media_file'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Slider slide update validation failed', [
                'slide_id' => $slider->id,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->except('media_file'),
                'has_file' => $request->hasFile('media_file'),
                'file_info' => $request->hasFile('media_file') ? [
                    'original_name' => $request->file('media_file')->getClientOriginalName(),
                    'mime_type' => $request->file('media_file')->getMimeType(),
                    'size' => $request->file('media_file')->getSize(),
                    'error' => $request->file('media_file')->getError(),
                ] : null,
            ]);

            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        // Additional validation: if type is changing, a new file must be provided
        if ($slider->type !== $request->type && !$request->hasFile('media_file')) {
            Log::warning('Attempted to change media type without providing new file', [
                'slide_id' => $slider->id,
                'old_type' => $slider->type,
                'new_type' => $request->type,
            ]);

            return Response::jsonResponse(false, 'When changing media type, a new file must be uploaded.', [], 422);
        }

        try {
            $this->sliderService->updateSlide(
                $slider,
                $request->only(['type', 'alt_text', 'screen_size', 'status']),
                $request->file('media_file')
            );

            return Response::jsonResponse(true, 'Slide updated successfully', [
                'redirect' => route('slider.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update slider slide', [
                'slide_id' => $slider->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('media_file'),
            ]);

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Remove the specified slide
     */
    public function destroy(SliderSlide $slider)
    {
        try {
            $this->sliderService->deleteSlide($slider);

            return Response::jsonResponse(true, 'Slide deleted successfully', ['redirect' => route('slider.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Activate a slide
     */
    public function activate(SliderSlide $slider)
    {
        try {
            $this->sliderService->updateStatus($slider, 'active');

            return Response::jsonResponse(true, 'Slide activated successfully', ['redirect' => route('slider.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Deactivate a slide
     */
    public function deactivate(SliderSlide $slider)
    {
        try {
            $this->sliderService->updateStatus($slider, 'inactive');

            return Response::jsonResponse(true, 'Slide deactivated successfully', ['redirect' => route('slider.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Reorder slides
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slides' => 'required|array',
            'slides.*' => 'integer|exists:slider_slides,id',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $this->sliderService->reorderSlides($request->input('slides'));

            return Response::jsonResponse(true, 'Slides reordered successfully');
        } catch (\Exception $e) {
            Log::error('Failed to reorder slider slides', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}