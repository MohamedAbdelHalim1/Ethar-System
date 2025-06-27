<x-app-layout>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

            <!-- جدول البراندات -->
            <div class="table-responsive">
                <table id="brands-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Owner Name</th>
                            <th>Owner Phone</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Sales Name</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days Left</th>
                            <th>Location</th>
                            <th>Drive Link</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                                        <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs">New</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">-</span>
                                    @endif
                                </td>

                                <td class="text-nowrap">
                                    <!-- Edit button -->
                                    <a href="{{ route('brands.edit', $brand->id) }}"
                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                                        Edit
                                    </a>

                                    <!-- Delete button with confirmation -->
                                    <form class="d-inline" method="POST" action="{{ route('brands.destroy', $brand->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this brand?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition">
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
    @endpush
</x-app-layout>
