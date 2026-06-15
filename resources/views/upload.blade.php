@extends('layouts.admin')

@section('content')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Shopify Product Importer</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Upload</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-md-6">
                    <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4">
                        <!--begin::Header-->
                        <div class="card-header">
                            <div class="card-title">Upload Product CSV</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form action="{{ route('post-upload') }}" method="POST" enctype="multipart/form-data">
                            <!--begin::Body-->
                            @csrf
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="file" name="csv_file" class="form-control" id="csv_file" />
                                    <label class="input-group-text" for="csv_file">Upload</label>
                                </div>
                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer">
                                <input type="submit" class="btn btn-primary" value="Import Products" />
                            </div>
                            <!--end::Footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Quick Example-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Import Status</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="p-4">File Name</th>
                                        <th class="p-4">Queue Status</th>
                                        <th class="p-4">Total Rows</th>
                                        <th class="p-4">Uploaded At</th>
                                        <th class="p-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($uploads as $upload)
                                        <tr class="border-b border-gray-100 text-gray-700 text-sm">
                                            <td class="p-4 font-medium">{{ $upload->file_name }}</td>
                                            <td class="p-4">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                {{ $upload->status == 'completed' ? 'badge text-bg-success' : '' }}
                                                {{ $upload->status == 'processing' ? 'badge text-bg-primary' : '' }}
                                                {{ $upload->status == 'pending' ? 'badge text-bg-warning' : '' }}
                                                {{ $upload->status == 'failed' ? 'badge text-bg-danger' : '' }}">
                                                    {{ ucfirst($upload->status) }}
                                                </span>
                                            </td>
                                            <td class="p-4">{{ $upload->total_rows }}</td>
                                            <td class="p-4">{{ $upload->created_at->format('M d, Y H:i') }}</td>
                                            <td class="p-4">
                                                <a href="{{ route('logs', $upload->id) }}" class="btn btn-info btn-sm">View
                                                    Logs</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
@endsection
