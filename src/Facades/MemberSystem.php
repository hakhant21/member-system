<?php

namespace Det\Members\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Det\Members\Models\Member createMember(array $data)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getAllMembers(array $filters, int $perPage)
 * @method static \Det\Members\Models\Member updateMember(int $id, array $data)
 * @method static bool deleteMember(int $id)
 * 
 * @see \Det\Members\Services\MemberService
 */
class MemberSystem extends Facade
{
    protected static function getFacadeAccessor()
    {
        // This key matches the binding in your ServiceProvider
        return \Det\Members\Services\Contracts\MemberServiceInterface::class;
    }
}