<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Categories
        </h2>
    </x-slot> --}}

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 relative">
                {{ session('success') }}
                <button @click="show = false"
                    class="absolute top-0 right-0 mt-2 me-2 text-green-700 hover:text-green-900 text-lg font-bold">
                    &times;
                </button>
            </div>
        @endif


        <div class="flex justify-end mb-4">
            <form method="POST" action="{{ route('categories.store') }}" class="flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="Enter New Category" class="border rounded px-2 py-1">
                <button type="submit" class="bg-sky-500 text-white px-4 py-1 rounded">Add</button>
            </form>
        </div>

        <div class="bg-white shadow-md rounded p-4">
            <table id="categories-table" class="min-w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="flex gap-2 text-center">

                                <!-- Modal -->
                                <div x-data="{ open: false }">
                                    <button @click="open = true"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Edit
                                    </button>

                                    <div x-show="open" x-cloak
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                        <div class="bg-white p-6 rounded shadow w-96">
                                            <h2 class="text-lg font-semibold mb-4">Edit Category</h2>
                                            <form method="POST"
                                                action="{{ route('categories.update', $category->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $category->name }}"
                                                    class="w-full border rounded px-2 py-1 mb-4">
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="open = false"
                                                        class="bg-gray-300 px-3 py-1 rounded">Cancel</button>
                                                    <button type="submit"
                                                        class="bg-sky-500 text-white px-4 py-1 rounded">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('categories.destroy', $category->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
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

    
</x-app-layout>
