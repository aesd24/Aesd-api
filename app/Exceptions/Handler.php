<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

   /**
     * Convert a validation exception into a JSON response for API requests.
     */
    public function render($request, Throwable $exception)
    {
        // Liste des routes à exclure
        $excludedRoutes = [
            'register',   // Exemples de routes à exclure
            'login',
            'reset-password',
            'churches',
            'ceremonies'
            // Ajoutez d'autres routes si nécessaire
        ];

        // Si l'exception est une erreur de validation
        if ($exception instanceof ValidationException && !in_array($request->path(), $excludedRoutes)) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $exception->errors(),
            ], 422); // Code 422 pour erreurs de validation
        }

        // Retourner la réponse parent pour les autres types d'exceptions
        return parent::render($request, $exception);
    }




}
