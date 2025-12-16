<?php

namespace DET\Members\Services;

use DET\Members\Models\Member;
use DET\Members\Services\Contracts\MemberServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberService implements MemberServiceInterface
{
    public function getAllMembers(array $filters, int $perPage = 20)
    {
        return Member::with(['profile', 'roles'])
            ->filter($filters)
            ->paginate($perPage);
    }

    public function createMember(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Member
            $member = Member::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            // 2. Create Profile
            if (isset($data['profile'])) {
                $member->profile()->create($data['profile']);
            } else {
                // Create empty profile to ensure relationship exists
                $member->profile()->create([]);
            }

            // 3. Assign Default Role
            $member->assignRole('member');

            return $member;
        });
    }

    public function updateMember(int $id, array $data)
    {
        $member = Member::findOrFail($id);

        DB::transaction(function () use ($member, $data) {
            $member->update($data);

            if (isset($data['profile'])) {
                $member->profile()->updateOrCreate(
                    ['member_id' => $member->id],
                    $data['profile']
                );
            }
        });

        return $member->refresh();
    }

    public function updateProfile($member, array $data)
    {
        // Decode JSON fields if they are passed as strings
        if (isset($data['settings']) && is_string($data['settings'])) {
            $data['settings'] = json_decode($data['settings'], true);
        }
        if (isset($data['preferences']) && is_string($data['preferences'])) {
            $data['preferences'] = json_decode($data['preferences'], true);
        }

        // Update or Create the profile
        $member->profile()->updateOrCreate(
            ['member_id' => $member->id],
            $data
        );

        return $member->load('profile');
    }

    public function uploadAvatar($member, $file)
    {
        // Get config
        $disk = config('member-system.avatars.disk', 'public');
        $path = config('member-system.avatars.path', 'avatars');

        // Store file
        $filename = $file->store($path, $disk);

        // Update DB
        $member->profile()->updateOrCreate(
            ['member_id' => $member->id],
            ['avatar' => $filename]
        );

        return $filename;
    }

    public function deleteMember(int $id)
    {
        $member = Member::findOrFail($id);
        $member->delete(); // Soft delete

        return true;
    }

    public function toggleStatus(int $id, bool $status)
    {
        $member = Member::findOrFail($id);
        $member->update(['is_active' => $status]);

        return $member;
    }

    public function assignRole(int $id, string $role)
    {
        $member = Member::findOrFail($id);
        $member->syncRoles([$role]); // Replace existing roles

        return $member;
    }
}
