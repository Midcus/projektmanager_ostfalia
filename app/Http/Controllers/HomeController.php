<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Thesis;
use OpenApi\Annotations as OA;


class HomeController extends Controller
{
    /**
     * Display the home page based on user role
     *
     * @OA\Get(
     *     path="/home",
     *     tags={"Home"},
     *     summary="Redirects to appropriate dashboard based on user role",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=302,
     *         description="Redirects to appropriate dashboard or home page",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated"
     *     )
     * )
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->roll == 'Admin') {
                return redirect()->route('admin-dashboard');
            } elseif (auth()->user()->roll == 'Prof') {
                return redirect()->route('prof.dashboard');
            } elseif (auth()->user()->roll == 'Student') {
                return redirect()->route('welcome');
            }
        }
    
        return redirect('/');
    }
    
    /**
     * Display the welcome page with thesis listing
     *
     * @OA\Get(
     *     path="/",
     *     tags={"Home"},
     *     summary="Display welcome page with filtered thesis list",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter theses by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="projektart",
     *         in="query",
     *         description="Filter theses by project type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="betreuer",
     *         in="query",
     *         description="Filter theses by supervisor",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="kenntnisse",
     *         in="query",
     *         description="Filter theses by required skills",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter theses by status (Aktiv/Inaktiv for students, exact status for others)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"Aktiv", "Inaktiv"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with thesis list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="theses",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="projektart", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="betreuer", type="string"),
     *                     @OA\Property(property="kenntnisse", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="display_status", type="string"),
     *                     @OA\Property(
     *                         property="interesse",
     *                         type="array",
     *                         @OA\Items(type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function welcome(Request $request)
    {
        $name = $request->input('name');
        $projektart = $request->input('projektart');
        $betreuer = $request->input('betreuer');
        $kenntnisse = $request->input('kenntnisse');
        $status = $request->input('status');

        $query = Thesis::where('geheim', 'no');

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($projektart) {
            $query->whereJsonContains('projektart', $projektart);
        }
        if ($betreuer) {
            $query->where('betreuer', 'like', '%' . $betreuer . '%');
        }
        if ($kenntnisse) {
            $query->where('kenntnisse', 'like', '%' . $kenntnisse . '%');
        }
        if ($status) {
            if (!Auth::check() || (Auth::check() && Auth::user()->roll === 'Student')) {
                if ($status === 'Aktiv') {
                    $query->where('status', 'Angebot');
                } elseif ($status === 'Inaktiv') {
                    $query->where('status', '!=', 'Angebot');
                }
            } else {
                $query->where('status', $status);
            }
        }

        $theses = $query->paginate(20)->appends($request->query());

        $theses->each(function ($thesis) {
            if (!Auth::check() || (Auth::check() && Auth::user()->roll === 'Student')) {
                $thesis->display_status = ($thesis->status === 'Angebot') ? 'Aktiv' : 'Inaktiv';
            } else {
                $thesis->display_status = $thesis->status;
            }

            if (Auth::check()) {
                $thesis->interesse = $thesis->interestedUsers()
                    ->wherePivot('expires_at', '>', now())
                    ->pluck('email')
                    ->map(function ($email) {
                        return explode('@', $email)[0];
                    })->all();
            } else {
                $thesis->interesse = $thesis->interestedUsers()
                    ->wherePivot('expires_at', '>', now())
                    ->count();
            }
        });

        return view('welcome', compact('theses'));
    }
}