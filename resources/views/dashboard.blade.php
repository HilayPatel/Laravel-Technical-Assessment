@extends('layouts.admin')

@section('content')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Shopify Product list</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
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
                            <h3 class="card-title">Product List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Vendor</th>
                                        <th>Tags</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="border-b border-gray-100 text-gray-700 text-sm">
                                            <td class="p-4 font-medium">{{ $product->title }}</td>
                                            <td class="p-4">{{ $product->importRecord->sku ?? 'N/A' }}</td>
                                            <td class="p-4">{{ $product->product_type }}</td>
                                            <td class="p-4">${{ number_format($product->variant_price, 2) }}</td>
                                            <td class="p-4">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                {{ $product->importRecord && $product->importRecord->status == 'successful' ? 'badge text-bg-success' : '' }}
                                                {{ $product->importRecord && $product->importRecord->status == 'failed' ? 'badge text-bg-danger' : '' }}">
                                                    {{ ucfirst($product->importRecord ? $product->importRecord->status : '') }}
                                                </span>
                                            </td>
                                            <td class="p-4">{{ $product->vendor }}</td>
                                            <td class="p-4">
                                                @foreach (explode(',', $product->tags) as $tag)
                                                    <span class="badge text-bg-secondary">{{ $tag }}</span>
                                                @endforeach
                                            </td>
                                            <td class="p-4">
                                                {{ $product->importRecord ? $product->importRecord->created_at->format('M d, Y H:i') : 'N/A' }}
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
