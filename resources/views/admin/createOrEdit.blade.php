@extends('admin::admin.layouts.master')

@section('title', 'Brand Management')

@section('page-title', isset($brand) ? 'Edit Brand' : 'Create Brand')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.brands.index') }}">Brand Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($brand) ? 'Edit Brand' : 'Create Brand' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Start Brand Content -->
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <form action="{{ isset($brand) ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}"
                      method="POST" id="brandForm">
                    @if (isset($brand))
                        @method('PUT')
                    @endif
                    @csrf
                    <div class="row">
                        <div class="col-md-6">                                
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ $brand?->name ?? old('name') }}" required>
                                @error('name')
                                    <div class="text-danger validation-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status<span class="text-danger">*</span></label>
                                <select name="status" class="form-control select2" required>
                                    <option value="1" {{ (($brand?->status ?? old('status')) == '1') ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (($brand?->status ?? old('status')) == '0') ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="text-danger validation-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="saveBtn">{{ isset($brand) ? 'Update' : 'Save' }}</button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Brand Content -->
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('backend/custom.css') }}">           
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#brandForm').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a brand name",
                        minlength: "Name must be at least 2 characters"
                    }
                },
                submitHandler: function(form) {
                    const $btn = $('#saveBtn');
                    $btn.prop('disabled', true).text($btn.text().trim().toLowerCase() === 'update' ? 'Updating...' : 'Saving...');
                    form.submit();
                },
                errorElement: 'div',
                errorClass: 'text-danger custom-error',
                errorPlacement: function(error, element) {
                    $('.validation-error').hide();
                    error.insertAfter(element);
                }
            });
        });
    </script>
@endpush
