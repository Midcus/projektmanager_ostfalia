<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Mail\ActivationEmail;
use Illuminate\Support\Facades\Mail;
use OpenApi\Annotations as OA;


class ActivationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/activation",
     *     summary="Show activation form",
     *     tags={"Activation"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the activation form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     )
     * )
     */
    public function show()
    {
        return view('auth.activation');
    }

    /**
     * @OA\Post(
     *     path="/activation/verify",
     *     summary="Verify user activation code",
     *     tags={"Activation"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                 @OA\Property(property="activation_code", type="string", maxLength=6, minLength=6, description="6-digit activation code"),
     *                 @OA\Property(property="g-recaptcha-response", type="string", description="reCAPTCHA response token, required after 5 failed attempts", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Account successfully activated",
     *         @OA\Header(header="Location", description="Redirect to login page", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid/expired activation code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ungültiger oder abgelaufener Aktivierungscode.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="reCAPTCHA verification failed.")
     *         )
     *     )
     * )
     */
    public function verify(Request $request)
    {
        $attempts = Session::get('verify_attempts_' . $request->email, 0);
        $lastAttemptTime = Session::get('last_verify_time_' . $request->email, now()->subHour()->toDateTimeString());

        if (now()->diffInMinutes($lastAttemptTime) >= 60) {
            Session::put('verify_attempts_' . $request->email, 0);
            $attempts = 0;
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'activation_code' => 'required|string|size:6',
        ]);

        if ($attempts >= 5) {
            $request->validate([
                'g-recaptcha-response' => 'required',
            ]);

            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ])->json();

            if (!$response['success']) {
                Log::warning('reCAPTCHA verification failed for activation attempt for email: ' . $request->email);
                Session::put('verify_attempts_' . $request->email, $attempts + 1);
                Session::put('last_verify_time_' . $request->email, now()->toDateTimeString());
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
            }
        }

        $user = User::where('email', $request->email)
            ->where('activation_code', $request->activation_code)
            ->where('activation_expires_at', '>', now())
            ->first();

        if (!$user) {
            Log::warning('Invalid activation attempt for email: ' . $request->email);
            Session::put('verify_attempts_' . $request->email, $attempts + 1);
            Session::put('last_verify_time_' . $request->email, now()->toDateTimeString());
            return back()->withErrors(['activation_code' => 'Ungültiger oder abgelaufener Aktivierungscode.']);
        }

        if ($user->is_activated) {
            Log::info('Account already activated for email: ' . $request->email);
            Session::forget('verify_attempts_' . $request->email);
            Session::forget('last_verify_time_' . $request->email);
            return redirect()->route('login')->with('success', 'Ihr Konto ist bereits aktiviert. Bitte loggen Sie sich ein.');
        }

        try {
            $user->update([
                'is_activated' => true,
                'activation_code' => null,
                'activation_expires_at' => null,
            ]);
            Log::info('Account activated successfully for email: ' . $request->email);
            Session::forget('verify_attempts_' . $request->email);
            Session::forget('last_verify_time_' . $request->email);
        } catch (\Exception $e) {
            Log::error('Failed to activate account for email: ' . $request->email . ' - Error: ' . $e->getMessage());
            Session::put('verify_attempts_' . $request->email, $attempts + 1);
            Session::put('last_verify_time_' . $request->email, now()->toDateTimeString());
            return back()->withErrors(['error' => 'Fehler beim Aktivieren des Kontos. Bitte versuchen Sie es erneut.']);
        }

        return redirect()->route('login')->with('success', 'Konto erfolgreich aktiviert! Bitte loggen Sie sich ein.');
    }

    /**
     * @OA\Get(
     *     path="/activation/resend",
     *     summary="Show resend activation code form",
     *     tags={"Activation"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the resend activation code form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     )
     * )
     */
    public function showResendForm()
    {
        return view('auth.resend');
    }

    /**
     * @OA\Post(
     *     path="/activation/resend",
     *     summary="Resend activation code",
     *     tags={"Activation"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                 @OA\Property(property="g-recaptcha-response", type="string", description="reCAPTCHA response token, required after 5 failed attempts", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New activation code sent successfully",
     *         @OA\Header(header="Location", description="Redirect to resend form", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or account already activated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Konto bereits aktiviert oder nicht gefunden.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="reCAPTCHA verification failed.")
     *         )
     *     )
     * )
     */
    public function resend(Request $request)
    {
        $attempts = Session::get('resend_attempts_' . $request->email, 0);
        $lastResendTime = Session::get('last_resend_time_' . $request->email, now()->subHour()->toDateTimeString());

        if (now()->diffInMinutes($lastResendTime) >= 60) {
            Session::put('resend_attempts_' . $request->email, 0);
            $attempts = 0;
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user || $user->is_activated) {
            Log::info('Resend attempt for already activated or non-existent email: ' . $email);
            return redirect()->route('resend.form')->withErrors(['email' => 'Konto bereits aktiviert oder nicht gefunden.']);
        }

        if ($attempts >= 5) {
            $request->validate([
                'g-recaptcha-response' => 'required',
            ]);

            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ])->json();

            if (!$response['success']) {
                Log::warning('reCAPTCHA verification failed for resend code attempt for email: ' . $email);
                Session::put('resend_attempts_' . $request->email, $attempts + 1);
                Session::put('last_resend_time_' . $request->email, now()->toDateTimeString());
                return redirect()->route('resend.form')->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
            }
        }

        $activationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        try {
            $user->update([
                'activation_code' => $activationCode,
                'activation_expires_at' => now()->addHours(24),
            ]);
            Log::info('New activation code generated for email: ' . $email . ' - Code: ' . $activationCode);
        } catch (\Exception $e) {
            Log::error('Failed to update activation code for email: ' . $email . ' - Error: ' . $e->getMessage());
            Session::put('resend_attempts_' . $request->email, $attempts + 1);
            Session::put('last_resend_time_' . $request->email, now()->toDateTimeString());
            return redirect()->route('resend.form')->withErrors(['error' => 'Fehler beim Generieren eines neuen Codes. Bitte versuchen Sie es erneut.']);
        }

        try {
            Mail::to($user->email)->send(new ActivationEmail($activationCode));
            Log::info('Activation email resent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to resend activation email to: ' . $user->email . ' - Error: ' . $e->getMessage());
            Session::put('resend_attempts_' . $request->email, $attempts + 1);
            Session::put('last_resend_time_' . $request->email, now()->toDateTimeString());
            return redirect()->route('resend.form')->withErrors(['email' => 'Fehler beim Senden der Aktivierungs-E-Mail. Bitte versuchen Sie es erneut.']);
        }

        Session::put('resend_attempts_' . $request->email, $attempts + 1);
        Session::put('last_resend_time_' . $request->email, now()->toDateTimeString());
        Log::info('Resend attempts updated for email: ' . $email . ' - Attempts: ' . ($attempts + 1));

        return redirect()->route('resend.form')->with('success', 'Ein neuer Aktivierungscode wurde gesendet.');
    }
}