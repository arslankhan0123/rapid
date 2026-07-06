@extends('layouts.app')
@section('title')
    {{ __('messages.salary_generates.salary_generates') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .payslip-header {
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }

        .table-custom {
            margin-bottom: 40px;
            border: 1px solid #dee2e6;
        }

        .table-custom th,
        .table-custom td {
            padding: 8px;
            vertical-align: middle;
            color: black;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: black;
        }

        .summary-table th {
            text-align: right;
            color: black;
        }

        .summary-table td {
            text-align: right;
            color: black;
        }

        .btn-group-custom {
            color: black;
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.employee_salaries.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('employee-salaries.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.salary_generates.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid" id="payslip-content">
                        <div class="payslip-header">
                            <h2>{{ __('messages.employee_salaries.payslip') }} (
                                {{ str_pad($salarySheet->employee->iqama_no, 6, '0', STR_PAD_LEFT) }})</h2>
                        </div>

                        <table class="table table-bordered table-custom">
                            <tr>
                                <th>{{ __('messages.employee_salaries.employee_name') }}</th>
                                <td>{{ $salarySheet->employee->name }}</td>
                                <th>{{ __('messages.employee_salaries.id') }}</th>
                                <td>{{ $salarySheet->employee->iqama_no }}</td>

                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.position') }}</th>
                                <td>{{ $salarySheet->employee->designation->name ?? null }}</td>
                                <th>{{ __('messages.employee_salaries.from') }}</th>
                                <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->startOfMonth()->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.contact') }}</th>
                                <td>{{ $salarySheet->employee->phone }}</td>
                                <th>{{ __('messages.employee_salaries.to') }}</th>
                                <td> {{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->endOfMonth()->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.address') }}</th>
                                <td>{{ $salarySheet->employee->city }}, {{ $salarySheet->employee->state }}</td>
                                <th>{{ __('messages.employee_salaries.approved_date') }}</th>
                                <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->approved_date)->format('F d, Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.total_working_hours') }}</th>
                                <td></td> <!-- Example value -->
                                <th>{{ __('messages.employee_salaries.worked_hours') }}</th>
                                <td></td> <!-- Example value -->
                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.basic_salary') }}</td>
                                <td>{{ number_format($salarySheet->basic_salary, 2) }}</td>

                                <th>{{ __('messages.employee_salaries.gross_salary') }}</th>
                                <td>{{ number_format($salarySheet->gross_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.employee_salaries.month') }}</th>
                                <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->format('F, Y') }}
                                </td>
                            </tr>

                        </table>

                        <table class="table table-bordered table-custom">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.employee_salaries.description') }}</th>
                                    <th>{{ __('messages.employee_salaries.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('messages.employee_salaries.total_deduction') }}</th>

                                    <td>{{ number_format($salarySheet->total_deduction, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.employee_salaries.overtime') }}</td>
                                    <td>{{ number_format($salarySheet->total_bonus, 2) }}</td>
                                </tr>

                                <tr>
                                    <td>{{ __('messages.employee_salaries.salary_advance') }}</td>

                                    <td>{{ number_format($salarySheet->salary_advance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.employee_salaries.total_allowances') }}</td>
                                    <td>{{ number_format($salarySheet->total_allowances, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.employee_salaries.total_loan') }}</td>

                                    <td>{{ number_format($salarySheet->loan, 2) }}</td>
                                </tr>


                                <tr>
                                    <th>{{ __('messages.employee_salaries.net_salary') }}</th>
                                    <th>{{ number_format($salarySheet->net_salary, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <div class="row justify-content-end">
                            <div class="col-4 text-center">
                                <hr>
                                <p>Authorized by</p>
                            </div>
                        </div>


                    </div>
                    <!-- Buttons Section -->
                    @can('export_employee_salaries')
                        <div class="row justify-content-end">
                            <div class="col-4">
                                <input type="hidden" id="employee-id" value="{{ $salarySheet->employee->id }}">
                                <input type="hidden" id="salary-month"
                                    value="{{ $salarySheet->salaryGenerate->salary_month }}">
                                <div class="btn-group-custom">
                                    <button class="btn btn-primary m-1" onclick="printPayslip();">Print</button>
                                    <button class="btn btn-success m-1" id="generate-pdf">Generate PDF</button>
                                </div>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
        </div>
    </section>
    @include('salary_generates.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        function printPayslip() {
            var printContents = document.getElementById('payslip-content').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
        $(document).ready(function() {
            $('#generate-pdf').click(function() {
                const {
                    jsPDF
                } = window.jspdf;

                const employeeId = $('#employee-id').val();
                const salaryMonth = $('#salary-month').val();
                const fileName = 'Payslip_emp_' + `${employeeId}_date_${salaryMonth}.pdf`;

                html2canvas(document.querySelector("#payslip-content"), {
                    scale: 2, // Increase scale for higher resolution
                    useCORS: true // Handle CORS issues if there are external resources
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });

                    const margin = 10; // 10mm margin
                    const imgWidth = 210 - 2 * margin; // A4 size width minus margins
                    const pageHeight = 297 - 2 * margin; // A4 size height minus margins
                    const imgHeight = canvas.height * imgWidth / canvas.width;
                    let heightLeft = imgHeight;
                    let position = margin; // Start position with top margin

                    pdf.addImage(imgData, 'PNG', margin, position, imgWidth, imgHeight, '', 'FAST');
                    heightLeft -= pageHeight;

                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight + margin;
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', margin, position, imgWidth, imgHeight, '',
                            'FAST');
                        heightLeft -= pageHeight;
                    }

                    pdf.save(fileName); // Save the PDF with the dynamic file name
                });
            });
        });
    </script>
@endsection
