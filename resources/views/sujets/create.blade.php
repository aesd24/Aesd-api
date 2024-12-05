<!-- resources/views/sujets/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un Sujet de Discussion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <form action="{{ route('sujets.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="theme" class="block text-sm font-medium text-gray-700">Thème</label>
                        <input type="text" name="theme" id="theme" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="date" id="date" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="body" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="body" id="body" class="mt-1 block w-full" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="">Créer</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
