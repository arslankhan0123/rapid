<?php

namespace App\Http\Controllers;

use App\Models\MasterAccount;
use Illuminate\Http\Request;
use DataTables;

class BalanceSheetController extends AppBaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getBalanceSheetData($request);
        }
        $usersBranches = $this->getUsersBranches();
        return view('balance_sheets.index', compact('usersBranches'));
    }

    private function getBalanceSheetData($request)
    {
        // Get only accounts with report_type = 'Balance Sheet' ordered by type and level
        $masterAccounts = MasterAccount::where('report_type', 'Balance Sheet')
            ->orderByRaw("
                CASE
                    WHEN account_type = 'Assets' THEN 1
                    WHEN account_type = 'Liabilities' THEN 2
                    WHEN account_type = 'Equity' THEN 3
                END
            ")->orderBy('account_level')->get();

        $data = [];
        $currentType = null;
        $typeSubtotal = 0;

        foreach ($masterAccounts as $account) {
            // Add type header row when type changes
            if ($currentType !== $account->account_type) {
                if ($currentType !== null) {
                    // Add subtotal row for previous type
                    $data[] = $this->createSubtotalRow($currentType, $typeSubtotal);
                    $typeSubtotal = 0;
                }

                // Add type header row
                $data[] = $this->createTypeHeaderRow($account->account_type);
                $currentType = $account->account_type;
            }

            // Add account row
            $amount = 0.00; // You can replace this with actual amounts from your database
            $typeSubtotal += $amount;

            $data[] = [
                'name_type' => $this->getIndentedName($account),
                'assets_amount' => $account->account_type === 'Assets' ? number_format($amount, 2) : '',
                'liabilities_amount' => in_array($account->account_type, ['Liabilities', 'Equity']) ? number_format($amount, 2) : '',
                'is_header' => false,
                'is_subtotal' => false
            ];
        }

        // Add final subtotal row
        if ($currentType !== null) {
            $data[] = $this->createSubtotalRow($currentType, $typeSubtotal);
        }

        return DataTables::of($data)
            ->rawColumns(['name_type'])
            ->make(true);
    }

    private function createTypeHeaderRow($type)
    {
        return [
            'name_type' => '<strong>' . strtoupper($type) . '</strong>',
            'assets_amount' => '',
            'liabilities_amount' => '',
            'is_header' => true,
            'is_subtotal' => false
        ];
    }

    private function createSubtotalRow($type, $subtotal)
    {
        return [
            'name_type' => '<strong>Total ' . $type . '</strong>',
            'assets_amount' => $type === 'Assets' ? number_format($subtotal, 2) : '',
            'liabilities_amount' => in_array($type, ['Liabilities', 'Equity']) ? number_format($subtotal, 2) : '',
            'is_header' => false,
            'is_subtotal' => true
        ];
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
