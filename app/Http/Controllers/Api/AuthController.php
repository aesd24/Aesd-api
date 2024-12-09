<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ServiteurDeDieu;
use Illuminate\Support\Facades\Storage;
use App\Models\Fidele;
use App\Models\Chantre;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        // Log des données de la requête pour le débogage
        Log::info('Données de la requête de registre:', $request->all());

        // Validation des données d'entrée
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Vérification de l'unicité de l'email
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15|unique:users', // Vérification de l'unicité du téléphone
            'account_type' => 'required|in:serviteur_de_dieu,fidele,chantre',
            'adresse' => 'required|string|max:255',
            // Ajout des validations pour les champs spécifiques
            'id_card_recto' => $request->account_type === 'serviteur_de_dieu' ? 'required|file|mimes:jpeg,png,jpg|max:2048' : 'nullable',
            'id_card_verso' => $request->account_type === 'serviteur_de_dieu' ? 'required|file|mimes:jpeg,png,jpg|max:2048' : 'nullable',
            'is_main' => 'nullable|boolean',
        ], [
            'name.required' => 'Le champ nom est obligatoire.',
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'account_type.required' => 'Le type de compte est obligatoire.',
            'account_type.in' => 'Le type de compte doit être soit serviteur_de_dieu, fidele ou chantre.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'id_card_recto.required' => 'Le recto de la carte d\'identité est requis.',
            'id_card_recto.file' => 'Le recto de la carte d\'identité doit être un fichier.',
            'id_card_recto.mimes' => 'Le recto de la carte d\'identité doit être de type jpeg, png ou jpg.',
            'id_card_recto.max' => 'Le recto de la carte d\'identité ne doit pas dépasser :max ko.',
            'id_card_verso.required' => 'Le verso de la carte d\'identité est requis.',
            'id_card_verso.file' => 'Le verso de la carte d\'identité doit être un fichier.',
            'id_card_verso.mimes' => 'Le verso de la carte d\'identité doit être de type jpeg, png ou jpg.',
            'id_card_verso.max' => 'Le verso de la carte d\'identité ne doit pas dépasser :max ko.',
        ]);

        DB::beginTransaction();
        try {
            // Création de l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'account_type' => $request->account_type,
                'adresse' => $request->adresse,
                'password' => Hash::make($request->password),
            ]);

            // Log de la création de l'utilisateur
            Log::info('Utilisateur créé avec ID:', ['user_id' => $user->id]);

            // Créer des enregistrements dans les tables spécifiques selon le type de compte
            switch ($request->account_type) {
                case 'serviteur_de_dieu':

                    $serviteur = ServiteurDeDieu::firstOrNew(['user_id' => $user->id]);

                    // Suppression des anciennes images si elles existent
                    if ($serviteur->id_card_recto) {
                        Storage::delete($serviteur->id_card_recto);
                    }
                    if ($serviteur->id_card_verso) {
                        Storage::delete($serviteur->id_card_verso);
                    }
                    // Gestion du stockage des fichiers pour le recto
                    $idCardRectoFile = $request->file('id_card_recto');
                    $rectoFilename = 'recto_' . $user->name . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
                    $idCardRectoPath = $idCardRectoFile->storeAs('public/id_cards', $rectoFilename);

                    // Gestion du stockage des fichiers pour le verso
                    $idCardVersoFile = $request->file('id_card_verso');
                    $versoFilename = 'verso_' . $user->name . '_' . time() . '.' . $idCardVersoFile->getClientOriginalExtension();
                    $idCardVersoPath = $idCardVersoFile->storeAs('public/id_cards', $versoFilename);

                    // Créer l'enregistrement dans la table ServiteurDeDieu
                    ServiteurDeDieu::create([
                        'user_id' => $user->id,
                        'is_main' => $request->is_main, // Ajouter le champ is_main
                        'id_card_recto' => $idCardRectoPath, // Ajouter le chemin du fichier recto
                        'id_card_verso' => $idCardVersoPath, // Ajouter le chemin du fichier verso
                    ]);
                    break;


                case 'fidele':
                    Fidele::create(['user_id' => $user->id]);
                    break;
                case 'chantre':
                    Chantre::create(['user_id' => $user->id]);
                    break;
            }

            // Émettre l'événement pour l'envoi de l'e-mail de vérification
            event(new Registered($user));

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur créé avec succès.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Erreur lors de la création de l\'utilisateur:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json(['message' => 'Erreur lors de la création de l\'utilisateur'], 500);
        }
    }



    public function login(Request $request)
    {
        // Valider soit l'email, soit le téléphone, ainsi que le mot de passe avec des messages personnalisés
        $request->validate([
            'user_info' => 'required|string', // Peut être soit un email, soit un numéro de téléphone
            'password' => 'required|string',
        ], [
            'user_info.required' => 'Le champ identifiant est obligatoire.',
            'user_info.string' => 'Le champ identifiant doit être une chaîne de caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ]);

        // Vérifier si le champ 'user_info' est un email ou un numéro de téléphone
        $loginField = filter_var($request->user_info, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Rechercher l'utilisateur par email ou par téléphone
        $user = User::where($loginField, $request->user_info)->first();

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'user_info' => ['Les informations d\'identification sont incorrectes.'],
            ]);
        }

        // Créer un token pour l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retourner la réponse JSON avec le token d'accès
        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    // Déconnexion d'un utilisateur
    // public function logout(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json(['message' => 'Déconnexion réussie']);
    // }



    public function logout(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401); // Code 401 Unauthorized
        }

        try {
            // Supprimer tous les tokens de l'utilisateur
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (\Exception $e) {
            // Gérer l'exception
            Log::error('Erreur lors de la déconnexion:', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json(['message' => 'Erreur lors de la déconnexion.'], 500); // Code 500 Internal Server Error
        }
    }


    public function forgotPassword(Request $request)
    {
        Log::info('Données de la requête:', $request->all());

        // Étape 1: Validation de base
        $request->validate([
            'user_info' => 'required|string', // Peut être soit un email, soit un numéro de téléphone
        ], [
            'user_info.required' => 'Le champ identifiant est obligatoire.',
            'user_info.string' => 'Le champ identifiant doit être une chaîne de caractères.',
        ]);

        // Étape 2: Déterminer si c'est un email ou un numéro de téléphone
        $loginField = filter_var($request->user_info, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Étape 3: Validation spécifique
        if ($loginField == 'email') {
            // Validation de l'email
            $request->validate([
                'user_info' => 'email',
            ], [
                'user_info.email' => 'Le champ doit contenir une adresse email valide.',
            ]);
        } else {
            // Validation du numéro de téléphone (exemple pour 10 chiffres)
            $request->validate([
                'user_info' => 'regex:/^[0-9]{10}$/',  // Adapte cette regex si tu as besoin de plus de flexibilité
            ], [
                'user_info.regex' => 'Le champ doit contenir un numéro de téléphone valide (10 chiffres).',
            ]);
        }

        // Étape 4: Rechercher l'utilisateur en fonction de l'email ou du téléphone
        $user = DB::table('users')->where($loginField, $request->user_info)->first();

        if (!$user) {
            return response()->json(['message' => 'Cet utilisateur n\'est pas associé à un compte'], 404);
        }

        // Étape 5: Suite logique (envoi de l'email ou du SMS pour réinitialiser le mot de passe)
        if ($loginField == 'email') {
            // Envoyer le lien de réinitialisation par email
            $status = Password::sendResetLink(['email' => $request->user_info]);

            if ($status == Password::RESET_LINK_SENT) {
                return response()->json(['message' => 'Lien de réinitialisation envoyé à l\'email']);
            }

            return response()->json(['message' => 'Erreur lors de l\'envoi du lien de réinitialisation par email'], 500);
        } elseif ($loginField == 'phone') {
            // Gérer l'envoi du lien de réinitialisation par SMS (voir exemple précédent)
            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            $resetLink = url('/reset-password/' . $token);

            $smsSent = $this->sendSms($request->user_info, "Voici votre lien de réinitialisation: $resetLink");

            if ($smsSent) {
                return response()->json(['message' => 'Lien de réinitialisation envoyé par SMS']);
            }

            return response()->json(['message' => 'Erreur lors de l\'envoi du lien de réinitialisation par SMS'], 500);
        }
    }


    // Exemple de fonction pour envoyer un SMS (avec un service comme Twilio)
    protected function sendSms($phoneNumber, $message)
    {
        try {
            // Vérifie que le numéro de téléphone est correct
            Log::info("Envoi d'un SMS au numéro : $phoneNumber");

            // Ajout d'une journalisation pour vérifier les informations d'identification
            Log::info("SID: " . env('TWILIO_SID')); // Vérifie si le SID est chargé
            Log::info("Token: " . env('TWILIO_AUTH_TOKEN')); // Vérifie si le Token est chargé

            // Utiliser Twilio pour envoyer le SMS
            $twilio = new \Twilio\Rest\Client(('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create($phoneNumber, [
                'from' => env('TWILIO_PHONE_NUMBER'), // Numéro Twilio
                'body' => $message // Contenu du message
            ]);

            Log::info("SMS envoyé avec succès : $message");
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
            return false;
        }
    }






    // Méthode pour réinitialiser le mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mot de passe réinitialisé avec succès'])
            : response()->json(['message' => 'Erreur lors de la réinitialisation du mot de passe'], 500);
    }


    public function user_auth_infos(): JsonResponse
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401); // 401 Unauthorized
        }

        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // dd($user);


        $serviteur = ServiteurDeDieu::where('id', $user->id)->first();
        $fidele = Fidele::where('id', $user->id)->first();
        $chantre = Chantre::where('id', $user->id)->first();


        if ($serviteur) {

            // Retourner les informations de l'utilisateur
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'account_type' => $user->account_type,
                'adresse' => $user->adresse,
                'id_card_recto' => $serviteur->id_card_recto,
                'id_card_verso' => $serviteur->id_card_verso,
                'profile_photo' =>  $user->profile_photo,
                'temps_total'  =>    $user->temps_total,
                'points_obtenus' =>  $user->points_obtenus,
                'is_assigned' => $serviteur->is_assigned,
                'is_main' => $serviteur->is_main,
                'user_id' =>  $serviteur->user_id,
                'church_id' =>  $serviteur->church_id,
                'created_at' =>  $serviteur->created_at

                // Ajoutez d'autres informations nécessaires ici
            ], 200); // 200 OK

        }


        if ($chantre) {

            // Retourner les informations de l'utilisateur
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'account_type' => $user->account_type,
                'adresse' => $user->adresse,
                'profile_photo' =>  $user->profile_photo,
                'temps_total'  =>    $user->temps_total,
                'points_obtenus' =>  $user->points_obtenus,

                'manager'  =>    $chantre->manager,
                'description' =>  $chantre->description,
                // 'temps_total'  =>    $chantre->temps_total,
                // 'points_obtenus' =>  $chantre->points_obtenus,
                'user_id' =>  $chantre->user_id,
                'church_id' =>  $chantre->church_id,
                'created_at' =>  $chantre->created_at

                // Ajoutez d'autres informations nécessaires ici
            ], 200); // 200 OK

        }



        if ($fidele) {

            // Retourner les informations de l'utilisateur
            return response()->json([
               'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'account_type' => $user->account_type,
                'adresse' => $user->adresse,
                'profile_photo' =>  $user->profile_photo,
                'temps_total'  =>    $user->temps_total,
                'points_obtenus' =>  $user->points_obtenus,

                'user_id' =>  $fidele->user_id,
                'church_id' =>  $fidele->church_id,
                'created_at' =>  $fidele->created_at

                // Ajoutez d'autres informations nécessaires ici
            ], 200); // 200 OK

        }
    }
}
