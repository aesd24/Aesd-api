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
                    <h1 class="text-2xl font-bold mb-4">Liste des dont</h1>

                    @if (session('success'))
                        <div class="alert alert-success mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- <a href="{{ route('churches.create') }}" class="">faire un don</a> --}}
                    <a href="{{ route('dons.create') }}" class="btn btn-primary mb-3">{{ __('Créer un nouveau don') }}</a>


                    <!-- Tableau des dons -->
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Titre') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Objectif') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th>{{ __('Date de fin') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dons as $don)
                                <tr>
                                    <td>{{ $don->id }}</td>
                                    <td>{{ $don->title }}</td>
                                    <td>{{ $don->description ?? __('Aucune description') }}</td>
                                    <td>{{ number_format($don->objectif, 2, ',', ' ') }} cfa</td>
                                    <td>
                                        @if ($don->status == 'pending')
                                            <span class="badge bg-warning">{{ __('En attente') }}</span>
                                        @elseif ($don->status == 'active')
                                            <span class="badge bg-success">{{ __('Actif') }}</span>
                                        @elseif ($don->status == 'closed')
                                            <span class="badge bg-danger">{{ __('Clôturé') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($don->end_at)->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('dons.show', $don) }}"
                                            class="btn btn-info btn-sm">{{ __('Voir') }}</a>


                                        {{-- <a href="{{ route('dons.edit', $don) }}"
                                            class="btn btn-warning btn-sm">{{ __('Modifier') }}</a>
                                        <form action="{{ route('dons.destroy', $don) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce don ?') }}')">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('Aucun don trouvé.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>



                </div>

            </div>
        </div>
    </div>
</x-app-layout>
