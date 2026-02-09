<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class PasswordResetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/password/reset",
     *     summary="Show password reset request form",
     *     tags={"Password Reset"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the password reset request form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     )
     * )
     */
    public function showResetForm()
    {
        return view('auth.reset');
    }

    /**
     * @OA\Post(
     *     path="/password/reset/request",
     *     summary="Request a password reset code",
     *     tags={"Password Reset"},
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
     *         response=302,
     *         description="Reset code sent successfully, redirect to verify form",
     *         @OA\Header(header="Location", description="Redirect to /password/reset/verify", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or email not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="E-Mail-Adresse nicht gefunden. Bitte versuchen Sie es erneut.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="CAPTCHA-Verifizierung fehlgeschlagen.")
     *         )
     *     )
     * )
     */
    public function requestReset(Request $request)
    {
        $email = $request->input('email');
        $emailAttemptsKey = 'email_attempts_' . $email;
        $emailAttempts = Session::get($emailAttemptsKey, 0);

        if ($emailAttempts >= 5) {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $recaptchaResponse = $request->input('g-recaptcha-response');
            $recaptchaSecret = config('app.recaptcha.secret_key');
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ])->json();

            if (!$response['success']) {
                return redirect()->back()->withErrors(['g-recaptcha-response' => 'CAPTCHA-Verifizierung fehlgeschlagen.'])->withInput();
            }
        }

        Session::put($emailAttemptsKey, $emailAttempts + 1);
        Session::put($emailAttemptsKey . '_expires_at', now()->addHour()->timestamp);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.exists' => 'E-Mail-Adresse nicht gefunden. Bitte versuchen Sie es erneut.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resetCode = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $expiresAt = now()->addHours(24);

        $user = User::where('email', $email)->first();
        $user->update([
            'reset_code' => $resetCode,
            'reset_expires_at' => $expiresAt,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordEmail($resetCode, $expiresAt));
        } catch (\Exception $e) {
            \Log::error('Error sending reset password email: ' . $e->getMessage());
            return redirect()->back()->withErrors(['email' => 'Fehler beim Senden der E-Mail. Bitte versuchen Sie es später erneut.'])->withInput();
        }

        return redirect()->route('password.verify')->with('success', 'Ein Reset-Code wurde an Ihre E-Mail-Adresse gesendet.');
    }

    /**
     * @OA\Get(
     *     path="/password/reset/verify",
     *     summary="Show password reset verification form",
     *     tags={"Password Reset"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the password reset verification form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     )
     * )
     */
    public function showVerifyForm()
    {
        return view('auth.verify');
    }

    /**
     * @OA\Post(
     *     path="/password/reset/verify",
     *     summary="Verify reset code and update password",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                 @OA\Property(property="reset_code", type="string", maxLength=7, minLength=7, description="7-digit reset code"),
     *                 @OA\Property(property="password", type="string", description="New password (min 8 chars, with upper, lower, and special chars)"),
     *                 @OA\Property(property="password_confirmation", type="string", description="Password confirmation"),
     *                 @OA\Property(property="g-recaptcha-response", type="string", description="reCAPTCHA response token, required after 5 failed attempts", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Password reset successfully, redirect to login",
     *         @OA\Header(header="Location", description="Redirect to /login", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid/expired reset code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ungültiger oder abgelaufener Reset-Code.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="CAPTCHA-Verifizierung fehlgeschlagen.")
     *         )
     *     )
     * )
     */
    public function verifyReset(Request $request)
    {
        $email = $request->input('email');
        $verifyAttemptsKey = 'verify_reset_attempts_' . $email;
        $verifyAttempts = Session::get($verifyAttemptsKey, 0);

        if ($verifyAttempts >= 5) {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $recaptchaResponse = $request->input('g-recaptcha-response');
            $recaptchaSecret = config('app.recaptcha.secret_key');
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ])->json();

            if (!$response['success']) {
                return redirect()->back()->withErrors(['g-recaptcha-response' => 'CAPTCHA-Verifizierung fehlgeschlagen.'])->withInput();
            }
        }

        Session::put($verifyAttemptsKey, $verifyAttempts + 1);
        Session::put($verifyAttemptsKey . '_expires_at', now()->addHour()->timestamp);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'reset_code' => 'required|size:7',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]+$/',
            ],
        ], [
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.exists' => 'E-Mail-Adresse nicht gefunden.',
            'reset_code.required' => 'Bitte geben Sie den Reset-Code ein.',
            'reset_code.size' => 'Der Reset-Code muss 7 Zeichen lang sein.',
            'password.required' => 'Bitte geben Sie ein neues Passwort ein.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwortbestätigung stimmt nicht überein.',
            'password.regex' => 'Das Passwort muss Groß- und Kleinbuchstaben sowie ein Sonderzeichen enthalten.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $email)->first();
        if (!$user || $user->reset_code !== $request->reset_code || $user->reset_expires_at < now()) {
            return redirect()->back()->withErrors(['reset_code' => 'Ungültiger oder abgelaufener Reset-Code.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_expires_at' => null,
        ]);

        Session::forget($verifyAttemptsKey);
        Session::forget($verifyAttemptsKey . '_expires_at');

        return redirect()->route('login')->with('success', 'Ihr Passwort wurde erfolgreich zurückgesetzt. Bitte melden Sie sich an.');
    }

    /**
     * @OA\Get(
     *     path="/password/reset/resend",
     *     summary="Show resend password reset code form",
     *     tags={"Password Reset"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the resend password reset code form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     )
     * )
     */
    public function showResendForm()
    {
        return view('auth.password-resend');
    }

    /**
     * @OA\Post(
     *     path="/password/reset/resend",
     *     summary="Resend password reset code",
     *     tags={"Password Reset"},
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
     *         response=302,
     *         description="New reset code sent successfully, redirect to verify form",
     *         @OA\Header(header="Location", description="Redirect to /password/reset/verify", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or email not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="E-Mail-Adresse nicht gefunden. Bitte versuchen Sie es erneut.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="CAPTCHA-Verifizierung fehlgeschlagen.")
     *         )
     *     )
     * )
     */
    public function resend(Request $request)
    {
        $email = $request->input('email');
        $emailAttemptsKey = 'email_attempts_' . $email;
        $emailAttempts = Session::get($emailAttemptsKey, 0);

        if ($emailAttempts >= 5) {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $recaptchaResponse = $request->input('g-recaptcha-response');
            $recaptchaSecret = config('app.recaptcha.secret_key');
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ])->json();

            if (!$response['success']) {
                return redirect()->back()->withErrors(['g-recaptcha-response' => 'CAPTCHA-Verifizierung fehlgeschlagen.'])->withInput();
            }
        }

        Session::put($emailAttemptsKey, $emailAttempts + 1);
        Session::put($emailAttemptsKey . '_expires_at', now()->addHour()->timestamp);

        $resendAttemptsKey = 'resend_attempts_' . $email;
        $resendAttempts = Session::get($resendAttemptsKey, 0);

        if ($resendAttempts >= 5) {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $recaptchaResponse = $request->input('g-recaptcha-response');
            $recaptchaSecret = config('app.recaptcha.secret_key');
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ])->json();

            if (!$response['success']) {
                return redirect()->back()->withErrors(['g-recaptcha-response' => 'CAPTCHA-Verifizierung fehlgeschlagen.'])->withInput();
            }
        }

        Session::put($resendAttemptsKey, $resendAttempts + 1);
        Session::put($resendAttemptsKey . '_expires_at', now()->addHour()->timestamp);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.exists' => 'E-Mail-Adresse nicht gefunden. Bitte versuchen Sie es erneut.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resetCode = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $expiresAt = now()->addHours(24);

        $user = User::where('email', $email)->first();
        $user->update([
            'reset_code' => $resetCode,
            'reset_expires_at' => $expiresAt,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordEmail($resetCode, $expiresAt));
        } catch (\Exception $e) {
            \Log::error('Error sending reset password email: ' . $e->getMessage());
            return redirect()->back()->withErrors(['email' => 'Fehler beim Senden der E-Mail. Bitte versuchen Sie es später erneut.'])->withInput();
        }

        return redirect()->route('password.verify')->with('success', 'Ein neuer Reset-Code wurde an Ihre E-Mail-Adresse gesendet.');
    }
}