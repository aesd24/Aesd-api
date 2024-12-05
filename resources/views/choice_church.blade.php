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

                    @if (session('success'))
                        <div style="color: green;">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('eglise.sauvegarder') }}" method="POST">
                        @csrf
                        <label for="church_id">Église :</label>
                        <select name="church_id" id="church_id" required>
                            <option value="">-- Sélectionnez une église --</option>
                            @foreach ($eglises as $eglise)
                                <option value="{{ $eglise->id }}"
                                    @if(old('church_id', $selectedChurchId) == $eglise->id) selected @endif>
                                    {{ $eglise->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('church_id')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <button type="submit">Sauvegarder</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
