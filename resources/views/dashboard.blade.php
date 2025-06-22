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
            <div class="bg-white shadow-md rounded p-4">
                <table id="brands-table" class="min-w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days Left</th>
                            <th>Location</th>
                            <th>Drive Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->category->name ?? '-' }}</td>
                                <td>{{ $brand->subscription_duration }}</td>
                                <td>{{ $brand->start_date }}</td>
                                <td>{{ $brand->end_date }}</td>
                                @php
                                    $daysLeft = today()->diffInDays(
                                        \Carbon\Carbon::parse($brand->end_date)->startOfDay(),
                                        false,
                                    );

                                @endphp
                                <td>
                                    @if ($daysLeft <= 0)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Expired</span>
                                    @elseif ($daysLeft <= 1)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">{{ $daysLeft }}
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


                                <td>
                                    @if ($brand->locations->count())
                                        {{ $brand->locations->pluck('number')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($brand->drive_link)
                                        <a href="{{ $brand->drive_link }}" target="_blank"
                                            class="text-blue-500 hover:text-blue-700">
                                            🔗
                                        </a>
                                    @else
                                        -
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
