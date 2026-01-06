<?php

namespace App\Services;

use App\Models\InviteCode;
use App\Repositories\InviteCodeRepository;

class InviteCodeService extends InviteCodeRepository
{
    public function getDiscount($inviteCode)
    {
        $inviteCode = InviteCode::where('code', $inviteCode)->first();
        if($inviteCode === null) {
            return 'false';
        } else {
            return $inviteCode->discount;
        }
    }

    public function storeInviteCode($request)
    {
        $input = $request->all();
        return InviteCodeRepository::create($input);
    }

    public function getAllInviteCodes()
    {
        return InviteCodeRepository::all();
    }

    public function getInviteCodeByCode($inviteCode)
    {
        return InviteCode::where('code', $inviteCode)->first();
    }

    public function useInviteCode($inviteCode, $total)
    {
        $inviteCode = $this->getInviteCodeByCode($inviteCode);
        $usageCount = $inviteCode->usage_count;
        $orderTotal = $inviteCode->order_total;
        $input['usage_count'] = $usageCount+1;
        $input['order_total'] = intval($orderTotal)+$total;

        InviteCodeRepository::update($input, $inviteCode->id);
    }

    public function reductionInviteCode($inviteCode, $total)
    {
        $inviteCode = $this->getInviteCodeByCode($inviteCode);
        $usageCount = $inviteCode->usage_count;
        $orderTotal = $inviteCode->order_total;
        $input['usage_count'] = $usageCount-1;
        $input['order_total'] = intval($orderTotal)-$total;

        InviteCodeRepository::update($input, $inviteCode->id);
    }
}
