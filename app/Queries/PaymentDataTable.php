<?php

namespace App\Queries;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PaymentDataTable
 */
class PaymentDataTable
{
    /**
     * @param  array  $input
     * @return Payment
     */
    public function get($input = [])
    {
        /** @var Payment $query */
        $query = Payment::with(['paymentMode', 'invoice', 'branch'])->select('payments.*')->latest();
        $query->when(
            !empty($input['filterBranch']),
            function ($q) use ($input) {
                // Filter by the specific branch if provided
                $q->where('branch_id', $input['filterBranch']);
            },
            function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereHas('branch', function ($branchQuery) {
                    $branchQuery->whereIn('id', function ($subQuery) {
                        $subQuery->select('branch_id')
                            ->from('users_branches')
                            ->where('user_id', auth()->id());
                    });
                });
            }
        );
        $query->when(isset($input['owner_id']), function (Builder $q) use ($input) {
            $q->where('owner_id', '=', $input['owner_id']);
        });

        $query->when(isset($input['owner_type']), function (Builder $q) use ($input) {
            $q->where('owner_type', '=', $input['owner_type']);
        });

        return $query;
    }
}
