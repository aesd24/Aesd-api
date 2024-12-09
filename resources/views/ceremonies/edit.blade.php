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
                    <h1 class="text-2xl font-semibold mb-6">Modifier la Cérémonie</h1>

                    <form action="{{ route('ceremonies.update', $ceremonie->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- Utilisation de la méthode PUT pour la mise à jour -->

                        <!-- Champ Titre -->
                        <div class="form-group mb-4">
                            <label for="title" class="block text-gray-700">Titre de la Cérémonie</label>
                            <input type="text" name="title" id="title"
                                class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                value="{{ old('title', $ceremonie->title) }}" required>
                            @if ($errors->has('title'))
                                <div class="text-red-500 mt-1">{{ $errors->first('title') }}</div>
                            @endif
                        </div>

                        <!-- Champ Description -->
                        <div class="form-group mb-4">
                            <label for="description" class="block text-gray-700">Description</label>
                            <textarea name="description" id="description" class="form-control border border-gray-300 rounded px-4 py-2 w-full">{{ old('description', $ceremonie->description) }}</textarea>
                            @if ($errors->has('description'))
                                <div class="text-red-500 mt-1">{{ $errors->first('description') }}</div>
                            @endif
                        </div>

                        <!-- Champ Date de l'événement -->
                        <div class="form-group mb-4">
                            <label for="event_date" class="block text-gray-700">Date de l'événement</label>
                            <input type="date" name="event_date" id="event_date"
                                class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                value="{{ old('event_date', $ceremonie->event_date) }}" required>
                            @if ($errors->has('event_date'))
                                <div class="text-red-500 mt-1">{{ $errors->first('event_date') }}</div>
                            @endif
                        </div>

                        <!-- Champ pour le média -->
                        <div class="form-group mb-4">
                            <label for="media" class="block text-gray-700">Média (images ou vidéos)</label>
                            <input type="file" name="media" id="media"
                                class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                accept="image/*,video/*">
                            @if ($errors->has('media'))
                                <div class="text-red-500 mt-1">{{ $errors->first('media') }}</div>
                            @endif
                            <small class="text-gray-500">Laissez vide si vous ne souhaitez pas changer le média
                                actuel.</small>
                            @if ($ceremonie->media)
                                <div class="mt-2">
                                    <strong>Média actuel :</strong>
                                    @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $ceremonie->media))
                                        <img class="w-24 h-24 object-cover rounded"
                                            src="{{ asset('storage/media/' . basename($ceremonie->media)) }}"
                                            alt="Image actuelle">
                                    @elseif (preg_match('/\.(mp4|mov|wmv)$/i', $ceremonie->media))
                                        <video width="120" height="80" controls>
                                            <source src="{{ asset('storage/media/' . basename($ceremonie->media)) }}"
                                                type="video/mp4">
                                            Votre navigateur ne prend pas en charge la balise vidéo.
                                        </video>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Section pour les Églises associées -->
                        <h3 class="text-xl font-semibold mt-6 mb-4">Associer une Église</h3>
                        @foreach ($churches as $church)
                            <div class="form-group mb-2">
                                <input type="radio" name="id_eglise" value="{{ $church->id }}"
                                    id="church_{{ $church->id }}" class="mr-2"
                                    {{ $ceremonie->churches->pluck('id')->contains($church->id) ? 'checked' : '' }}>
                                <label for="church_{{ $church->id }}"
                                    class="text-gray-700">{{ $church->name }}</label>
                            </div>
                        @endforeach
                        @if ($errors->has('id_eglise'))
                            <div class="text-red-500 mt-1">{{ $errors->first('id_eglise') }}</div>
                        @endif


                        <!-- Bouton de soumission -->
                        <button type="submit" class="">Mettre
                            à jour</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
