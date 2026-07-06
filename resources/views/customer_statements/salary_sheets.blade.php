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
    $report_date = \Carbon\Carbon::createFromFormat('Y-m', $salaryGenerate->salary_month)->format('F Y');
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
                    <div class="table-responsive">
                        <div class="d-flex justify-content-end mb-3">
                            <!-- Print Button -->
                            <button id="printButton" class="btn btn-secondary mr-2" style="height: 36px;">
                                <i class="fas fa-print"></i> Print
                            </button>

                            <!-- PDF Download Button -->
                            <button id="downloadButton" class="btn btn-primary mb-4"> <i class="fas fa-download"></i>
                                Download PDF</button>

                        </div>
                        <table id="salaryTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.salaries.employee') }}</th>
                                    <th>{{ __('messages.employee_salaries.basic_salary') }}</th>
                                    <th>{{ __('messages.employee_salaries.salary_advance') }}</th>
                                    <th>{{ __('messages.employee_salaries.gross_salary') }}</th>
                                    <th>{{ __('messages.employee_salaries.state_income_tax') }}</th>
                                    <th>{{ __('messages.employee_salaries.loan') }}</th>
                                    <th>{{ __('messages.employee_salaries.total_bonus') }}</th>
                                    <th>{{ __('messages.employee_salaries.total_allowances') }}</th>
                                    <th>{{ __('messages.employee_salaries.total_commission') }}</th>
                                    <th>{{ __('messages.employee_salaries.total_insurance') }}</th>
                                    <th>{{ __('messages.employee_salaries.total_deduction') }}</th>
                                    <th>{{ __('messages.employee_salaries.net_salary') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sheets as $sheet)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sheet->employee->name }}</td>
                                        <td>{{ number_format($sheet->basic_salary, 2) }}</td>
                                        <td>{{ number_format($sheet->salary_advance, 2) }}</td>
                                        <td>{{ number_format($sheet->gross_salary, 2) }}</td>
                                        <td>{{ number_format($sheet->state_income_tax, 2) }}</td>
                                        <td>{{ number_format($sheet->loan, 2) }}</td>
                                        <td>{{ number_format($sheet->total_bonus, 2) }}</td>
                                        <td>{{ number_format($sheet->total_allowances, 2) }}</td>
                                        <td>{{ number_format($sheet->total_commission, 2) }}</td>
                                        <td>{{ number_format($sheet->total_insurance, 2) }}</td>
                                        <td>{{ number_format($sheet->total_deduction, 2) }}</td>
                                        <td>{{ number_format($sheet->net_salary, 2) }}</td>
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
    <!-- Include jsPDF and html2canvas libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <!-- Add these lines in your Blade template or layout file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Add these lines in your Blade template or layout file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        document.getElementById('downloadButton').addEventListener('click', function() {
            html2canvas(document.getElementById('salaryTable')).then(canvas => {
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4'); // Create a new PDF document

                const imgData = canvas.toDataURL('image/png'); // Convert canvas to image data
                const imgWidth = 190; // Width of A4 page in mm (adjust as needed)
                const pageHeight = 295; // Height of A4 page in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                // Add title to the first page
                pdf.setFontSize(16);
                pdf.setFont("arial", "bold");
                pdf.setTextColor(0, 0, 0); // Set text color to black (RGB)

                pdf.text('Salary Chart for {{ $report_date }}', 14, 20); // Adjust position as needed

                // Add the table image
                pdf.addImage(imgData, 'PNG', 10, 30, imgWidth, imgHeight); // Adjust margins if needed
                heightLeft -= pageHeight - 30; // Adjust heightLeft based on header height

                while (heightLeft >= 0) {
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight); // Adjust margins if needed
                    heightLeft -= pageHeight;
                }

                // Save the PDF file with dynamic filename
                const fileName =
                `salary_sheets_${new Date().toISOString().slice(0,10)}.pdf`; // e.g., salary_sheets_2024-08-20.pdf
                pdf.save(fileName); // Save the PDF file
            });
        });
    </script>



    <script>
        document.getElementById('printButton').addEventListener('click', function() {
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Salary Table for {{ $report_date }}</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
            printWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; }');
            printWindow.document.write('thead { background-color: #f2f2f2; }');
            printWindow.document.write('@media print {');
            printWindow.document.write('body { margin: 0; }');
            printWindow.document.write('table { border-collapse: collapse; }');
            printWindow.document.write('th, td { border: 1px solid black; }');
            printWindow.document.write('}');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write(document.getElementById('salaryTable').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });
    </script>
@endsection
