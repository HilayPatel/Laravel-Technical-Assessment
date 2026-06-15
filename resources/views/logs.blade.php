@extends('layouts.admin')

@section('content')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Logs Entries</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logs</li>
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
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Log Entries</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Log Message</th>
                                        <th>Level</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td>{{ $log->message }}</td>
                                            <td>
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                {{ $log->level == 'debug' ? 'badge text-bg-success' : '' }}
                                                {{ $log->level == 'info' ? 'badge text-bg-info' : '' }}
                                                {{ $log->level == 'warning' ? 'badge text-bg-primary' : '' }}
                                                {{ $log->level == 'critical' ? 'badge text-bg-warning' : '' }}
                                                {{ $log->level == 'error' ? 'badge text-bg-danger' : '' }}">
                                                    {{ ucfirst($log->level) }}
                                                </span>
                                            </td>
                                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
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
