<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function verify(Request $request)
    {
        $from_mobile = $request->boolean('from_mobile');

        // Verifico se la richiesta viene dall'app mobile o dal web
        $frontend_url = $from_mobile
            ? Config::get('app.mobile_url')
            : Config::get('app.frontend_url');

        $user = User::find($request->route('id'));

        if (
            !$user ||
            !hash_equals(
                sha1($user->getEmailForVerification()),
                $request->route('hash'),
            )
        ) {
            $message = 'Cannot verify email';

            if ($request->expectsJson()) {
                return $this->apiResponse(false, null, 422, $message);
            }
            return $this->redirectFrontend($frontend_url, 'invalid', $message);
        }

        if (!$request->hasValidSignature()) {
            $message = 'URL signature is not valid';

            if ($request->expectsJson()) {
                return $this->apiResponse(false, null, 422, $message);
            }
            return $this->redirectFrontend($frontend_url, 'invalid', $message);
        }

        if ($user->hasVerifiedEmail()) {
            $message = 'Email already verified';

            if ($request->expectsJson()) {
                return $this->apiResponse(true, null, 200, $message);
            }
            return $this->redirectFrontend($frontend_url, 'success', $message);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            if ($request->expectsJson()) {
                return $this->apiResponse(
                    true,
                    null,
                    200,
                    'Email verified successfully.',
                );
            }
        }

        $email_verify_page = '/email-verify';
        // Frontend URL + pagina di verifica email
        $redirect_url = $frontend_url . $email_verify_page;

        return $this->redirectFrontend(
            $redirect_url,
            'success',
            'Email verified successfully.',
        );
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->apiResponse(
                false,
                null,
                422,
                'Email already verified.',
            );
        }

        // invio notifica
        $user->sendEmailVerificationNotification();

        return $this->apiResponse(
            true,
            null,
            200,
            'Verification notification sent successfully.',
        );
    }

    private function redirectFrontend(
        string $base,
        string $status,
        string $message,
    ) {
        $sep = str_contains($base, '?') ? '&' : '?';

        $url = url()->query("{$base}{$sep}status={$status}", [
            'message' => $message,
        ]);

        return redirect()->away($url);
    }
}
