{{-- resources/views/ceremonies/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de la Cérémonie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="container">
                    <h1 class="text-2xl font-semibold mb-4">Détails de la Cérémonie : {{ $ceremonie->title }}</h1>

                    <p class="mb-4"><strong>Description :</strong> {{ $ceremonie->description }}</p>
                    <p class="mb-4"><strong>Date de l'événement :</strong> {{ $ceremonie->event_date }}</p>

                    <h3 class="mt-4 text-lg font-semibold">Églises associées</h3>
                    <ul class="mb-4">
                        @foreach ($ceremonie->churches as $church)
                            <li>{{ $church->name }} - Période : {{ $church->pivot->periode_time }}</li>
                        @endforeach
                    </ul>

                    @if($ceremonie->media)
                        <div class="mb-4">
                            <strong>Média :</strong>
                            @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $ceremonie->media))
                                <img src="{{ asset('storage/media/' . basename($ceremonie->media)) }}" alt="Média de la cérémonie" class="w-32 h-32 object-cover rounded">
                            @elseif (preg_match('/\.(mp4|mov|wmv)$/i', $ceremonie->media))
                                <video width="320" height="240" controls>
                                    <source src="{{ asset('storage/media/' . basename($ceremonie->media)) }}" type="video/mp4">
                                    Votre navigateur ne prend pas en charge la balise vidéo.
                                </video>
                            @endif
                        </div>
                    @else
                        <p>Aucun média disponible.</p>
                    @endif

                    <a href="{{ route('ceremonies.index') }}" class="btn btn-secondary mt-4">Retour à la liste des cérémonies</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
