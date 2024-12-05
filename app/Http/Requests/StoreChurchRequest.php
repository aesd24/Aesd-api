<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // Changez à 'true' si vous souhaitez autoriser tous les utilisateurs
        return true; // Vous pouvez ajouter une logique d'autorisation ici si nécessaire
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:10',
            'adresse' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_main' => 'required|boolean',
            'description' => 'nullable|string',
            'type_church' => 'nullable|string',
            'categorie' => 'nullable|string',
        ];
    }

    /**
     * Obtenir les messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères.',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être de type :values.',
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',
            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',
            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
        ];
    }
}
