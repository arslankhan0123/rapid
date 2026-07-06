<?php

namespace App\Http\Controllers;

use App\Models\MasterAccount;
use Illuminate\Http\Request;
use DataTables;

class ProfitLossController extends AppBaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getProfitLossData($request);
        }
        $usersBranches = $this->getUsersBranches();
        return view('profit_and_loss.index', compact('usersBranches'));
    }

    private function getProfitLossData($request)
    {
        // Get only accounts with report_type = 'Profit and Loss Account' ordered by level
        $masterAccounts = MasterAccount::where('report_type', 'Profit and Loss Account')
            ->orderBy('account_level')
            ->get();

        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($masterAccounts as $account) {
            // Add account row - show 00.00 for amounts as no calculation required
            $debitAmount = 0.00; // Fixed 00.00 amount
            $creditAmount = 0.00; // Fixed 00.00 amount

            $totalDebit += $debitAmount;
            $totalCredit += $creditAmount;

            $data[] = [
                'name' => $this->getIndentedName($account),
                'debit_amount' => '0.00',
                'credit_amount' => '0.00',
                'is_header' => false
            ];
        }

        // Add total row
        if (count($data) > 0) {
            $data[] = [
                'name' => '<strong>Total</strong>',
                'debit_amount' => '<strong>0.00</strong>',
                'credit_amount' => '<strong>0.00</strong>',
                'is_header' => false,
                'is_total' => true
            ];
        } else {
            // Add a row showing no data available
            $data[] = [
                'name' => '<em>No profit and loss accounts found</em>',
                'debit_amount' => '0.00',
                'credit_amount' => '0.00',
                'is_header' => false,
                'is_total' => false
            ];
        }

        return DataTables::of($data)
            ->rawColumns(['name', 'debit_amount', 'credit_amount'])
            ->make(true);
    }

    private function getIndentedName($account)
    {
        // Create indentation based on account level
        switch ($account->account_level) {
            case 'level-1':
                return '<strong>' . $account->name . '</strong>';
            case 'level-2':
                return '&nbsp;&nbsp;&nbsp;&nbsp;' . $account->name;
            case 'level-3':
                return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account->name;
            case 'level-4':
                return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account->name;
            default:
                return $account->name;
        }
    }
}
