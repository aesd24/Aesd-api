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




                    {{-- Formulaire de création --}}
                    <form action="{{ route('dons.store') }}" method="POST">
                        @csrf

                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Titre') }}</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <!-- Objectif -->
                        <div class="mb-3">
                            <label for="objectif" class="form-label">{{ __('Objectif') }}</label>
                            <input type="number" name="objectif" id="objectif" class="form-control"
                                value="{{ old('objectif') }}" required>
                        </div>

                        <!-- Date de fin -->
                        {{-- <div class="mb-3">
                            <label for="end_at" class="form-label">{{ __('Date de fin') }}</label>
                            <input type="date" name="end_at" id="end_at" class="form-control"
                                value="{{ old('end_at') }}" required>
                        </div> --}}

                        <!-- Statut -->
                        {{-- <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Statut') }}</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">{{ __('Sélectionnez un statut') }}</option>
                                <option value="pending" @selected(old('status') == 'pending')>{{ __('En attente') }}</option>
                                <option value="active" @selected(old('status') == 'active')>{{ __('Actif') }}</option>
                                <option value="closed" @selected(old('status') == 'closed')>{{ __('Clôturé') }}</option>
                            </select>
                        </div> --}}

                        <!-- Bouton d'envoi -->
                        <button type="submit" class="btn btn-primary">{{ __('Créer le don') }}</button>

                    </form>




                </div>

            </div>
        </div>
    </div>
</x-app-layout>
