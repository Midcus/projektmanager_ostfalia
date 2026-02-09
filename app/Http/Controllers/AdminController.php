<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;


class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/dashboard",
     *     summary="Show admin dashboard with user list",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Display the admin dashboard with a list of users",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to login if not authenticated or not an Admin",
     *         @OA\Header(header="Location", description="Redirect to /login", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden if user is not an Admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized access")
     *         )
     *     )
     * )
     */
    public function dashboard()
    {
        $users = User::all();
        return view('admin-dashboard', compact('users'));
    }

    /**
     * @OA\Delete(
     *     path="/admin/delete-user/{id}",
     *     summary="Delete a user by ID",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="User deleted successfully, redirect to admin dashboard",
     *         @OA\Header(header="Location", description="Redirect to /admin/dashboard", @OA\Schema(type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Benutzer nicht gefunden.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden if attempting to delete self or not an Admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sie können sich nicht selbst löschen!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Redirect to login if not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bitte melden Sie sich an.")
     *         )
     *     )
     * )
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return redirect()->route('admin-dashboard')->with('error', 'Benutzer nicht gefunden.');
        }
    
        if (auth()->user()->id == $id) {
            return redirect()->route('admin-dashboard')->with('error', 'Sie können sich nicht selbst löschen!');
        }
    
        $user->delete();
        return redirect()->route('admin-dashboard')->with('success', 'Benutzer wurde erfolgreich gelöscht.');
    }
}