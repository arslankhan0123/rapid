@extends('layouts.app')
@section('title')
    {{ __('messages.safety_materials.add_safety_material') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.safety_materials.add_safety_material') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('safety-materials.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')
                    {!! Form::open(['route' => 'safety-materials.store', 'id' => 'createSafetyMaterialForm']) !!}
                    @include('safety_materials.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // Calculate next date when date or duration changes
            $('#date, #duration').on('change', function() {
                calculateNextDate();
            });

            function calculateNextDate() {
                const date = $('#date').val();
                const duration = $('#duration').val();

                if (date && duration) {
                    $.ajax({
                        url: '{{ route('safety-materials.calculate-next-date') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            date: date,
                            duration: duration
                        },
                        success: function(response) {
                            $('#next_date').val(response.next_date);
                        }
                    });
                }
            }

            // Submit form with spinner and redirect
            $('#createSafetyMaterialForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSave');
                $btn.html($btn.data('loading-text')).attr('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = "{{ route('safety-materials.index') }}";
                        } else {
                            alert(response.message || 'Something went wrong');
                            $btn.html('{{ __('messages.common.submit') }}').attr('disabled',
                                false);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            let message = Object.values(errors).flat().join("\n");
                            alert(message);
                        } else {
                            alert(xhr.responseJSON?.message || 'Error saving data');
                        }
                        $btn.html('{{ __('messages.common.submit') }}').attr('disabled',
                            false);
                    }
                });
            });
        });
    </script>
@endsection
