<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\ActivationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;


class RegisterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/register",
     *     summary="Show registration form",
     *     tags={"Registration"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the registration form",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to home if already authenticated",
     *         @OA\Header(header="Location", description="Redirect to /home", @OA\Schema(type="string"))
     *     )
     * )
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Registration"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=255, description="User's first name, must start with uppercase letter"),
     *                 @OA\Property(property="nachname", type="string", maxLength=255, description="User's last name, must start with uppercase letter"),
     *                 @OA\Property(property="email", type="string", format="email", maxLength=255, description="User's email, format: id followed by 6 digits and @ostfalia.de"),
     *                 @OA\Property(property="password", type="string", minLength=8, description="Password, min 8 chars, with upper, lower, and special chars"),
     *                 @OA\Property(property="password_confirmation", type="string", description="Password confirmation"),
     *                 @OA\Property(property="roll", type="string", enum={"Admin", "Prof", "Student"}, description="User role"),
     *                 @OA\Property(property="internal_code", type="string", description="Internal code for Admin or Prof roles", nullable=true),
     *                 @OA\Property(property="praefix", type="string", description="Prefix for Prof role", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Registration successful, redirect to activation page",
     *         @OA\Header(header="Location", description="Redirect to /activation", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid internal code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email validation failed.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating user or sending activation email",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Fehler beim Senden der Aktivierungs-E-Mail. Bitte versuchen Sie es erneut.")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-ZÄÖÜ][a-zäöüßA-ZÄÖÜ\s-]*$/'
            ],
            'nachname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-ZÄÖÜ][a-zäöüßA-ZÄÖÜ\s-]*$/'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^id\d{6}@ostfalia\.de$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]+$/'
            ],
            'roll' => 'required|string|in:Admin,Prof,Student',
            'internal_code' => 'nullable|string',
        ], [
            'name.regex' => 'Der Vorname muss mit einem Großbuchstaben beginnen und darf nur Buchstaben, Leerzeichen oder Bindestriche enthalten.',
            'nachname.regex' => 'Der Nachname muss mit einem Großbuchstaben beginnen und darf nur Buchstaben, Leerzeichen oder Bindestriche enthalten.',
            'email.regex' => 'Die E-Mail muss mit "id" beginnen, gefolgt von genau 6 Ziffern und auf "@ostfalia.de" enden (z.B. id123456@ostfalia.de).',
            'password.regex' => 'Das Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben und ein Sonderzeichen (!@#$%^&*) enthalten.',
            'name.required' => 'Der Vorname ist erforderlich.',
            'nachname.required' => 'Der Nachname ist erforderlich.',
            'email.required' => 'Die E-Mail ist erforderlich.',
            'email.unique' => 'Diese E-Mail ist bereits registriert. Bitte versuchen Sie, sich anzumelden oder Ihr Passwort zurückzusetzen.',
            'password.required' => 'Das Passwort ist erforderlich.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwortbestätigung stimmt nicht überein.',
            'roll.required' => 'Die Rolle ist erforderlich.',
            'roll.in' => 'Die ausgewählte Rolle ist ungültig.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (($request->roll == 'Admin' && $request->internal_code != env('ADMIN_INTERNAL_CODE')) ||
            ($request->roll == 'Prof' && $request->internal_code != env('PROF_INTERNAL_CODE'))) {
            return back()->withErrors(['internal_code' => 'Der interne Code ist für die ausgewählte Rolle falsch.'])->withInput();
        }

        $activationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::info('Generated activation code: ' . $activationCode);

        try {
            $user = User::create([
                'name' => $request->name,
                'nachname' => $request->nachname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'roll' => $request->roll,
                'praefix' => $request->roll == 'Prof' ? $request->praefix : null,
                'activation_code' => $activationCode,
                'activation_expires_at' => now()->addHours(24),
                'is_activated' => false,
            ]);
            Log::info('User created with activation_code: ' . $user->activation_code);
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Fehler beim Erstellen des Benutzers. Bitte versuchen Sie es erneut.'])->withInput();
        }

        try {
            Mail::to($user->email)->send(new ActivationEmail($activationCode));
        } catch (\Exception $e) {
            $user->delete();
            Log::error('Failed to send activation email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Fehler beim Senden der Aktivierungs-E-Mail. Bitte versuchen Sie es erneut.'])->withInput();
        }

        return redirect()->route('activation.show')->with('success', 'Bitte überprüfen Sie Ihre E-Mail für den Aktivierungscode.');
    }
}