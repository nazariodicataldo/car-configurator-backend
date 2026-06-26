<?php
namespace App\Traits;

trait ApiResponse
{
    public static function apiResponse(
        bool $success,
        mixed $dataOrErrors = null,
        int $code = 200,
        ?string $message = null,
    ) {
        $paginator = null;
        $data = $dataOrErrors;

        if (
            $success &&
            $dataOrErrors instanceof \Illuminate\Pagination\LengthAwarePaginator
        ) {
            $paginator = [
                'totalItems' => $dataOrErrors->total(),
                'page' => $dataOrErrors->currentPage(),
                'perPage' => $dataOrErrors->perPage(),
                'totalPages' => $dataOrErrors->lastPage(),
                'hasNextPage' => $dataOrErrors->hasMorePages(),
                'hasPrevPage' => !$dataOrErrors->onFirstPage(),
            ];

            // Sostituisco il paginator con i soli items
            $data = $dataOrErrors->items();
        }

        $payload = [
            'success' => $success,
            $success ? 'data' : 'errors' => $data,
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        if ($paginator !== null) {
            $payload['pagination'] = $paginator;
        }

        if ($message) {
            $payload['message'] = $message;
        }

        return response()->json($payload, $code);
    }
}
