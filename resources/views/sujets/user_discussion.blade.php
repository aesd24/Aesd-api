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

                <div class="mt-6 space-y-4">
                    @foreach ($sujets as $sujet)
                        <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg shadow-md">
                            <!-- Information principale sur une seule ligne -->
                            <div class="flex flex-col sm:flex-row sm:space-x-6 w-full">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-blue-700">{{ $sujet->theme }}</h3>
                                    <p class="text-sm text-gray-500">Date:
                                        {{ \Carbon\Carbon::parse($sujet->date)->format('d-m-Y') }}</p>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-600">{{ Str::limit($sujet->body, 100) }}...</p> <!-- Résumé -->
                                </div>
                            </div>

                            <!-- Actions alignées sur la droite -->
                            <div class="flex space-x-4">
                                <a href="{{ route('sujets.user_show_discussion', $sujet) }}"
                                    class="text-blue-500 hover:underline">Voir</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
