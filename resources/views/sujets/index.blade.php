<!-- resources/views/sujets/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Liste des Sujets de Discussion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="alert alert-success" style="color: green;">
                        {{ session('success') }}
                    </div>
                @endif

                <a href="{{ route('sujets.create') }}" class="">Créer un sujet</a>

                <table class="table-auto mt-6 w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Thème</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sujets as $sujet)
                            <tr>
                                <td class="border px-4 py-2">{{ $sujet->theme }}</td>
                                <td class="border px-4 py-2">{{ $sujet->date }}</td>
                                <td class="border px-4 py-2">{{ $sujet->body }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('sujets.show', $sujet) }}" class="text-blue-500">Voir</a>
                                    |
                                    <a href="{{ route('sujets.edit', $sujet) }}" class="text-yellow-500">Modifier</a>
                                    |
                                    <form action="{{ route('sujets.destroy', $sujet) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500">Supprimer</button>
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
