<?php

namespace Det\Members\Http\Controllers;

use Det\Members\Services\Contracts\MemberServiceInterface;
use Det\Members\Http\Requests\UpdateProfileRequest;
use Det\Members\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    protected $memberService;

    public function __construct(MemberServiceInterface $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Get Current Member Profile
     */
    public function show(Request $request)
    {
        // Return the currently logged-in user with profile
        return new MemberResource($request->user()->load('profile'));
    }

    /**
     * Update Profile Details
     */
    public function update(UpdateProfileRequest $request)
    {
        $member = $request->user();
        
        $this->memberService->updateProfile($member, $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => new MemberResource($member->refresh())
        ]);
    }

    /**
     * Upload Avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            // 'avatar' => 'required|image|max:2048|mimes:jpg,jpeg,png,gif',
            'avatar' => 'required|file|max:2048|mimes:jpg,jpeg,png,gif',
        ]);

        $path = $this->memberService->uploadAvatar(
            $request->user(),
            $request->file('avatar')
        );

        return response()->json([
            'message' => 'Avatar uploaded successfully.',
            'avatar_url' => $path 
        ]);
    }
}