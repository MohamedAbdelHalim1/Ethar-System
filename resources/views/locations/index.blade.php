<x-app-layout>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">All Locations</h2>

            <form method="GET" action="{{ route('locations.index') }}" class="mb-4 flex items-center gap-4">
                <div>
                    <label for="from" class="block text-sm font-medium">From</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                        class="border rounded px-2 py-1">
                </div>

                <div>
                    <label for="to" class="block text-sm font-medium">To</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                        class="border rounded px-2 py-1">
                </div>

                <div>
                    <label for="availability" class="block text-sm font-medium">Availability</label>
                    <select name="availability" id="availability" class="border rounded px-2 py-1">
                        <option value="">-- All --</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>
                            Available</option>
                        <option value="not_available"
                            {{ request('availability') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-6">
                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Filter</button>
                    <a href="{{ route('locations.index') }}" class="bg-gray-300 px-3 py-1 rounded">Reset</a>
                </div>
            </form>

            <table id="locations-table" class="min-w-full">
                <thead>
                    <tr>
                        <th>Location Number</th>
                        <th class="text-left">Upcoming Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        @php
                            $upcomingBookings = $location->brands
                                ->filter(fn($b) => \Carbon\Carbon::parse($b->pivot->start_date)->isFuture())
                                ->count();
                        @endphp
                        <tr>
                            <td>{{ $location->number }}</td>
                            <td class="text-left">{{ $upcomingBookings }}</td>
                            <td>
                                <a href="{{ route('locations.calendar', $location->id) }}"
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                                    📅 Calendar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                const table3 = document.getElementById('locations-table');
                if (table3) {
                    $('#locations-table').DataTable();
                }
            });
        </script>
    @endpush


</x-app-layout>
