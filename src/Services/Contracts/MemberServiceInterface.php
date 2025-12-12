<?php

namespace Det\Members\Services\Contracts;

interface MemberServiceInterface
{
    public function getAllMembers(array $filters, int $perPage);
    public function createMember(array $data);
    public function updateMember(int $id, array $data);
    public function updateProfile($member, array $data);
    public function uploadAvatar($member, $file);
    public function deleteMember(int $id);
    public function toggleStatus(int $id, bool $status);
    public function assignRole(int $id, string $role);
}