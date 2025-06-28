<x-app-layout>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

        <style>
            .table-responsive::-webkit-scrollbar {
                height: 6px;
            }

            .table-responsive::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #0ea5e9;
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #0369a1;
            }
        </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- زرار إضافة براند -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('brands.create') }}"
                    class="bg-sky-500 text-white px-4 py-2 rounded hover:bg-sky-600 transition">
                    + Add New Brand
                </a>
            </div>

            <!-- فلتر الحالة -->
            <form method="GET" action="{{ route('dashboard') }}" class="row g-3 mb-4 align-items-end">

                <div class="col-auto">
                    <label for="status" class="form-label mb-0">Filter by Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- All --</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="Contract Done" {{ request('status') == 'Contract Done' ? 'selected' : '' }}>
                            Contract Done</option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>
                </div>

                <div class="col-auto">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>


            <!-- جدول البراندات -->
            <div class="table-responsive">
                <table id="brands-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Owner Name</th>
                            <th class="text-nowrap">Owner Phone</th>
                            <th class="text-nowrap">Type</th>
                            <th class="text-nowrap">Value</th>
                            <th class="text-nowrap">Sales Name</th>
                            <th class="text-nowrap">Category</th>
                            <th class="text-nowrap">Duration</th>
                            <th class="text-nowrap">Start Date</th>
                            <th class="text-nowrap">End Date</th>
                            <th class="text-nowrap">Days Left</th>
                            <th class="text-nowrap">Location</th>
                            <th class="text-nowrap">Drive Link</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr>
                                <td class="text-nowrap">{{ $brand->name }}</td>
                                <td class="text-nowrap">{{ $brand->owner_name ?? '-' }}</td>
                                <td class="text-nowrap">{{ $brand->owner_phone ?? '-' }}</td>
                                <td class="text-nowrap">{{ $brand->type ?? '-' }}</td>
                                <td class="text-nowrap">
                                    @if ($brand->type === 'rent')
                                        {{ $brand->rent_value ? number_format($brand->rent_value, 2) : '-' }}
                                    @elseif ($brand->type === 'percentage')
                                        {{ $brand->percentage_value ? number_format($brand->percentage_value, 2) . ' %' : '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $brand->sales_name ?? '-' }}</td>
                                <td class="text-nowrap">{{ $brand->category->name ?? '-' }}</td>
                                <td class="text-nowrap">{{ $brand->subscription_duration }}</td>
                                <td class="text-nowrap">{{ $brand->start_date }}</td>
                                <td class="text-nowrap">{{ $brand->end_date }}</td>

                                @php
                                    $daysLeft = today()->diffInDays(
                                        \Carbon\Carbon::parse($brand->end_date)->startOfDay(),
                                        false,
                                    );
                                @endphp

                                <td class="text-nowrap">
                                    @if ($daysLeft <= 0)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Expired</span>
                                    @elseif ($daysLeft <= 1)
                                        <span
                                            class="bg-red-500 text-white px-2 py-1 rounded text-xs">{{ $daysLeft }}
                                            day left</span>
                                    @elseif ($daysLeft <= 3)
                                        <span
                                            class="bg-yellow-400 text-black px-2 py-1 rounded text-xs">{{ $daysLeft }}
                                            days left</span>
                                    @else
                                        <span
                                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">{{ $daysLeft }}
                                            days left</span>
                                    @endif
                                </td>

                                <td class="text-nowrap">
                                    @if ($brand->locations->count())
                                        {{ $brand->locations->pluck('number')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center text-nowrap">
                                    @if ($brand->drive_link)
                                        <a href="{{ $brand->drive_link }}" target="_blank"
                                            class="text-blue-500 hover:text-blue-700">
                                            🔗
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-nowrap">
                                    @if ($brand->status === 'new')
                                        <span class="badge bg-primary text-white px-2 py-1 rounded text-xs">New</span>
                                    @elseif ($brand->status === 'Contract Done')
                                        <span class="badge bg-success text-white px-2 py-1 rounded text-xs">Contract
                                            Done</span>
                                    @else
                                        <span class="badge bg-warning text-gray-800 px-2 py-1 rounded text-xs">-</span>
                                    @endif
                                </td>

                                <td class="text-nowrap">{{ $brand->created_at }}</td>

                                <td class="text-nowrap">
                                    <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    @if ($brand->status === 'new')
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#contractModal" data-brand-id="{{ $brand->id }}">
                                            Add Contract
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="contractModal" tabindex="-1"
                                            aria-labelledby="contractModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form id="contractForm" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="contractModalLabel">Upload
                                                                Contract</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="file" name="contract_file"
                                                                accept="application/pdf" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">Upload</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif ($brand->contract_file)
                                        <a href="{{ asset('assets/contract-files/' . $brand->contract_file) }}"
                                            class="btn btn-sm btn-success" target="_blank">
                                            Download Contract
                                        </a>
                                    @endif

                                    <form class="d-inline" method="POST"
                                        action="{{ route('brands.destroy', $brand->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this brand?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <!-- DataTables Buttons -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#brands-table').DataTable({
                    order: [
                        [14, 'desc']
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                    buttons: [{
                        extend: 'colvis',
                        text: 'Columns'
                    }]
                });
            });

            $(document).ready(function() {
                $('#contractModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget)
                    var brandId = button.data('brand-id')
                    var url = "{{ url('brands') }}/" + brandId + "/upload-contract";
                    $('#contractForm').attr('action', url);
                });
            });
        </script>
    @endpush



</x-app-layout>
