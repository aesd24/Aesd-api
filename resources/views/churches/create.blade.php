<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">








                <h1>Ajouter une Église</h1>

                <form action="{{ route('churches.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="attestation_file_path">Attestation</label>
                        <input type="file" name="attestation_file_path" id="attestation_file_path" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>


                    <div class="form-group">
                        <label for="is_main">Est Principal</label>
                        <select name="is_main" id="is_main" class="form-control" required>
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>



                    <div class="form-group">
                        <label for="type_church">Type d'Église</label>
                        <input type="text" name="type_church" id="type_church" class="form-control">
                    </div>

                    {{-- <div class="form-group">
            <label for="categorie">Catégorie</label>
            <input type="text" name="categorie" id="categorie" class="form-control">
        </div> --}}

                    <button type="submit" class="btn btn-primary">Créer l'Église</button>
                </form>




            </div>
        </div>
    </div>
</x-app-layout>
