<?php

namespace App\Queries;

use App\Models\SalarySheet;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class EmployeeStatementDataTable
{

    public function get($input = [])
    {
        /** @var SalarySheet $query */
        $query = SalarySheet::with(['employee.department', 'employee.designation', 'salaryGenerate', 'salaryPayment']);

        // Date range filters using SalaryGenerate's salary_month
        if (!empty($input['from_date']) && !empty($input['to_date'])) {
            $query->whereHas('salaryGenerate', function ($q) use ($input) {
                $q->whereBetween('salary_month', [$input['from_date'], $input['to_date']]);
            });
        } elseif (!empty($input['from_date'])) {
            $query->whereHas('salaryGenerate', function ($q) use ($input) {
                $q->where('salary_month', '>=', $input['from_date']);
            });
        } elseif (!empty($input['to_date'])) {
            $query->whereHas('salaryGenerate', callback: function ($q) use ($input) {
                $q->where('salary_month', '<=', $input['to_date']);
            });
        }

        // Filter by employee_id
        if (!empty($input['employee_id'])) {
            $query->where('employee_id', $input['employee_id']);
        }

        // Filter by department_id via related employee's department
        if (!empty($input['department_id'])) {
            $query->whereHas('employee.department', function ($q) use ($input) {
                $q->where('id', $input['department_id']);
            });
        }

        // Filter by designation_id via related employee's designation
        if (!empty($input['designation_id'])) {
            $query->whereHas('employee.designation', function ($q) use ($input) {
                $q->where('id', $input['designation_id']);
            });
        }

        // Filter by payment status
        if (!empty($input['payment_status'])) {
            $query->where('status', $input['payment_status']);
        }

        // Order by creation date (latest first)
        $query->orderBy('id', 'desc');

        // Get the results
        $salarySheets = $query->get();


        // Create a new array to hold the processed results
        $processedResults = [];
        $previousBalance = 0;
        // Loop through each salarySheet and format the necessary columns
        foreach ($salarySheets as $salarySheet) {

            // Prepare row data for DataTable (SalarySheet row)
            $row = [

                // Column 1: posted_at (Formatted date)
                'doc_date' => $salarySheet->salaryGenerate ?
                    (new \DateTime($salarySheet->salaryGenerate->generate_date))->format('d-m-Y') : '',

                // Column 3: "Salary" (Static text)
                'type' => 'Salary Sheet',
                'month' => $salarySheet->salaryGenerate ?
                    (new \DateTime($salarySheet->salaryGenerate->salary_month))->format('M Y') : '',

                // Column 5: salary_payment.amount (Formatted to 2 decimal places)
                'credit' => $salarySheet->net_salary ?? 0,
                'debit' => 0,
                'balance'=> $salarySheet->net_salary ?? 0
            ];

            $previousBalance= $salarySheet->net_salary ?? 0;
            // Add the row to the results array
            $processedResults[] = $row;

            // If salaryPayment exists, add the corresponding row for salaryPayment with the same structure
            if ($salarySheet->salaryPayment) {

                $processedResults[] = [
                    'doc_date' => $salarySheet->salaryPayment ?
                        (new \DateTime($salarySheet->salaryPayment->created_at))->format('d-m-Y') : '', // Leave empty or you can format another value
                    'type' => 'Payslip', // Static label to indicate this is a payment row
                    'month' =>  $salarySheet->salaryPayment ?
                        (new \DateTime($salarySheet->salaryPayment->created_at))->format('M Y') : '',
                    'debit' => $salarySheet->salaryPayment->amount, // Salary payment amount
                    'credit' => 0,
                    'balance'=> $previousBalance- $salarySheet->salaryPayment->amount


                ];
            }
            $previousBalance=0;
        }

        return $processedResults;
    }
}
