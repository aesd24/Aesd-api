<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\ServiteurDeDieu;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    // public function update(User $user, array $input): void
    // {

    //     Validator::make($input, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    //         'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
    //     ])->validateWithBag('updateProfileInformation');

    //     if (isset($input['photo'])) {
    //         $user->updateProfilePhoto($input['photo']);
    //     }

    //     if ($input['email'] !== $user->email &&
    //         $user instanceof MustVerifyEmail) {
    //         $this->updateVerifiedUser($user, $input);
    //     } else {
    //         $user->forceFill([
    //             'name' => $input['name'],
    //             'email' => $input['email'],
    //         ])->save();
    //     }
    // }





    public function update(User $user, array $input): void
    {

        // $serviteurDeDieu = ServiteurDeDieu::find($user->id); // Remplacez $id par l'ID du serviteur de Dieu que vous voulez récupérer
        // $idCardRecto = $serviteurDeDieu->id_card_recto;
        // $idCardVerso = $serviteurDeDieu->id_card_verso;



        // Accéder au serviteur de Dieu associé à cet utilisateur
        $serviteurDeDieu = $user->serviteurDeDieu;
        // Vérifier si un serviteur de Dieu existe pour cet utilisateur
        if ($serviteurDeDieu) {

            dd($serviteurDeDieu);
            // Accéder aux informations de la carte d'identité
            $idCardRecto = $serviteurDeDieu->id_card_recto;
            $idCardVerso = $serviteurDeDieu->id_card_verso;
        }


        // Accéder au chantre associé à cet utilisateur
        $chantre = $user->chantre;

        // Vérifier si un chantre existe pour cet utilisateur
        if ($chantre) {

            dd($chantre);
            // Accéder aux informations du chantre
            $manager = $chantre->manager;
            $description = $chantre->description;
            $churchId = $chantre->church_id;
        }



        // Accéder au fidèle associé à cet utilisateur
        $fidele = $user->fidele;

        // Vérifier si un fidèle existe pour cet utilisateur
        if ($fidele) {

            dd($fidele);
            // Accéder aux informations du fidèle
            $churchId = $fidele->church_id;
        }







        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'account_type' => ['nullable', 'string', 'in:serviteur_de_dieu,fidele,chantre'], // Options disponibles
            // 'phone' => ['nullable', 'regex:/^(\+33|0)[1-9](\d{2}){4}$/'], // Exemple de validation pour numéro français
            'adresse' => ['nullable', 'string', 'max:255'],
            // 'id_card_recto' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
            // 'id_card_verso' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
            // 'is_main' => ['nullable', 'boolean'],
        ])->validateWithBag('updateProfileInformation');

        // Gestion de la photo de profil
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Gestion des cartes d'identité
        if (isset($input['id_card_recto'])) {
            $user->forceFill([
                'id_card_recto' => $input['id_card_recto']->store('id_cards', 'public'), // Sauvegarde dans un répertoire public
            ]);
        }

        if (isset($input['id_card_verso'])) {
            $user->forceFill([
                'id_card_verso' => $input['id_card_verso']->store('id_cards', 'public'),
            ]);
        }

        // Mise à jour des autres champs
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'account_type' => $input['account_type'] ?? $user->account_type, // Conserve la valeur actuelle si non définie
            'phone' => $input['phone'] ?? $user->phone,
            'adresse' => $input['adresse'] ?? $user->adresse,
            // 'is_main' => $input['is_main'] ?? $user->is_main,
        ])->save();

        // Si l'email a changé, gérer la vérification
        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        }
    }


    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
