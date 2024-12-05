<!-- resources/views/sujets/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du Sujet de Discussion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h3 class="text-xl font-semibold mb-4">{{ $sujet->theme }}</h3>
                <p class="text-sm mb-4"><strong>Date :</strong> {{ $sujet->date }}</p>
                <p class="mb-4"><strong>Description :</strong> {{ $sujet->body }}</p>

                {{-- <a href="{{ route('sujets.edit', $sujet) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Modifier</a> --}}

                {{-- <form action="{{ route('sujets.destroy', $sujet) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Supprimer</button>
                </form> --}}

            </div>
        </div>
    </div>
</x-app-layout>
