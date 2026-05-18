<?php
namespace App\Traits;

use function PHPUnit\Framework\isNull;

trait ApiResponse
{
    /* Helper per generare delle response Json */
    public static function apiResponse(
        /* Utile sia per risposte di successo che errori */
        bool $success,
        mixed $dataOrErrors = null,
        int $code = 200,
        ?string $message = null,
    ) {
        //Verifico se l'utente vuole la paginazione
        $paginator = null;
        if (
            $success &&
            $dataOrErrors instanceof \Illuminate\Pagination\LengthAwarePaginator
        ) {
            // mi prendo le proprietà con la paginazione
            $paginator = [
                'totalItems' => $dataOrErrors->total(),
                'page' => $dataOrErrors->currentPage(),
                'perPage' => $dataOrErrors->perPage(),
                'totalPages' => $dataOrErrors->lastPage(),
                'hasNextPage' => $dataOrErrors->hasMorePages(),
                'hasPrevPage' => !$dataOrErrors->onFirstPage(),
            ];
        }

        $payload = [
            'success' => $success,
            $success ? 'data' : 'errors' => $dataOrErrors,
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        // se c'è la paginazione, la aggiungo al payload
        if ($paginator !== null) {
            $payload = [...$payload, $paginator];
        }

        if ($message) {
            $payload['message'] = $message;
        }

        return response()->json($payload, $code);
    }
}

?>
