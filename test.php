

/**
     * Mettre à jour les informations d'une église.
     */
    public function update(Request $request, Church $church)
    {

        // Validation des données d'entrée
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:10',
            'adresse' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo
            'is_main' => 'required|boolean',
            'description' => 'nullable|string',
            'type_church' => 'nullable|string',
            // 'categorie' => 'nullable|string',
        ], [
            // Messages personnalisés
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',

            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères.',

            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',

            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',

            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

            // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
        ]);


        // Gestion du téléchargement du logo
        if ($request->hasFile('logo')) {
            // Supprimez l'ancien logo s'il existe
            if ($church->logo) {
                Storage::disk('public')->delete($church->logo);
            }

            // Génération du nom du nouveau logo
            $idCardRectoFile = $request->file('logo');
            $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
            $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
            $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo dans les données validées
        }

        // Vérification que l'utilisateur connecté est un serviteur de Dieu
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();


        if (!$serviteurDeDieu) {
            return redirect()->back()->withErrors('L\'utilisateur connecté n\'est pas un serviteur de Dieu.');
        }

        // Mise à jour des informations de l'église
        $church->update(array_merge($validatedData, [
            'owner_servant_id' => $serviteurDeDieu->id, // Utilise l'ID du serviteur de Dieu
        ]));


        if ($request->change_serviteur) {

            // Récupérer le serviteur actuellement assigné à l'église
            $serviteur = ServiteurDeDieu::where('church_id', $church->id)
                ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
                ->first();

                dd($serviteur```php
public function update(Request $request, Church $church)
{
    // Validation des données d'entrée
    $validatedData = $request->validate([```php
public function update(Request $request, Church $church)
{
    // Validation des données d'entrée
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:10',
        'adresse' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo
        'is_main' => 'required|boolean',
        'description' => 'nullable|string',
        'type_church' => 'nullable|string',
        // 'categorie' => 'nullable|string',
    ], [
        // Messages personnalisés
        'name.required' => 'Le champ nom est obligatoire.',
        'name.string' => 'Le champ nom doit être une chaîne de caractères.',
        'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',

        'email.email' => 'L\'adresse email doit être valide.',
        'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères.',

        'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
        'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',

        'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',

        'logo.image' => 'Le logo doit être une image.',
        'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
        'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

        'is_main.required' => 'Le champ "principal" est requis.',
        'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

        'description.string' => 'La description doit être une chaîne de caractères.',

        'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

        // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
    ]);

    // Gestion du téléchargement du logo
    if ($request->hasFile('logo')) {
        // Supprimez l'ancien logo s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        // Génération du nom du nouveau logo
        $idCardRectoFile = $request->file('logo');
        $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
        $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
        $validatedData['logo'] = $logoPath; // Met à jour
    // Gestion du téléchargement du logo
    if ($request->hasFile('logo')) {
        // Supprimez l'ancien logo s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        // Génération du nom du nouveau logo
        $idCardRectoFile = $request->file('logo');
        $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
        $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
        $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo dans les données validées
    }

    // Vérification que l'utilisateur connecté est un serviteur de Dieu
    $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

    if (!$serviteurDeDieu) {
        return redirect()->back()->withErrors('L\'utilisateur connecté n\'est pas un serviteur de Dieu.');
    }

    // Mise à jour des informations de l'église
    $church->update(array_merge($validatedData, [
        'owner_servant_id' => $serviteurDeDieu->id, // Utilise l'ID du serviteur de Dieu
    ]));

    if ($request->change_serviteur) {
        // Récupérer le serviteur actuellement assigné à l'église
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
            ->first();

        // Vérifier si un serviteur est assigné à l'église
        if ($serviteur) {
            // Désassocier le serviteur actuel de l'église
            $serviteur->church_id = null;
            $serviteur->save(); // Sauvegarder les modifications
        }

        // Récupérer le nouveau serviteur assigné via le champ 'user_id' de la requête
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();

        // Vérifier si le nouveau serviteur existe
        if ($serviteurDeDieu) {
            // Associer le nouveau serviteur à l'église
            $serviteurDeDieu->church_id = $church->id;
            $serviteurDeDieu->save(); // Sauvegarder les modifications
        } else {
            // Gérer le cas où le nouveau serviteur n'existe pas
            return redirect()->back()->with('error', 'Le serviteur spécifié n\'existe pas.');
        }
    }

    // Redirection avec message de succès
    return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
}

public function update(Request $request, Church $church)
{
    // Validation des données d'entrée
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:10',
        'adresse' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo
        'is_main' => 'required|boolean',
        'description' => 'nullable|string',
        'type_church' => 'nullable|string',
        // 'categorie' => 'nullable|string',
    ], [
        // Messages personnalisés
        'name.required' => 'Le champ nom est obligatoire.',
        'name.string' => 'Le champ nom doit être une chaîne de caractères.',
        'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',

        'email.email' => 'L\'adresse email doit être valide.',
        'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères.',

        'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
        'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
        'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',

        'logo.image' => 'Le logo doit être une image.',
        'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
        'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

        'is_main.required' => 'Le champ "principal" est requis.',
        'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

        'description.string' => 'La description doit être une chaîne de caractères.',

        'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

        // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
    ]);

    // Gestion du téléchargement du logo
    if ($request->hasFile('logo')) {
        // Supprimez l'ancien logo s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        // Génération du nom du nouveau logo
        $idCardRectoFile = $request->file('logo');
        $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
        $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
        $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo dans les données validées
    }

    // Vérification que l'utilisateur connecté est un serviteur de Dieu
    $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

    if (!$serviteurDeDieu) {
        return redirect()->back()->withErrors('L\'utilisateur connecté n\'est pas un serviteur de Dieu.');
    }

    // Mise à jour des informations de l'église
    $church->update(array_merge($validatedData, [
        'owner_servant_id' => $serviteurDeDieu->id, // Utilise l'ID du serviteur de Dieu
    ]));

    if ($request->change_serviteur) {
        // Récupérer le serviteur actuellement assigné à l'église
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
            ->first();

        // Vérifier si un serviteur est assigné à l'église
        if ($serviteur) {
            // Désassocier le serviteur actuel de l'église
            $serviteur->church_id = null;
            $serviteur->save(); // Sauvegarder les modifications
        }

       ', 'Le serviteur spécifié n\'existe pas.');
        }
    }

    // Redirection avec message de succès
    return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
}
```amp 'user_id' de la requête
        $serv
        // Récupérer le nouveau serviteur assigné via le champ 'user_id
            // Désassoc
    // Vérification que l'utilisateur connecté est un serviteur de Dieu
    $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

    if (!$serviteurDeDieu) {
        return redirect(!$serviteur
       $serviteurDeDieu =
    // VlogoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
        $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo dans les données validées
        $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo/logos', $rectoFilename);
        $validatedData['logo'] = $logordRectoFile->getClientOriginalExtension();
        $logoPathoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
        $
        $idCardRectoFile = $request->file('logo');
        $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRect');
        $
        $idCardRect
        //

               'logo' => 'nullable|image|mimes:jpeg,png

main' => 'required|booleansse' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo

        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gife' => 'nullable|string
        'phone' => 'nullable|string|max:lidate([
       public function update
public    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email       // Gérer le cas où le nouveau serviteur n'existe pas
            return redirect()->back()->with('error', 'Le serviteur spécifié n\'existe pas.');
        }
    }

    // Redirection avec message de succès
    return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
}
```
            // Vérifier si un serviteur est assigné à l'église
            if ($serviteur) {
                // Désassocier le serviteur actuel de l'église
                $serviteur->church_id = null;
                $serviteur->save(); // Sauvegarder les modifications
            }


            // Récupérer le nouveau serviteur assigné via le champ 'user_id' de la requête
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();

            // Vérifier si le nouveau serviteur existe
            if ($serviteurDeDieu) {
                // Associer le nouveau serviteur à l'église
                $serviteurDeDieu->church_id = $church->id;
                $serviteurDeDieu->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le serviteur spécifié n\'existe pas.');
            }
        }

        // Redirection avec message de succès
        return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
    }



