<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="container">
                    <h1 class="text-2xl font-bold mb-4">Liste des Églises</h1>

                    @if (session('success'))
                        <div class="alert alert-success mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('churches.create') }}" class="">Ajouter une Église</a>

                    @if ($churches->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-700 mb-3">Listes des Églises</h3>
                            <table class="min-w-full table-auto bg-white shadow rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-600">Nom</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Email</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Téléphone</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Adresse</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Image</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($churches as $church)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $church->name }}</td>
                                            <td class="px-4 py-2">{{ $church->email }}</td>
                                            <td class="px-4 py-2">{{ $church->phone }}</td>
                                            <td class="px-4 py-2">{{ $church->adresse }}</td>
                                            <td class="px-4 py-2"><img
                                                    src="{{ asset('storage/logos/' . basename($church->logo)) }}"
                                                    alt="Image" class="w-10 h-10 rounded-full object-cover"></td>
                                            <td class="px-4 py-2 flex space-x-2">
                                                <a href="{{ route('churches.edit', $church) }}"
                                                    class="">Éditer</a>
                                                <form action="{{ route('churches.destroy', $church) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- @if ($churchesSecondaires->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-700 mb-3">Église Principale
                                Principal</h3>
                            <table class="min-w-full table-auto bg-white shadow rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-600">Nom</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Email</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Téléphone</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Adresse</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Image</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($churchesSecondaires as $church)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $church->name }}</td>
                                            <td class="px-4 py-2">{{ $church->email }}</td>
                                            <td class="px-4 py-2">{{ $church->phone }}</td>
                                            <td class="px-4 py-2">{{ $church->adresse }}</td>
                                            <td class="px-4 py-2"><img
                                                    src="{{ asset('storage/logos/' . basename($church->logo)) }}"
                                                    alt="Image" class="w-10 h-10 rounded-full object-cover"></td>
                                            <td class="px-4 py-2 flex space-x-2">
                                                <a href="{{ route('churches.edit', $church) }}"
                                                    class="">Éditer</a>
                                                <form action="{{ route('churches.destroy', $church) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif --}}

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
