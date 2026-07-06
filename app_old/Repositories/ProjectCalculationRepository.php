<?php

// namespace App\Repositories;

// use App\Models\ProjectsCalculation;
// use App\Models\ProjectValue;
// use App\Models\ProjectExpense;
// use Illuminate\Support\Arr;

// class ProjectCalculationRepository extends BaseRepository
// {
//     protected $fieldSearchable = [
//         'code',
//         'name'
//     ];

//     public function getFieldsSearchable()
//     {
//         return $this->fieldSearchable;
//     }

//     public function model()
//     {
//         return ProjectsCalculation::class;
//     }

//     public function create($input)
//     {
//         $project = ProjectsCalculation::create(Arr::only($input, ['code', 'name']));

//         // Save project values
//         if (!empty($input['values'])) {
//             foreach ($input['values'] as $value) {
//                 $gross = $value['amount'] * $value['quantity'];
//                 $net = $gross - ($gross * ($value['discount'] / 100));

//                 $project->values()->create([
//                     'category' => $value['category'],
//                     'amount' => $value['amount'],
//                     'unit' => $value['unit'],
//                     'quantity' => $value['quantity'],
//                     'discount' => $value['discount'],
//                     'gross_amount' => $gross,
//                     'net_amount' => $net
//                 ]);
//             }
//         }

//         // Save project expenses
//         if (!empty($input['expenses'])) {
//             foreach ($input['expenses'] as $expense) {
//                 $total = $expense['amount'] * $expense['quantity'];
//                 if ($expense['percentage'] > 0) {
//                     $total = $total * ($expense['percentage'] / 100);
//                 }

//                 $project->expenses()->create([
//                     'name' => $expense['name'],
//                     'amount' => $expense['amount'],
//                     'percentage' => $expense['percentage'],
//                     'unit' => $expense['unit'],
//                     'quantity' => $expense['quantity'],
//                     'total_amount' => $total
//                 ]);
//             }
//         }

//         return $project;
//     }

//     public function update($input, $id)
//     {
//         $project = ProjectsCalculation::findOrFail($id);
//         $project->update(Arr::only($input, ['code', 'name']));

//         // Delete existing values and expenses
//         $project->values()->delete();
//         $project->expenses()->delete();

//         // Save project values
//         if (!empty($input['values'])) {
//             foreach ($input['values'] as $value) {
//                 $gross = $value['amount'] * $value['quantity'];
//                 $net = $gross - ($gross * ($value['discount'] / 100));

//                 $project->values()->create([
//                     'category' => $value['category'],
//                     'amount' => $value['amount'],
//                     'unit' => $value['unit'],
//                     'quantity' => $value['quantity'],
//                     'discount' => $value['discount'],
//                     'gross_amount' => $gross,
//                     'net_amount' => $net
//                 ]);
//             }
//         }

//         // Save project expenses
//         if (!empty($input['expenses'])) {
//             foreach ($input['expenses'] as $expense) {
//                 $total = $expense['amount'] * $expense['quantity'];
//                 if ($expense['percentage'] > 0) {
//                     $total = $total * ($expense['percentage'] / 100);
//                 }

//                 $project->expenses()->create([
//                     'name' => $expense['name'],
//                     'amount' => $expense['amount'],
//                     'percentage' => $expense['percentage'],
//                     'unit' => $expense['unit'],
//                     'quantity' => $expense['quantity'],
//                     'total_amount' => $total
//                 ]);
//             }
//         }

//         return $project;
//     }
// }


namespace App\Repositories;

use App\Models\ProjectsCalculation;
use App\Models\ProjectValue;
use App\Models\ProjectExpense;
use App\Models\ProjectCalculationPartner;
use Illuminate\Support\Arr;

class ProjectCalculationRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'name',
        'customer_name',
        'address',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return ProjectsCalculation::class;
    }

    // public function create($input)
    // {
    //     // Create main project
    //     $project = ProjectsCalculation::create(Arr::only($input, ['code', 'name', 'customer_name', 'address']));

    //     // Get project months from input
    //     $projectMonths = $input['project_months'] ?? 1;

    //     // Save project values
    //     if (!empty($input['values'])) {
    //         $totalGross = 0;

    //         // First calculate total gross for percentage calculation
    //         foreach ($input['values'] as $value) {
    //             $gross = $value['amount'] * $value['unit'] * $value['quantity'];
    //             $totalGross += $gross;
    //         }

    //         // Now create values with proper percentage calculation
    //         foreach ($input['values'] as $value) {
    //             $gross = $value['amount'] * $value['unit'] * $value['quantity'];
    //             $net = $gross - $value['discount'];
    //             $percentage = $totalGross > 0 ? ($gross / $totalGross) * 100 : 0;

    //             $project->values()->create([
    //                 'category' => $value['category'],
    //                 'amount' => $value['amount'],
    //                 'unit' => $value['unit'],
    //                 'quantity' => $value['quantity'],
    //                 'discount' => $value['discount'],
    //                 'gross_amount' => $gross,
    //                 'net_amount' => $net,
    //                 'percentage' => $percentage
    //             ]);
    //         }
    //     }

    //     // Save project expenses
    //     if (!empty($input['expenses'])) {
    //         $totalExpenseAmount = 0;

    //         // First calculate total expense amount for percentage calculation
    //         foreach ($input['expenses'] as $expense) {
    //             $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
    //             $totalExpenseAmount += $total;
    //         }

    //         // Now create expenses with proper percentage calculation
    //         foreach ($input['expenses'] as $expense) {
    //             $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
    //             $percentage = $totalExpenseAmount > 0 ? ($total / $totalExpenseAmount) * 100 : 0;

    //             $project->expenses()->create([
    //                 'name' => $expense['name'],
    //                 'amount' => $expense['amount'],
    //                 'percentage' => $percentage,
    //                 'unit' => $expense['unit'],
    //                 'quantity' => $expense['quantity'],
    //                 'total_amount' => $total
    //             ]);
    //         }
    //     }

    //     // Calculate final values for partners
    //     $finalProjectValue = $project->values->sum('net_amount');
    //     $finalProjectExpense = $project->expenses->sum('total_amount') * $projectMonths;
    //     $profitAmount = $finalProjectValue - $finalProjectExpense;

    //     // Save project partners
    //     if (!empty($input['partners'])) {
    //         foreach ($input['partners'] as $partner) {
    //             $perPartner = $profitAmount * ($partner['percentage'] / 100);

    //             $project->partners()->create([
    //                 'name' => $partner['name'],
    //                 'amount' => $profitAmount,
    //                 'percentage' => $partner['percentage'],
    //                 'per_partner' => $perPartner
    //             ]);
    //         }
    //     }

    //     return $project;
    // }

    // public function update($input, $id)
    // {
    //     $project = ProjectsCalculation::findOrFail($id);
    //     $project->update(Arr::only($input, ['code', 'name', 'customer_name', 'address']));

    //     // Get project months from input
    //     $projectMonths = $input['project_months'] ?? 1;

    //     // Delete existing relations
    //     $project->values()->delete();
    //     $project->expenses()->delete();
    //     $project->partners()->delete();

    //     // Save project values
    //     if (!empty($input['values'])) {
    //         $totalGross = 0;

    //         // Calculate total gross first
    //         foreach ($input['values'] as $value) {
    //             $gross = $value['amount'] * $value['unit'] * $value['quantity'];
    //             $totalGross += $gross;
    //         }

    //         foreach ($input['values'] as $value) {
    //             $gross = $value['amount'] * $value['unit'] * $value['quantity'];
    //             $net = $gross - $value['discount'];
    //             $percentage = $totalGross > 0 ? ($gross / $totalGross) * 100 : 0;

    //             $project->values()->create([
    //                 'category' => $value['category'],
    //                 'amount' => $value['amount'],
    //                 'unit' => $value['unit'],
    //                 'quantity' => $value['quantity'],
    //                 'discount' => $value['discount'],
    //                 'gross_amount' => $gross,
    //                 'net_amount' => $net,
    //                 'percentage' => $percentage
    //             ]);
    //         }
    //     }

    //     // Save project expenses
    //     if (!empty($input['expenses'])) {
    //         $totalExpenseAmount = 0;

    //         // Calculate total expense amount first
    //         foreach ($input['expenses'] as $expense) {
    //             $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
    //             $totalExpenseAmount += $total;
    //         }

    //         foreach ($input['expenses'] as $expense) {
    //             $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
    //             $percentage = $totalExpenseAmount > 0 ? ($total / $totalExpenseAmount) * 100 : 0;

    //             $project->expenses()->create([
    //                 'name' => $expense['name'],
    //                 'amount' => $expense['amount'],
    //                 'percentage' => $percentage,
    //                 'unit' => $expense['unit'],
    //                 'quantity' => $expense['quantity'],
    //                 'total_amount' => $total
    //             ]);
    //         }
    //     }

    //     // Calculate final values for partners
    //     $finalProjectValue = collect($input['values'] ?? [])->sum(function ($v) {
    //         return ($v['amount'] * $v['unit'] * $v['quantity']) - $v['discount'];
    //     });

    //     $finalProjectExpense = collect($input['expenses'] ?? [])->sum('total_amount') * $projectMonths;

    //     $profitAmount = $finalProjectValue - $finalProjectExpense;

    //     // Save project partners
    //     if (!empty($input['partners'])) {
    //         foreach ($input['partners'] as $partner) {
    //             $perPartner = $profitAmount * ($partner['percentage'] / 100);

    //             $project->partners()->create([
    //                 'name' => $partner['name'],
    //                 'amount' => $profitAmount,
    //                 'percentage' => $partner['percentage'],
    //                 'per_partner' => $perPartner
    //             ]);
    //         }
    //     }

    //     return $project;
    // }

    public function create($input)
    {
        // Create main project
        $project = ProjectsCalculation::create(Arr::only($input, ['code', 'name', 'customer_name', 'address']));

        // Get project months from input (PROJECT SUMMARY month - for partner distribution)
        $projectMonths = $input['project_months'] ?? 1;

        // Save project values
        if (!empty($input['values'])) {
            $totalGross = 0;

            foreach ($input['values'] as $value) {
                $gross = $value['amount'] * $value['unit'] * $value['quantity'];
                $totalGross += $gross;
            }

            foreach ($input['values'] as $value) {
                $gross = $value['amount'] * $value['unit'] * $value['quantity'];
                $net = $gross - $value['discount'];
                $percentage = $totalGross > 0 ? ($gross / $totalGross) * 100 : 0;

                $project->values()->create([
                    'category' => $value['category'],
                    'amount' => $value['amount'],
                    'unit' => $value['unit'],
                    'quantity' => $value['quantity'],
                    'discount' => $value['discount'],
                    'gross_amount' => $gross,
                    'net_amount' => $net,
                    'percentage' => $percentage
                ]);
            }
        }

        // Save project expenses
        if (!empty($input['expenses'])) {
            $totalExpenseAmount = 0;

            foreach ($input['expenses'] as $expense) {
                $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
                $totalExpenseAmount += $total;
            }

            foreach ($input['expenses'] as $expense) {
                $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
                $percentage = $totalExpenseAmount > 0 ? ($total / $totalExpenseAmount) * 100 : 0;

                $project->expenses()->create([
                    'name' => $expense['name'],
                    'amount' => $expense['amount'],
                    'percentage' => $percentage,
                    'unit' => $expense['unit'],
                    'quantity' => $expense['quantity'],
                    'total_amount' => $total
                ]);
            }
        }

        // Save project commissions (use their own months field)
        if (!empty($input['commissions'])) {
            foreach ($input['commissions'] as $commission) {
                $commissionMonths = $commission['months'] ?? 1;
                $perMonth = $commission['commission_amount'] / $commissionMonths;

                $project->commissions()->create([
                    'commission_amount' => $commission['commission_amount'],
                    'project_months' => $commissionMonths,
                    'per_month' => $perMonth
                ]);
            }
        }

        // Save project insurances (use their own months field)
        if (!empty($input['insurances'])) {
            foreach ($input['insurances'] as $insurance) {
                $insuranceMonths = $insurance['months'] ?? 1;
                $perMonth = $insurance['insurance_amount'] / $insuranceMonths;

                $project->insurances()->create([
                    'insurance_amount' => $insurance['insurance_amount'],
                    'project_months' => $insuranceMonths,
                    'per_month' => $perMonth
                ]);
            }
        }

        // Calculate final values for partners (AFTER deducting commissions and insurance from expenses)
        $finalProjectValue = $project->values->sum('net_amount');

        // Calculate total expenses MINUS commissions and insurance
        $totalExpenses = $project->expenses->sum('total_amount');
        $totalCommissions = $project->commissions->sum('commission_amount');
        $totalInsurances = $project->insurances->sum('insurance_amount');

        // Final expense = Expenses - Commissions - Insurance
        $finalProjectExpense = $totalExpenses + $totalCommissions + $totalInsurances;
        $profitAmount = $finalProjectValue - $finalProjectExpense;

        // Calculate profit per month for partners (based on PROJECT SUMMARY months)
        $profitPerMonth = $profitAmount / $projectMonths;

        // Save project partners
        if (!empty($input['partners'])) {
            foreach ($input['partners'] as $partner) {
                $perPartnerPerMonth = $profitPerMonth * ($partner['percentage'] / 100);
                $totalPerPartner = $perPartnerPerMonth * $projectMonths;

                $project->partners()->create([
                    'name' => $partner['name'],
                    'amount' => $profitPerMonth, // Store profit per month
                    'project_months' => $projectMonths, // Store project summary months
                    'percentage' => $partner['percentage'],
                    'per_partner' => $totalPerPartner // Store total for the project duration
                ]);
            }
        }

        return $project;
    }

    public function update($input, $id)
    {
        $project = ProjectsCalculation::findOrFail($id);
        $project->update(Arr::only($input, ['code', 'name', 'customer_name', 'address']));

        // Get project months from input (PROJECT SUMMARY month - for partner distribution)
        $projectMonths = $input['project_months'] ?? 1;

        // Delete existing relations
        $project->values()->delete();
        $project->expenses()->delete();
        $project->commissions()->delete();
        $project->insurances()->delete();
        $project->partners()->delete();

        // Save project values
        if (!empty($input['values'])) {
            $totalGross = 0;

            foreach ($input['values'] as $value) {
                $gross = $value['amount'] * $value['unit'] * $value['quantity'];
                $totalGross += $gross;
            }

            foreach ($input['values'] as $value) {
                $gross = $value['amount'] * $value['unit'] * $value['quantity'];
                $net = $gross - $value['discount'];
                $percentage = $totalGross > 0 ? ($gross / $totalGross) * 100 : 0;

                $project->values()->create([
                    'category' => $value['category'],
                    'amount' => $value['amount'],
                    'unit' => $value['unit'],
                    'quantity' => $value['quantity'],
                    'discount' => $value['discount'],
                    'gross_amount' => $gross,
                    'net_amount' => $net,
                    'percentage' => $percentage
                ]);
            }
        }

        // Save project expenses
        if (!empty($input['expenses'])) {
            $totalExpenseAmount = 0;

            foreach ($input['expenses'] as $expense) {
                $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
                $totalExpenseAmount += $total;
            }

            foreach ($input['expenses'] as $expense) {
                $total = $expense['amount'] * $expense['unit'] * $expense['quantity'];
                $percentage = $totalExpenseAmount > 0 ? ($total / $totalExpenseAmount) * 100 : 0;

                $project->expenses()->create([
                    'name' => $expense['name'],
                    'amount' => $expense['amount'],
                    'percentage' => $percentage,
                    'unit' => $expense['unit'],
                    'quantity' => $expense['quantity'],
                    'total_amount' => $total
                ]);
            }
        }

        // Save project commissions (use their own months field)
        if (!empty($input['commissions'])) {
            foreach ($input['commissions'] as $commission) {
                $commissionMonths = $commission['months'] ?? 1;
                $perMonth = $commission['commission_amount'] / $commissionMonths;

                $project->commissions()->create([
                    'commission_amount' => $commission['commission_amount'],
                    'project_months' => $commissionMonths,
                    'per_month' => $perMonth
                ]);
            }
        }

        // Save project insurances (use their own months field)
        if (!empty($input['insurances'])) {
            foreach ($input['insurances'] as $insurance) {
                $insuranceMonths = $insurance['months'] ?? 1;
                $perMonth = $insurance['insurance_amount'] / $insuranceMonths;

                $project->insurances()->create([
                    'insurance_amount' => $insurance['insurance_amount'],
                    'project_months' => $insuranceMonths,
                    'per_month' => $perMonth
                ]);
            }
        }

        // Calculate final values for partners (AFTER deducting commissions and insurance from expenses)
        $finalProjectValue = collect($input['values'] ?? [])->sum(function ($v) {
            return ($v['amount'] * $v['unit'] * $v['quantity']) - $v['discount'];
        });

        $totalExpenses = collect($input['expenses'] ?? [])->sum('total_amount');
        $totalCommissions = collect($input['commissions'] ?? [])->sum('commission_amount');
        $totalInsurances = collect($input['insurances'] ?? [])->sum('insurance_amount');

        // Final expense = Expenses - Commissions - Insurance
        $finalProjectExpense = $totalExpenses - $totalCommissions - $totalInsurances;
        $profitAmount = $finalProjectValue - $finalProjectExpense;

        // Calculate profit per month for partners (based on PROJECT SUMMARY months)
        $profitPerMonth = $profitAmount / $projectMonths;

        // Save project partners
        if (!empty($input['partners'])) {
            foreach ($input['partners'] as $partner) {
                $perPartnerPerMonth = $profitPerMonth * ($partner['percentage'] / 100);
                $totalPerPartner = $perPartnerPerMonth * $projectMonths;

                $project->partners()->create([
                    'name' => $partner['name'],
                    'amount' => $profitPerMonth, // Store profit per month
                    'project_months' => $projectMonths, // Store project summary months
                    'percentage' => $partner['percentage'],
                    'per_partner' => $totalPerPartner // Store total for the project duration
                ]);
            }
        }

        return $project;
    }
}
