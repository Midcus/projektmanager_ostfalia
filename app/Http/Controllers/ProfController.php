<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;

class ProfController extends Controller
{
    /**
     * Show the profile edit form for professor
     *
     * @OA\Get(
     *     path="/prof/edit",
     *     tags={"Professor"},
     *     summary="Display the professor profile edit form",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with the edit form view",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have professor role"
     *     )
     * )
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('prof.edit');  
    }

    /**
     * Update the professor's profile
     *
     * @OA\Put(
     *     path="/prof/update",
     *     tags={"Professor"},
     *     summary="Update professor profile information",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="praefix", type="string", nullable=true, maxLength=255, description="Title prefix (e.g., Dr., Prof.)"),
     *             @OA\Property(property="name", type="string", maxLength=255, description="First name"),
     *             @OA\Property(property="nachname", type="string", maxLength=255, description="Last name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful update with redirect to professor dashboard",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have professor role"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = auth()->user();
    
        $request->validate([
            'praefix' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'nachname' => 'required|string|max:255',
        ]);
    
        $user->update([
            'praefix' => $request->praefix,
            'name' => $request->name,
            'nachname' => $request->nachname,
        ]);
    
        Thesis::where('prof_id', $user->id)->update([
            'betreuer' => $user->praefix . ' ' . $user->name . ' ' . $user->nachname
        ]);
    
        return redirect()->route('prof.dashboard')->with('success', 'Profil erfolgreich aktualisiert.');
    }
}