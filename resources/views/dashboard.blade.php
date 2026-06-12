@extends('layouts.admin')

@section('content')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard v3</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard v3</li>
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
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Online Store Visitors</h3>
                                <a href="javascript:void(0);"
                                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">View
                                    Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span class="fw-bold fs-5">820</span> <span>Visitors Over Time</span>
                                </p>
                                <p class="ms-auto d-flex flex-column text-end">
                                    <span class="text-success"> <i class="bi bi-arrow-up"></i> 12.5% </span>
                                    <span class="text-secondary">Since last week</span>
                                </p>
                            </div>
                            <!-- /.d-flex -->
                            <div class="position-relative mb-4">
                                <div id="visitors-chart"></div>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="me-2">
                                    <i class="bi bi-square-fill text-primary"></i> This Week
                                </span>
                                <span> <i class="bi bi-square-fill text-secondary"></i> Last Week </span>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <h3 class="card-title">Products</h3>
                            <div class="card-tools">
                                <a href="#" class="btn btn-tool btn-sm"> <i class="bi bi-download"></i> </a>
                                <a href="#" class="btn btn-tool btn-sm"> <i class="bi bi-list"></i> </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Sales</th>
                                        <th>More</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="./assets/img/default-150x150.png" alt="Product 1"
                                                class="rounded-circle img-size-32 me-2" />
                                            Some Product
                                        </td>
                                        <td>$13 USD</td>
                                        <td>
                                            <small class="text-success me-1">
                                                <i class="bi bi-arrow-up"></i>
                                                12%
                                            </small>
                                            12,000 Sold
                                        </td>
                                        <td>
                                            <a href="#" class="text-secondary"> <i class="bi bi-search"></i> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="./assets/img/default-150x150.png" alt="Product 1"
                                                class="rounded-circle img-size-32 me-2" />
                                            Another Product
                                        </td>
                                        <td>$29 USD</td>
                                        <td>
                                            <small class="text-info me-1">
                                                <i class="bi bi-arrow-down"></i>
                                                0.5%
                                            </small>
                                            123,234 Sold
                                        </td>
                                        <td>
                                            <a href="#" class="text-secondary"> <i class="bi bi-search"></i> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="./assets/img/default-150x150.png" alt="Product 1"
                                                class="rounded-circle img-size-32 me-2" />
                                            Amazing Product
                                        </td>
                                        <td>$1,230 USD</td>
                                        <td>
                                            <small class="text-danger me-1">
                                                <i class="bi bi-arrow-down"></i>
                                                3%
                                            </small>
                                            198 Sold
                                        </td>
                                        <td>
                                            <a href="#" class="text-secondary"> <i class="bi bi-search"></i> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="./assets/img/default-150x150.png" alt="Product 1"
                                                class="rounded-circle img-size-32 me-2" />
                                            Perfect Item
                                            <span class="badge text-bg-danger">NEW</span>
                                        </td>
                                        <td>$199 USD</td>
                                        <td>
                                            <small class="text-success me-1">
                                                <i class="bi bi-arrow-up"></i>
                                                63%
                                            </small>
                                            87 Sold
                                        </td>
                                        <td>
                                            <a href="#" class="text-secondary"> <i class="bi bi-search"></i> </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
@endsection
{{--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopify Product Importer Dashboard</title>
    <script src="https://tailwindcss.com"></script>
</head>

<body class="bg-gray-50 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">CSV to Shopify Engine</h1>

        <!-- File Upload Form Component -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700">Upload New Product CSV</h2>
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data"
                class="flex gap-4 items-center">
                @csrf
                <input type="file" name="csv_file" required class="border p-2 rounded w-full text-sm">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded font-medium hover:bg-indigo-700 transition">Import</button>
            </form>
        </div>

        <!-- History Operational Logs Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm font-semibold border-b border-gray-200">
                        <th class="p-4">File Name</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Total Rows</th>
                        <th class="p-4">Uploaded At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uploads as $upload)
                        <tr class="border-b border-gray-100 text-gray-700 text-sm">
                            <td class="p-4 font-medium">{{ $upload->file_name }}</td>
                            <td class="p-4">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold
                                {{ $upload->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $upload->status == 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $upload->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $upload->status == 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($upload->status) }}
                                </span>
                            </td>
                            <td class="p-4">{{ $upload->total_rows }}</td>
                            <td class="p-4">{{ $upload->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html> --}}
