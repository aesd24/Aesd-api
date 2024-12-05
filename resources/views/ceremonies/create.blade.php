{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">


                <div class="container">
                    <h1>Créer une nouvelle Cérémonie</h1>

                    <form action="{{ route('ceremonies.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="title">Titre de la Cérémonie</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="event_date">Date de l'événement</label>
                            <input type="date" name="event_date" id="event_date" class="form-control"
                                value="{{ old('event_date') }}" required>
                        </div>

                        <h3>Associer des Églises</h3>
                        @foreach ($churches as $church)
                            <div class="form-group">
                                <label for="event_date">choix eglise</label>
                                <input type="checkbox" name="churches[]" value="{{ $church->id }}">
                                <label>{{ $church->name }}</label>

                            </div>


                            <div class="form-group">
                                <label for="event_date">Période</label>
                                <input type="datetime-local" name="periode_times[]" class="form-control"
                                    placeholder="Période associée (ex: 2024-10-24T10:00)">
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary mt-3">Créer</button>
                    </form>
                </div>



            </div>
        </div>
    </div>
</x-app-layout> --}}


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
                    <h1 class="text-2xl font-semibold mb-6">Créer une nouvelle Cérémonie</h1>

                    <form action="{{ route('ceremonies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Champ Titre -->
                        <div class="form-group mb-4">
                            <label for="title" class="block text-gray-700">Titre de la Cérémonie</label>
                            <input type="text" name="title" id="title" class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                   value="{{ old('title') }}" required>
                            @if ($errors->has('title'))
                                <div class="text-red-500 mt-1">{{ $errors->first('title') }}</div>
                            @endif
                        </div>

                        <!-- Champ Description -->
                        <div class="form-group mb-4">
                            <label for="description" class="block text-gray-700">Description</label>
                            <textarea name="description" id="description" class="form-control border border-gray-300 rounded px-4 py-2 w-full">{{ old('description') }}</textarea>
                            @if ($errors->has('description'))
                                <div class="text-red-500 mt-1">{{ $errors->first('description') }}</div>
                            @endif
                        </div>

                        <!-- Champ Date de l'événement -->
                        <div class="form-group mb-4">
                            <label for="event_date" class="block text-gray-700">Date de l'événement</label>
                            <input type="date" name="event_date" id="event_date" class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                   value="{{ old('event_date') }}" required>
                            @if ($errors->has('event_date'))
                                <div class="text-red-500 mt-1">{{ $errors->first('event_date') }}</div>
                            @endif
                        </div>

                        <!-- Champ unique pour la période -->
                        <div class="form-group mb-4">
                            <label for="periode_time" class="block text-gray-700">Période de la cérémonie</label>
                            <input type="datetime-local" name="periode_time" id="periode_time" class="form-control border border-gray-300 rounded px-4 py-2 w-full"
                                   value="{{ old('periode_time') }}" required>
                            @if ($errors->has('periode_time'))
                                <div class="text-red-500 mt-1">{{ $errors->first('periode_time') }}</div>
                            @endif
                        </div>

                        <!-- Champ pour le média -->
                        <div class="form-group mb-4">
                            <label for="media" class="block text-gray-700">Média (images ou vidéos)</label>
                            <input type="file" name="media" id="media" class="form-control border border-gray-300 rounded px-4 py-2 w-full" accept="image/*,video/*" required>
                            @if ($errors->has('media'))
                                <div class="text-red-500 mt-1">{{ $errors->first('media') }}</div>
                            @endif
                        </div>

                        <!-- Section pour les Églises associées -->
                        <h3 class="text-xl font-semibold mt-6 mb-4">Associer des Églises</h3>
                        @foreach ($churches as $church)
                            <div class="form-group mb-2">
                                <input type="checkbox" name="churches[]" value="{{ $church->id }}" id="church_{{ $church->id }}" class="mr-2">
                                <label for="church_{{ $church->id }}" class="text-gray-700">{{ $church->name }}</label>
                            </div>
                        @endforeach

                        <!-- Bouton de soumission -->
                        <button type="submit" class=" px-4 py-2 rounded">Créer</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
