<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use OpenApi\Annotations as OA;


class LoginController extends Controller
{
    /**
     * @OA\Get(
     *     path="/login",
     *     summary="Show login form",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Display the login form",
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
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Authenticate user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *                 @OA\Property(property="password", type="string", description="User's password"),
     *                 @OA\Property(property="g-recaptcha-response", type="string", description="reCAPTCHA response token, required after 2 failed attempts", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful login, redirect to intended page",
     *         @OA\Header(header="Location", description="Redirect to /home or intended URL", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or authentication failure",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diese Anmeldeinformationen stimmen nicht mit unseren Aufzeichnungen überein.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account not activated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ihr Konto ist noch nicht aktiviert. Bitte geben Sie den Aktivierungscode ein.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts, reCAPTCHA verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="reCAPTCHA verification failed. Please try again.")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $attempts = $request->session()->get('login_attempts', 0);
        \Log::info('Login Attempts before validation: ' . $attempts); // Debug before validate
    
        $requireCaptcha = $attempts >= 2;
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    
        if ($requireCaptcha) {
            $rules['g-recaptcha-response'] = ['required'];
        }
    
        $request->validate($rules);
    
        if ($requireCaptcha) {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ])->json();
    
            if (!$response['success']) {
                $request->session()->put('login_attempts', $attempts + 1);
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.']);
            }
        }


        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->first();


            if (!$user->is_activated) {
                Auth::logout(); 
                return redirect()->route('activation.show')
                    ->withErrors(['email' => 'Ihr Konto ist noch nicht aktiviert. Bitte geben Sie den Aktivierungscode ein.']);
            }


            $request->session()->forget('login_attempts');
            return redirect()->intended('/home');
        }
    

        $request->session()->put('login_attempts', $attempts + 1);
        \Log::info('Login Attempts after failure: ' . ($attempts + 1));
    
        return back()->withErrors(['email' => 'Diese Anmeldeinformationen stimmen nicht mit unseren Aufzeichnungen überein.']);
    }
}