<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du Don') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="container">
                    <h1 class="text-2xl font-bold mb-4">{{ __('Détails du Don') }}</h1>

                    <div class="mb-4">
                        <a href="{{ route('dons.index') }}" class="btn btn-secondary mb-3">{{ __('Retour à la liste') }}</a>
                    </div>

                    <!-- Affichage des détails du don -->
                    <div class="mb-4">
                        <strong>{{ __('Titre') }}: </strong>{{ $don->title }}<br>
                        <strong>{{ __('Description') }}: </strong>{{ $don->description ?? __('Aucune description') }}<br>
                        <strong>{{ __('Objectif') }}: </strong>{{ number_format($don->objectif, 2, ',', ' ') }} CFA<br>
                        <strong>{{ __('Statut') }}: </strong>
                        @if ($don->status == 'pending')
                            <span class="badge bg-warning">{{ __('En attente') }}</span>
                        @elseif ($don->status == 'active')
                            <span class="badge bg-success">{{ __('Actif') }}</span>
                        @elseif ($don->status == 'closed')
                            <span class="badge bg-danger">{{ __('Clôturé') }}</span>
                        @endif
                        <br>
                        <strong>{{ __('Date de fin') }}: </strong>{{ \Carbon\Carbon::parse($don->end_at)->format('d-m-Y') }}<br>
                    </div>

                    <!-- Affichage des utilisateurs associés -->
                    <h3 class="text-xl font-bold mb-4">{{ __('Utilisateurs ayant effectué ce don') }}</h3>
                    @if ($don->users->isEmpty())
                        <p>{{ __('Aucun utilisateur n\'a encore effectué ce don.') }}</p>
                    @else
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Nom de l\'utilisateur') }}</th>
                                    <th>{{ __('Référence de paiement') }}</th>
                                    <th>{{ __('Date de paiement') }}</th>
                                    <th>{{ __('Montant du paiement') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($don->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->pivot->reference_paiement }}</td>
                                        <td>{{ \Carbon\Carbon::parse($user->pivot->date_paiement)->format('d-m-Y') }}</td>
                                        <td>{{ number_format($user->pivot->montant_paiement, 2, ',', ' ') }} CFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
