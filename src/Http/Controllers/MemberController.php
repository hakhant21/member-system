<?php

namespace Det\Members\Http\Controllers;

use Det\Members\Services\Contracts\MemberServiceInterface;
use Det\Members\Http\Requests\StoreMemberRequest;
use Det\Members\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    protected $memberService;

    // 1. Dependency Injection: We inject the Interface, not the concrete class.
    public function __construct(MemberServiceInterface $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * List Members (Search & Filter)
     */
    public function index(Request $request)
    {
        // 2. Delegate logic to Service
        $filters = $request->only(['search', 'role', 'is_active', 'sort_by', 'sort_dir']);
        $perPage = $request->input('per_page', 20);

        $members = $this->memberService->getAllMembers($filters, $perPage);

        // 3. Return formatted Resource
        return MemberResource::collection($members);
    }

    /**
     * Create New Member
     */
    public function store(StoreMemberRequest $request)
    {
        // 4. Validation is handled automatically by StoreMemberRequest before reaching here.
        
        // 5. Call Service to create data
        $member = $this->memberService->createMember($request->validated());

        return response()->json([
            'message' => 'Member created successfully.',
            'data' => new MemberResource($member),
        ], 201);
    }

    /**
     * Show Specific Member
     */
    public function show($id)
    {
        // We can add a getMemberById method to the service later, 
        // or use the Model directly for simple finds if permitted.
        $member = \Det\Members\Models\Member::with('profile', 'roles')->findOrFail($id);
        
        return new MemberResource($member);
    }

    public function update(Request $request, $id)
    {
        // Add validation here or create UpdateMemberRequest
        $data = $request->validate([
            'email' => 'email|unique:members,email,' . $id,
            'name' => 'string|max:255',
            'phone' => 'nullable|string'
        ]);

        $member = $this->memberService->updateMember($id, $data);

        return response()->json([
            'message' => 'Member updated successfully.',
            'data' => new MemberResource($member)
        ]);
    }

    /**
     * Delete Member (Soft Delete)
     */
    public function destroy($id)
    {
        $this->memberService->deleteMember($id);
        return response()->json(['message' => 'Member deleted successfully.']);
    }

    /**
     * Activate/Deactivate Member
     */
    public function toggleStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|boolean']);
        
        $member = $this->memberService->toggleStatus($id, $request->status);
        
        $status = $request->status ? 'activated' : 'deactivated';
        return response()->json(['message' => "Member {$status} successfully."]);
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        
        $this->memberService->assignRole($id, $request->role);
        
        return response()->json(['message' => "Role '{$request->role}' assigned successfully."]);
    }

    /**
     * Remove a Role from a Member
     */
    public function removeRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string']);
        
        $member = \Det\Members\Models\Member::findOrFail($id);
        
        if ($member->hasRole($request->role)) {
            $member->removeRole($request->role);
            return response()->json(['message' => "Role '{$request->role}' removed successfully."]);
        }
        
        return response()->json(['message' => "Member does not have this role."], 404);
    }
}