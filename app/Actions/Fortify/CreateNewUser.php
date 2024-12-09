<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\ServiteurDeDieu;
use App\Models\Fidele;
use App\Models\Chantre;
use Illuminate\Support\Facades\Storage;

use App\Models\Administrateur;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */


    // public function create(array $input): User
    // {
    //     Validator::make($input, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => $this->passwordRules(),
    //         'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
    //     ])->validate();

    //     return DB::transaction(function () use ($input) {
    //         return tap(User::create([
    //             'name' => $input['name'],
    //             'email' => $input['email'],
    //             'password' => Hash::make($input['password']),
    //         ]), function (User $user) {
    //             $this->createTeam($user);
    //         });
    //     });
    // }



    public function create(array $input): User
    {

        // dd($input);


        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'account_type' => ['required', 'in:serviteur_de_dieu,fidele,chantre,administrateur'], // Validation pour le type de compte
            'phone' => ['required', 'string', 'max:15', 'unique:users'], // Vérification de l'unicité du téléphone
            'adresse' => ['required', 'string', 'max:255'], // Adresse requise

            // Validation des cartes d'identité selon le type de compte
            'id_card_recto' => $input['account_type'] === 'serviteur_de_dieu' ? ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'] : ['nullable'],
            'id_card_verso' => $input['account_type'] === 'serviteur_de_dieu' ? ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'] : ['nullable'],
            // 'is_main' => $input['account_type'] === 'serviteur_de_dieu' ? ['required', 'boolean'] : ['nullable'],
        ], [
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'Le champ email ne doit pas dépasser :max caractères.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'account_type.required' => 'Le type de compte est obligatoire.',
            'account_type.in' => 'Le type de compte doit être serviteur_de_dieu, fidele, chantre ou administrateur.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.max' => 'L\'adresse ne doit pas dépasser :max caractères.',
            'id_card_recto.required' => 'Le recto de la carte d\'identité est requis.',
            'id_card_recto.file' => 'Le recto de la carte d\'identité doit être un fichier.',
            'id_card_recto.mimes' => 'Le recto de la carte d\'identité doit être de type jpeg, png ou jpg.',
            'id_card_recto.max' => 'Le recto de la carte d\'identité ne doit pas dépasser :max ko.',
            'id_card_verso.required' => 'Le verso de la carte d\'identité est requis.',
            'id_card_verso.file' => 'Le verso de la carte d\'identité doit être un fichier.',
            'id_card_verso.mimes' => 'Le verso de la carte d\'identité doit être de type jpeg, png ou jpg.',
            'id_card_verso.max' => 'Le verso de la carte d\'identité ne doit pas dépasser :max ko.',

            'profile_photo' => 'La photo d\'identité est requise.',
            'profile_photo.file' => 'La photo d\'identité doit être un fichier.',
            'profile_photo.mimes' => 'La photo d\'identité doit être de type jpeg, png ou jpg.',
            'profile_photo.max' => 'La photo d\'identité ne doit pas dépasser :max ko.',
            // 'is_main.required' => 'Le champ "Serviteur principal" est requis.',
            // 'is_main.boolean' => 'Le champ "Serviteur principal" doit être vrai ou faux.',
        ])->validate();

        return DB::transaction(function () use ($input) {


            // Stocker la photo de profil
            $profilePhotoPath = null;
            if (isset($input['profile_photo'])) {
                $profilePhotoPath = $this->storeProfilePhoto($input['profile_photo'], $input['name']);
            }


            // Créer l'utilisateur de base
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'], // Enregistrement du numéro de téléphone
                'account_type' => $input['account_type'],
                'adresse' => $input['adresse'], // Enregistrement de l'adresse
                'password' => Hash::make($input['password']),
                'profile_photo' => $profilePhotoPath, // Enregistrement du chemin de la photo de profil
            ]);

            // Créer le type d'utilisateur spécifique basé sur `account_type`
            switch ($input['account_type']) {
                case 'serviteur_de_dieu':
                    // Récupérer ou créer un nouvel enregistrement pour le Serviteur de Dieu
                    $serviteur = ServiteurDeDieu::firstOrNew(['user_id' => $user->id]);

                    // Suppression des anciennes images si elles existent
                    if ($serviteur->id_card_recto) {
                        Storage::delete($serviteur->id_card_recto);
                    }
                    if ($serviteur->id_card_verso) {
                        Storage::delete($serviteur->id_card_verso);
                    }

                    // Enregistrer les nouvelles images recto et verso de la carte d'identité
                    $idCardRectoPath = $this->storeIdCardImage($input['id_card_recto'], 'recto', $user->name);
                    $idCardVersoPath = $this->storeIdCardImage($input['id_card_verso'], 'verso', $user->name);

                    // Mise à jour des données dans la base de données
                    $serviteur->fill([
                        'id_card_recto' => $idCardRectoPath,
                        'id_card_verso' => $idCardVersoPath,
                        // 'is_main' => $input['is_main'] ?? false,
                    ]);
                    $serviteur->save();
                    break;

                case 'chantre':
                    Chantre::create([
                        'user_id' => $user->id, // Associe l'utilisateur à un chantre
                        'manager_id' => $input['manager_id'] ?? null, // Enregistre l'ID du manager (si disponible)
                        'role_description' => $input['description'] ?? null, // Stocke la description du rôle
                    ]);
                    break;

                case 'fidele': // Nouveau bloc pour l'administrateur
                    Fidele::create([
                        'user_id' => $user->id, // Associe l'utilisateur à un administrateur
                    ]);
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid account type.');
            }

            return $user;
        });
    }

    // Méthode pour stocker les images des cartes d'identité
    private function storeIdCardImage($file, $type, $userName)
    {
        return $file->storeAs('public/id_cards', "{$type}_{$userName}_" . time() . ".{$file->extension()}");
    }

    private function storeProfilePhoto($file, $userName)
    {
        return $file->storeAs('public/profile_photos', "{$userName}_" . time() . ".{$file->extension()}");
    }


    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }
}
