<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Add New Brand</h2>
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('brands.store') }}" x-data="{ showDates: false }">
                @csrf

                <!-- Brand Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Brand Name</label>
                    <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2"
                        required>
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Duration -->
                <div class="mb-4">
                    <label for="duration" class="block text-gray-700">Duration</label>
                    <select name="subscription_duration" id="duration" class="w-full border rounded px-3 py-2"
                        @change="showDates = ($event.target.value !== '')" required>
                        <option value="">Select a duration</option>
                        @foreach ($durations as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('subscription_duration') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div x-show="showDates" class="mb-4">
                    <label for="date_range" class="block text-gray-700">Select Date Range</label>
                    <input type="text" id="date_range" name="date_range" class="w-full border rounded px-3 py-2"
                        placeholder="Pick start and end dates">
                </div>


                <!-- Location -->
                <div class="mb-4">
                    <label for="location_id" class="block text-gray-700">Location</label>
                    <select name="location_id" id="location_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a location</option>
                        @foreach ($availableLocations as $location)
                            @php
                                $label = $location->number;
                                $bookings = $location->brands->sortBy('pivot.start_date')->values();

                                if ($bookings->isNotEmpty()) {
                                    $nextAvailableDate = now();

                                    foreach ($bookings as $booking) {
                                        $start = \Carbon\Carbon::parse($booking->pivot->start_date);
                                        $end = \Carbon\Carbon::parse($booking->pivot->end_date);

                                        if ($start->gt(now())) {
                                            if ($nextAvailableDate->lt($start)) {
                                                $label .=
                                                    ' (available from ' . $nextAvailableDate->toDateString() . ')';
                                                break;
                                            } else {
                                                $nextAvailableDate = $end->copy()->addDay();
                                            }
                                        } elseif ($end->gt(now())) {
                                            $nextAvailableDate = $end->copy()->addDay();
                                        }
                                    }

                                    if (!str_contains($label, 'available')) {
                                        $label .= ' (available from ' . $nextAvailableDate->toDateString() . ')';
                                    }
                                }
                            @endphp

                            <option value="{{ $location->id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Drive Link -->
                <div class="mb-4">
                    <label for="drive_link" class="block text-gray-700">Drive Link</label>
                    <input type="url" name="drive_link" id="drive_link" class="w-full border rounded px-3 py-2"
                        placeholder="https://drive.google.com/..." />
                </div>


                <!-- Buttons -->
                <div class="flex justify-end">
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 px-4 py-2 rounded me-2">Cancel</a>
                    <button type="submit" class="bg-sky-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
