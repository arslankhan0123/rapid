@extends('layouts.app')
@section('title')
    {{ __('messages.salary_generates.salary_generates') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@php
    $report_date =
        \Carbon\Carbon::createFromFormat('Y-m', $salaryGenerate->salary_month)->format('F Y') .
            ' | ' .
            $salaryGenerate->branch?->name ??
        '';
@endphp
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.salary_generates.salary_chart') }}
                {{ $report_date }}
            </h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('salary_generates.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.salary_generates.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">

                <div class="card-body">
                    {{-- @can('export_generate_salaries') --}}
                    {{-- <div class="d-flex justify-content-end mb-3">
                        <!-- Print Button -->

                        <a target="_blank"
                            href="{{ route('salary_generates.sheets.export', ['action' => 'csv', 'id' => $salaryGenerate->id]) }}"
                            class="btn btn-primary mr-2 text-white"
                            style="line-height: 30px;background:#1E90FF !important;">
                            <i class="fas fa-print"></i> CSV Download
                        </a>
                        <a target="_blank"
                            href="{{ route('salary_generates.sheets.export', ['action' => 'print', 'id' => $salaryGenerate->id]) }}"
                            class="btn btnWarning mr-2 text-white"
                            style="line-height: 30px;background:#87CEFA  !important;">
                            <i class="fas fa-print"></i> Print
                        </a>

                        <!-- PDF Download Button -->
                        <a href="{{ route('salary_generates.sheets.export', ['action' => 'download', 'id' => $salaryGenerate->id]) }}"
                            id="downloadButton" class="btn btn-primary mb-4"
                            style="line-height: 30px;background:#4682B4  !important;">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </div> --}}
                    <div class="d-flex justify-content-end mb-3">
                        <div class="dropdown">
                            <button
                                class="btn btn-outline-primary dropdown-toggle d-flex align-items-center justify-content-center shadow-sm"
                                type="button" id="downloadDropdown" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"
                                style="border-radius:8px; padding:8px 14px; font-size:16px; transition:all 0.2s ease;">
                                <i class="fas fa-cloud-download-alt"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0"
                                aria-labelledby="downloadDropdown" style="min-width:160px;">
                                <a class="dropdown-item d-flex align-items-center" target="_blank"
                                    href="{{ route('salary_generates.sheets.export', ['action' => 'csv', 'id' => $salaryGenerate->id]) }}">
                                    <i class="fas fa-file-csv text-primary mr-2"></i> CSV
                                </a>
                                <a class="dropdown-item d-flex align-items-center" target="_blank"
                                    href="{{ route('salary_generates.sheets.export', ['action' => 'print', 'id' => $salaryGenerate->id]) }}">
                                    <i class="fas fa-print text-secondary mr-2"></i> Print
                                </a>
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('salary_generates.sheets.export', ['action' => 'download', 'id' => $salaryGenerate->id]) }}">
                                    <i class="fas fa-file-pdf text-danger mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>


                    {{-- @endcan --}}
                    <div class="table-responsive">


                        <table id="salaryTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th style="white-space:nowrap;">Emp Id</th>
                                    <th style="white-space:nowrap;">Employee Name</th>
                                    <th>Iqama</th>
                                    <th style="white-space:nowrap;">Designation</th>
                                    <th style="white-space:nowrap;">Working Days</th>
                                    <th>Basic</th>
                                    <th>Bonuses</th>
                                    <th>Overtime</th>
                                    <th>Allowances</th>
                                    <th>Gross</th>
                                    <th>Advance</th>
                                    <th>Loan</th>
                                    <th style="white-space:nowrap;">Absent Deduction</th>
                                    <th>Insurance</th>
                                    <th style="white-space:nowrap;">Allowance Deduction</th>
                                    <th>Deduction</th>
                                    <th>Net</th>
                                    <th>Action</th>

                                </tr>
                             </thead>
                             <tbody>
                                 @foreach ($sheets as $sheet)
                                     @php
                                         $workingDays = $sheet->employee->attendance->where('hours', '>', 0)->count();
                                         $perDayAllowance = $sheet->total_allowances / 30;
                                         $perHourAllowance = $perDayAllowance / 8;
                                         $allowanceDeduction = $perHourAllowance * $sheet->absence_hours;
                                         $manualDeduction = $sheet->total_deduction - $sheet->hourly_deduction;
                                     @endphp
                                     <tr>
                                         <td>{{ $loop->iteration }}</td>
                                         <td style="white-space:nowrap;">{{ $sheet->employee->code }}</td>
                                         <td style="white-space:nowrap;">{{ $sheet->employee->name }}</td>
                                         <td>{{ $sheet->employee->iqama_no }}</td>
                                         <td style="white-space:nowrap;">{{ $sheet->employee->designation?->name ?? '' }}</td>
                                         <td style="white-space:nowrap;" class="text-center">{{ $workingDays }}</td>
                                         <td>{{ number_format($sheet->basic_salary, 2) }}</td>
                                         <td>{{ number_format($sheet->total_bonus, 2) }}</td>
                                         <td>{{ number_format($sheet->total_overtimes, 2) }}</td>
                                         <td>{{ number_format($sheet->total_allowances, 2) }}</td>
                                         <td>{{ number_format($sheet->basic_salary + $sheet->total_bonus + $sheet->total_overtimes + $sheet->total_allowances, 2) }}</td>
                                         <td>{{ number_format($sheet->salary_advance, 2) }}</td>
                                         <td>{{ number_format($sheet->loan, 2) }}</td>
                                         <td style="white-space:nowrap;">{{ number_format($sheet->hourly_deduction, 2) }}</td>
                                         <td>{{ number_format($sheet->total_insurance, 2) }}</td>
                                         <td style="white-space:nowrap;">{{ number_format($allowanceDeduction, 2) }}</td>
                                         <td>{{ number_format($manualDeduction, 2) }}</td>
                                         <td>{{ number_format($sheet->net_salary, 2) }}</td>
                                         <td style="white-space: nowrap;">
                                             <button onclick="printItem({{ $sheet->id }})" class="btn btn-info btn-sm">
                                                 <i class="fas fa-print"></i>
                                             </button>
                                             <button onclick="downloadItem({{ $sheet->id }})"
                                                 class="btn btn-success btn-sm ms-2">
                                                 <i class="fas fa-download"></i>
                                             </button>
                                         </td>
                                     </tr>
                                 @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>



    <script>
        function printItem(id) {

            const url = route('employee-salaries.payslip.download-view', {
                salarySheet: id
            });

            window.open(url, '_blank');
        }

        function downloadItem(id) {
            const url = route('employee-salaries.payslip.download', {
                salarySheet: id
            });
            window.open(url, '_blank');
        }
    </script>
@endsection
