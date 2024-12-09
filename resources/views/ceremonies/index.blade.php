<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="container">
                    <h1 class="text-2xl font-semibold mb-6">Liste des Cérémonies</h1>

                    <a href="{{ route('ceremonies.create') }}" class="btn btn-primary mb-4">Créer une nouvelle
                        cérémonie</a>

                    <table class="table mt-4 w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Titre</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Date de l'événement</th>
                                <th class="px-4 py-2">Églises</th>

                                <th class="px-4 py-2">Média</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ceremonies as $ceremonie)
                                <tr>
                                    <td class="border px-4 py-2">{{ $ceremonie->title }}</td>
                                    <td class="border px-4 py-2">{{ $ceremonie->description }}</td>
                                    <td class="border px-4 py-2">{{ $ceremonie->event_date }}</td>
                                    <td class="border px-4 py-2">
                                        @if ($ceremonie->churches)
                                            {{ $ceremonie->churches->name }}
                                        @else
                                            <span>Aucune église</span>
                                        @endif
                                    </td>

                                    <td class="border px-4 py-2">
                                        @if ($ceremonie->media)
                                            @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $ceremonie->media))
                                                <img class="w-10 h-10 rounded-full zoom-effect"
                                                    src="{{ asset('storage/media/' . basename($ceremonie->media)) }}"
                                                    alt="Image"
                                                    class="w-24 h-24 object-cover rounded-full zoom-effect">
                                            @elseif (preg_match('/\.(mp4|mov|wmv)$/i', $ceremonie->media))
                                                <video width="120" height="80" controls>
                                                    <source class="w-10 h-10 rounded-full zoom-effect"
                                                        src="{{ asset('storage/media/' . basename($ceremonie->media)) }}"
                                                        type="video/mp4">
                                                    Votre navigateur ne prend pas en charge la balise vidéo.
                                                </video>
                                            @endif
                                        @else
                                            <span>Aucun média</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{-- <a href="{{ route('ceremonies.show', $ceremonie->id) }}"
                                            class="btn btn-info">Voir</a> --}}
                                        <a href="{{ route('ceremonies.edit', $ceremonie->id) }}"
                                            class="btn btn-warning">Modifier</a>

                                        <form action="{{ route('ceremonies.destroy', $ceremonie->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
