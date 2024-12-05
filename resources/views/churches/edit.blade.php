{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">





                <h1>Modifier l'Église</h1>

                <form action="{{ route('churches.update', $church) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')


                    <div class="form-group">
                        <label for="user_id">Assigner un serviteur secondaire à cette église</label>
                        <input type="text" name="user_id" id="user_id" class="form-control" placeholder="Rechercher un utilisateur" required>

                        <!-- Liste filtrée d'utilisateurs -->
                        <ul id="userList" class="list-group mt-2" style="display: none; position: absolute; max-height: 200px; overflow-y: auto; background-color: white; border: 1px solid #ddd;">
                            @foreach ($users as $user)
                                <li class="list-group-item" data-user-id="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <script>
                        // Sélectionner l'élément du champ de recherche et la liste des utilisateurs
                        const searchInput = document.getElementById('user_id');
                        const userList = document.getElementById('userList');

                        // Filtrer les utilisateurs en fonction de la saisie
                        searchInput.addEventListener('input', function() {
                            const query = searchInput.value.toLowerCase();
                            const items = userList.getElementsByTagName('li');

                            let isMatchFound = false;
                            for (let i = 0; i < items.length; i++) {
                                const itemText = items[i].textContent.toLowerCase();
                                const isMatch = itemText.includes(query);
                                items[i].style.display = isMatch ? 'block' : 'none';
                                if (isMatch) isMatchFound = true;
                            }

                            // Afficher ou cacher la liste en fonction de la recherche
                            userList.style.display = isMatchFound && query !== '' ? 'block' : 'none';
                        });

                        // Lorsqu'un utilisateur clique sur un élément de la liste, remplir le champ avec le nom
                        userList.addEventListener('click', function(e) {
                            const userId = e.target.getAttribute('data-user-id');
                            const userName = e.target.textContent;
                            searchInput.value = userName;
                            // Optionnel : vous pouvez stocker l'ID de l'utilisateur sélectionné dans un champ caché
                            // Par exemple : document.getElementById('user_id_hidden').value = userId;
                            userList.style.display = 'none'; // Cacher la liste après la sélection
                        });

                        // Fermer la liste si on clique à l'extérieur
                        document.addEventListener('click', function(e) {
                            if (!userList.contains(e.target) && e.target !== searchInput) {
                                userList.style.display = 'none';
                            }
                        });
                    </script>



                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $church->name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ $church->email }}">
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ $church->phone }}">
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" value="{{ $church->adresse }}">
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                        @if ($church->logo)
                            <img src="{{ Storage::url($church->logo) }}" alt="Logo" width="100" class="mt-2">
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="is_main">Est Principal</label>
                        <select name="is_main" id="is_main" class="form-control" required>
                            <option value="1" {{ $church->is_main ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ !$church->is_main ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $church->description }}</textarea>
                    </div>



                    <div class="form-group">
                        <label for="type_church">Type d'Église</label>
                        <input type="text" name="type_church" id="type_church" class="form-control" value="{{ $church->type_church }}">
                    </div>

                    <div class="form-group">
                        <label for="categorie">Catégorie</label>
                        <input type="text" name="categorie" id="categorie" class="form-control" value="{{ $church->categorie }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Mettre à jour l'Église</button>
                </form>



            </div>
        </div>
    </div>
</x-app-layout>








 --}}







































<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-8">

                <h1 class="text-3xl font-bold text-gray-800 mb-6">Modifier l'Église</h1>

                <form action="{{ route('churches.update', $church) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="serviteur">Serviteur assigné:</label>
                        @if ($serviteur)
                            <!-- Vérifie si $serviteur n'est pas null -->
                            <input type="text" name="serviteur" value="{{ $serviteur->user->name }}" readonly />
                        @else
                            <input type="text" name="serviteur" value="Aucun serviteur assigné" readonly />
                        @endif
                    </div>

                    <div class="form-group mb-6">
                        <label for="change_serviteur" class="text-lg font-medium text-gray-700">Changer le serviteur
                            ?</label>
                        <input type="checkbox" id="change_serviteur" name="change_serviteur" value="1"
                            class="ml-2">
                    </div>




                    <div class="form-group mb-6 relative">
                        <label for="user_name" class="text-lg font-medium text-gray-700">Assigner un serviteur
                            secondaire à cette église</label>
                        <input type="text" id="user_name"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Rechercher un utilisateur">

                        <!-- Champ caché pour stocker l'ID de l'utilisateur sélectionné -->
                        <input type="hidden" name="user_id" id="user_id">

                        <!-- Champ caché pour l'ID de l'église -->
                        <input type="hidden" name="church_id" value="{{ $church->id }}">




                        <ul id="userList"
                            class="list-group mt-2 absolute w-full bg-white shadow-lg rounded-md border max-h-40 overflow-y-auto z-10"
                            style="display: none;">
                            @isset($users)
                                @if ($users->isNotEmpty())
                                    @foreach ($users as $user)
                                        <li class="list-group-item py-2 px-3 hover:bg-indigo-100 cursor-pointer"
                                            data-user-id="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item py-2 px-3 text-center text-gray-500">
                                        Aucun serviteur disponible.
                                    </li>
                                @endif
                            @else
                                <li class="list-group-item py-2 px-3 text-center text-gray-500">
                                    Aucun serviteur disponible.
                                </li>
                            @endisset
                        </ul>





                    </div>

                    <!-- JavaScript pour le filtrage dynamique -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const searchInput = document.getElementById('user_name');
                            const userIdInput = document.getElementById('user_id'); // Champ caché pour stocker l'ID
                            const userList = document.getElementById('userList');

                            // Filtrer les utilisateurs lors de la saisie
                            searchInput.addEventListener('input', function() {
                                const query = searchInput.value.toLowerCase();
                                const items = userList.getElementsByTagName('li');
                                let isMatchFound = false;

                                // Boucle pour vérifier si chaque utilisateur correspond à la recherche
                                for (let i = 0; i < items.length; i++) {
                                    const itemText = items[i].textContent.toLowerCase();
                                    const isMatch = itemText.includes(query);
                                    items[i].style.display = isMatch ? 'block' : 'none';
                                    if (isMatch) isMatchFound = true;
                                }

                                // Afficher ou cacher la liste en fonction de la recherche
                                userList.style.display = isMatchFound && query !== '' ? 'block' : 'none';
                            });

                            // Lorsqu'un utilisateur clique sur un élément de la liste
                            userList.addEventListener('click', function(e) {
                                const userId = e.target.getAttribute('data-user-id'); // Récupérer l'ID de l'utilisateur
                                const userName = e.target.textContent.trim(); // Nom de l'utilisateur

                                // Mettre à jour le champ texte avec le nom de l'utilisateur
                                searchInput.value = userName;
                                // Mettre à jour le champ caché avec l'ID de l'utilisateur
                                userIdInput.value = userId;

                                // Cacher la liste après la sélection
                                userList.style.display = 'none';
                            });

                            // Cacher la liste si on clique à l'extérieur
                            document.addEventListener('click', function(e) {
                                if (!userList.contains(e.target) && e.target !== searchInput) {
                                    userList.style.display = 'none';
                                }
                            });
                        });
                    </script>


                    <!-- Nom de l'église -->
                    <div class="form-group mb-6">
                        <label for="name" class="text-lg font-medium text-gray-700">Nom</label>
                        <input type="text" name="name" id="name"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->name }}" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-6">
                        <label for="email" class="text-lg font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->email }}">
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group mb-6">
                        <label for="phone" class="text-lg font-medium text-gray-700">Téléphone</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->phone }}">
                    </div>

                    <!-- Adresse -->
                    <div class="form-group mb-6">
                        <label for="adresse" class="text-lg font-medium text-gray-700">Adresse</label>
                        <input type="text" name="adresse" id="adresse"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->adresse }}">
                    </div>

                    <!-- Logo -->
                    <div class="form-group mb-6">
                        <label for="logo" class="text-lg font-medium text-gray-700">Logo</label>
                        <input type="file" name="logo" id="logo"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            accept="image/*">
                        @if ($church->logo)
                            <img src="{{ Storage::url($church->logo) }}" alt="Logo" width="100" class="mt-4">
                        @endif
                    </div>

                    <!-- Principal Church Option -->
                    <div class="form-group mb-6">
                        <label for="is_main" class="text-lg font-medium text-gray-700">Est Principal</label>
                        <select name="is_main" id="is_main"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                            <option value="1" {{ $church->is_main ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ !$church->is_main ? 'selected' : '' }}>Non</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="form-group mb-6">
                        <label for="description" class="text-lg font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $church->description }}</textarea>
                    </div>

                    <!-- Type d'église -->
                    <div class="form-group mb-6">
                        <label for="type_church" class="text-lg font-medium text-gray-700">Type d'Église</label>
                        <input type="text" name="type_church" id="type_church"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->type_church }}">
                    </div>

                    <!-- Catégorie -->
                    <div class="form-group mb-6">
                        <label for="categorie" class="text-lg font-medium text-gray-700">Catégorie</label>
                        <input type="text" name="categorie" id="categorie"
                            class="form-control w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="{{ $church->categorie }}">
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-full py-3 px-4 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Mettre
                        à jour l'Église</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
