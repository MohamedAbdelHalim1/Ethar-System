<x-app-layout>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">All Locations</h2>

            <table class="min-w-full">
                <thead>
                    <tr>
                        <th>Location Number</th>
                        <th>Upcoming Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                        @php
                            $upcomingBookings = $location->brands->filter(fn($b) => \Carbon\Carbon::parse($b->pivot->start_date)->isFuture())->count();
                        @endphp
                        <tr>
                            <td>{{ $location->number }}</td>
                            <td>{{ $upcomingBookings }}</td>
                            <td>
                                <a href="{{ route('locations.calendar', $location->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                                    📅 Calendar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
