<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Edit Brand</h2>
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('brands.update', $brand->id) }}" x-data="{
                showDates: true,
                type: '{{ old('type', $brand->type) }}'
            }">
                @csrf
                @method('PUT')

                <!-- Brand Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Brand Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $brand->name) }}"
                        class="w-full border rounded px-3 py-2" required>
                </div>

                <!-- Owner Name -->
                <div class="mb-4">
                    <label for="owner_name" class="block text-gray-700">Owner Name</label>
                    <input type="text" name="owner_name" id="owner_name"
                        value="{{ old('owner_name', $brand->owner_name) }}" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Owner Phone -->
                <div class="mb-4">
                    <label for="owner_phone" class="block text-gray-700">Owner Phone</label>
                    <input type="text" name="owner_phone" id="owner_phone"
                        value="{{ old('owner_phone', $brand->owner_phone) }}" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $brand->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Dropdown -->
                <div class="mb-4">
                    <label for="type" class="block text-gray-700">Type</label>
                    <select name="type" id="type" x-model="type" class="w-full border rounded px-3 py-2">
                        <option value="">Select Type</option>
                        <option value="rent" {{ old('type', $brand->type) == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="percentage" {{ old('type', $brand->type) == 'percentage' ? 'selected' : '' }}>
                            Percentage</option>
                    </select>
                </div>

                <!-- Rent Value -->
                <div class="mb-4" x-show="type == 'rent'" x-transition>
                    <label for="rent_value" class="block text-gray-700">Enter Rent Value</label>
                    <input type="number" step="0.01" name="rent_value" id="rent_value"
                        value="{{ old('rent_value', $brand->rent_value) }}" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Percentage Value -->
                <div class="mb-4" x-show="type == 'percentage'" x-transition>
                    <label for="percentage_value" class="block text-gray-700">Enter Percentage Value</label>
                    <input type="number" step="0.01" name="percentage_value" id="percentage_value"
                        value="{{ old('percentage_value', $brand->percentage_value) }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <!-- Duration -->
                <div class="mb-4">
                    <label for="subscription_duration" class="block text-gray-700">Duration</label>
                    <select name="subscription_duration" id="subscription_duration"
                        class="w-full border rounded px-3 py-2" @change="showDates = ($event.target.value !== '')"
                        required>
                        <option value="">Select a duration</option>
                        @foreach ($durations as $key => $label)
                            <option value="{{ $key }}"
                                {{ $brand->subscription_duration == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- Date Range -->
                <div x-show="showDates" class="mb-4">
                    <label for="date_range" class="block text-gray-700">Select Date Range</label>
                    <input type="text" id="date_range" name="date_range"
                        value="{{ \Carbon\Carbon::parse($brand->start_date)->format('Y-m-d') . ' to ' . \Carbon\Carbon::parse($brand->end_date)->format('Y-m-d') }}"
                        class="w-full border rounded px-3 py-2" placeholder="Pick start and end dates">
                </div>

                <!-- Location -->
                <div class="mb-4">
                    <label for="location_id" class="block text-gray-700">Location</label>
                    <select name="location_id" id="location_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a location</option>
                        @foreach ($availableLocations as $location)
                            @php
                                $label = $location->number;

                                $bookings = collect($location->brands)->sortBy('pivot.start_date')->values();

                                $nextAvailableDate = now();

                                foreach ($bookings as $booking) {
                                    $start = \Carbon\Carbon::parse($booking->pivot->start_date);
                                    $end = \Carbon\Carbon::parse($booking->pivot->end_date);

                                    if ($start->gt($nextAvailableDate)) {
                                        // في فراغ بين الوقت المتاح والحجز اللي جاي
                                        break;
                                    }

                                    if ($end->gte($nextAvailableDate)) {
                                        // المكان مشغول، فحنأجل التاريخ لبعد نهاية الحجز
                                        $nextAvailableDate = $end->copy()->addDay();
                                    }
                                }

                                $label .= ' (available from ' . $nextAvailableDate->toDateString() . ')';
                            @endphp


                            <option value="{{ $location->id }}"
                                {{ $brand->locations->first()?->id == $location->id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- Drive Link -->
                <div class="mb-4">
                    <label for="drive_link" class="block text-gray-700">Drive Link</label>
                    <input type="url" name="drive_link" id="drive_link" value="{{ $brand->drive_link }}"
                        class="w-full border rounded px-3 py-2" placeholder="https://drive.google.com/..." />
                </div>

                <!-- Sales Name -->
                <div class="mb-4">
                    <label for="sales_name" class="block text-gray-700">Sales Name</label>
                    <input type="text" name="sales_name" id="sales_name"
                        value="{{ old('sales_name', $brand->sales_name) }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 px-4 py-2 rounded me-2">Cancel</a>
                    <button type="submit" class="bg-sky-500 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
