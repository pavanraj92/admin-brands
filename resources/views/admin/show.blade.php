@extends('admin::admin.layouts.master')

@section('title', 'Brands Management')

@section('page-title', 'Brand Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.brands.index') }}">Brand Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">Brand Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Brand Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">                    
                    <div class="table-responsive">
                         <div class="card-body">      
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Name</th>
                                        <td scope="col">{{ $brand->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Description</th>
                                        <td scope="col">{!! $brand->description ?? 'N/A' !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td scope="col"> {!! config('brand.constants.aryStatusLabel.' . $brand->status, 'N/A') !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Created At</th>
                                        <td scope="col">{{ $brand->created_at
                                            ? $brand->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                            : '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                                             
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Brand Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
