@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails de l'Église: {{ $church->name }}</h1>

    <table class="table">
        <tr>
            <th>Nom</th>
            <td>{{ $church->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $church->email }}</td>
        </tr>
        <tr>
            <th>Téléphone</th>
            <td>{{ $church->phone }}</td>
        </tr>
        <tr>
            <th>Adresse</th>
            <td>{{ $church->adresse }}</td>
        </tr>
        <tr>
            <th>Logo</th>
            <td>
                @if($church->logo)
                    <img src="{{ Storage::url($church->logo) }}" alt="Logo" width="100">
                @else
                    Aucun logo
                @endif
            </td>
        </tr>
        <tr>
            <th>Est Principal</th>
            <td>{{ $church->is_main ? 'Oui' : 'Non' }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $church->description }}</td>
        </tr>
        <tr>
            <th>ID du Serviteur Propriétaire</th>
            <td>{{ $church->owner_servant_id }}</td>
        </tr>
        <tr>
            <th>Type d'Église</th>
            <td>{{ $church->type_church }}</td>
        </tr>
        <tr>
            <th>Catégorie</th>
            <td>{{ $church->categorie }}</td>
        </tr>
    </table>

    <a href="{{ route('churches.index') }}" class="btn btn-secondary">Retour à la liste</a>
    <a href="{{ route('churches.edit', $church) }}" class="btn btn-warning">Éditer</a>

    <form action="{{ route('churches.destroy', $church) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette église ?')">Supprimer</button>
    </form>
</div>
@endsection
