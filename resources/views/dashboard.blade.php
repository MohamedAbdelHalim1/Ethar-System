<x-app-layout>
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
            <div class="bg-white shadow-md rounded p-4 overflow-x-auto">
                <table id="brands-table" class="min-w-[1500px] whitespace-nowrap text-sm">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left">Name</th>
                            <th class="px-2 py-2 text-left">Owner Name</th>
                            <th class="px-2 py-2 text-left">Owner Phone</th>
                            <th class="px-2 py-2 text-left">Type</th>
                            <th class="px-2 py-2 text-left">Value</th>
                            <th class="px-2 py-2 text-left">Sales Name</th>
                            <th class="px-2 py-2 text-left">Category</th>
                            <th class="px-2 py-2 text-left">Duration</th>
                            <th class="px-2 py-2 text-left">Start Date</th>
                            <th class="px-2 py-2 text-left">End Date</th>
                            <th class="px-2 py-2 text-left">Days Left</th>
                            <th class="px-2 py-2 text-left">Location</th>
                            <th class="px-2 py-2 text-left">Drive Link</th>
                            <th class="px-2 py-2 text-left">Status</th>
                            <th class="px-2 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr>
                                <td class="px-2 py-1">{{ $brand->name }}</td>
                                <td class="px-2 py-1">{{ $brand->owner_name ?? '-' }}</td>
                                <td class="px-2 py-1">{{ $brand->owner_phone ?? '-' }}</td>
                                <td class="px-2 py-1">{{ $brand->type ?? '-' }}</td>
                                <td class="px-2 py-1">
                                    @if ($brand->type === 'rent')
                                        {{ $brand->rent_value ? number_format($brand->rent_value, 2) : '-' }}
                                    @elseif ($brand->type === 'percentage')
                                        {{ $brand->percentage_value ? number_format($brand->percentage_value, 2) . ' %' : '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-2 py-1">{{ $brand->sales_name ?? '-' }}</td>
                                <td class="px-2 py-1">{{ $brand->category->name ?? '-' }}</td>
                                <td class="px-2 py-1">{{ $brand->subscription_duration }}</td>
                                <td class="px-2 py-1">{{ $brand->start_date }}</td>
                                <td class="px-2 py-1">{{ $brand->end_date }}</td>

                                @php
                                    $daysLeft = today()->diffInDays(
                                        \Carbon\Carbon::parse($brand->end_date)->startOfDay(),
                                        false,
                                    );
                                @endphp

                                <td class="px-2 py-1">
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

                                <td class="px-2 py-1">
                                    @if ($brand->locations->count())
                                        {{ $brand->locations->pluck('number')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-2 py-1 text-center">
                                    @if ($brand->drive_link)
                                        <a href="{{ $brand->drive_link }}" target="_blank"
                                            class="text-blue-500 hover:text-blue-700">
                                            🔗
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-2 py-1">
                                    @if ($brand->status === 'new')
                                        <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs">New</span>
                                    @elseif ($brand->status === 'active')
                                        <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Active</span>
                                    @elseif ($brand->status === 'inactive')
                                        <span class="bg-gray-400 text-white px-2 py-1 rounded text-xs">Inactive</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">-</span>
                                    @endif
                                </td>

                                <td class="flex gap-2">
                                    <!-- Edit button -->
                                    <a href="{{ route('brands.edit', $brand->id) }}"
                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                                        Edit
                                    </a>

                                    <!-- Delete button with confirmation -->
                                    <form method="POST" action="{{ route('brands.destroy', $brand->id) }}"
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


</x-app-layout>
