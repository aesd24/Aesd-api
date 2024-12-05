{{-- <!-- resources/views/sujets/user_show.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du Sujet de Discussion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex flex-col space-y-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-blue-700">{{ $sujet->theme }}</h3>
                        <p class="text-sm text-gray-500">Date : {{ \Carbon\Carbon::parse($sujet->date)->format('d-m-Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600">{{ $sujet->body }}</p>
                    </div>

                    <!-- Afficher les commentaires des fidèles -->
                    <div>
                        <h4 class="font-semibold text-gray-800">Commentaires des Fidèles :</h4>
                        <ul>
                            @foreach ($sujet->fideles as $fidele)
                                <li>
                                    <strong>{{ $fidele->user->name }}</strong> (Fidèle):
                                    <em>"{{ $fidele->pivot->Comment }}"</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Afficher les commentaires des chantres -->
                    <div>
                        <h4 class="font-semibold text-gray-800">Commentaires des Chantres :</h4>
                        <ul>
                            @foreach ($sujet->chantres as $chantre)
                                <li>
                                    <strong>{{ $chantre->user->name }}</strong> (Chantre):
                                    <em>"{{ $chantre->pivot->Comment }}"</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}













<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du Sujet de Discussion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex flex-col space-y-4">


                    @if (session('success'))
                        <div class="alert alert-success" style="color: green;">
                            {{ session('success') }}
                        </div>
                    @endif


                    <div>
                        <h3 class="text-2xl font-semibold text-blue-700">{{ $sujet->theme }}</h3>
                        <p class="text-sm text-gray-500">Date :
                            {{ \Carbon\Carbon::parse($sujet->date)->format('d-m-Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600">{{ $sujet->body }}</p>
                    </div>

                    <!-- Afficher les commentaires des fidèles -->

                    <div>
                        <ul>
                            @foreach ($sujet->fideles as $fidele)
                                <li>
                                    <strong>{{ $fidele->user->name }}</strong>
                                    <em>"{{ $fidele->pivot->Comment }}"</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Afficher les commentaires des serviteurs -->
                    <div>
                        <ul>
                            @foreach ($sujet->serviteursDeDieu as $serviteur_de_dieu)
                                <li>
                                    <strong>{{ $serviteur_de_dieu->user->name }}</strong>
                                    <em>"{{ $serviteur_de_dieu->pivot->Comment }}"</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Afficher les commentaires des chantres -->

                    <div>
                        <ul>
                            @foreach ($sujet->chantres as $chantre)
                                <li>
                                    <strong>{{ $chantre->user->name }}</strong>
                                    <em>"{{ $chantre->pivot->Comment }}"</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Formulaire pour ajouter un commentaire -->
                    @if (auth()->user()->fidele || auth()->user()->chantre || auth()->user()->serviteur_de_dieu)
                        <!-- Vérification si l'utilisateur est un fidèle ou un chantre -->
                        <div>
                            <h4 class="font-semibold text-gray-800">Ajouter un Commentaire :</h4>
                            <form action="{{ route('sujets.add_comment', $sujet) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700">Votre
                                        Commentaire</label>
                                    <textarea name="comment" id="comment" rows="4"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required></textarea>
                                </div>
                                <button type="submit" class="">Ajouter le Commentaire</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
