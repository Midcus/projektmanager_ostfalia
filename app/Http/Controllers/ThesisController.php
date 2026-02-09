<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class ThesisController extends Controller
{
    /**
     * Display a listing of theses for the authenticated professor
     *
     * @OA\Get(
     *     path="/prof/dashboard",
     *     tags={"Thesis"},
     *     summary="List theses for the authenticated professor with filters",
     *     security={{"bearerAuth": {}}},
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
     *         @OA\Schema(type="string", enum={"Teamprojekt", "Studienarbeit", "Bachelorthesis", "Masterthesis"})
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
     *         description="Filter theses by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Angebot", "Aktiv", "Fertig", "Idle"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with paginated theses list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="theses",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="projektart", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="kenntnisse", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="interesse", type="array", @OA\Items(type="string"))
     *                 )
     *             )
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
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Thesis::where('prof_id', auth()->user()->id);
    
        if ($name = $request->input('name')) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($projektart = $request->input('projektart')) {
            $query->whereJsonContains('projektart', $projektart);
        }
        if ($kenntnisse = $request->input('kenntnisse')) {
            $query->where('kenntnisse', 'like', '%' . $kenntnisse . '%');
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
    
        $theses = $query->paginate(15)->appends($request->query());
    
        $theses->each(function ($thesis) {
            $thesis->interesse = $thesis->interestedUsers->pluck('email')->map(function ($email) {
                return explode('@', $email)[0];
            })->all();
        });
    
        return view('prof-dashboard', compact('theses'));
    }

    /**
     * Show the form for creating a new thesis
     *
     * @OA\Get(
     *     path="/thesis/create",
     *     tags={"Thesis"},
     *     summary="Display form to create a new thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with the create form view",
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
    public function create()
    {
        return view('thesis.create');
    }

    /**
     * Store a newly created thesis in storage
     *
     * @OA\Post(
     *     path="/prof/thesis",
     *     tags={"Thesis"},
     *     summary="Create a new thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=255, description="Thesis title"),
     *                 @OA\Property(property="description", type="string", description="Thesis description"),
     *                 @OA\Property(property="kenntnisse", type="string", description="Required skills"),
     *                 @OA\Property(property="notiz", type="string", nullable=true, description="Additional notes"),
     *                 @OA\Property(property="semester_type", type="string", enum={"WS", "SS", "N/A"}, description="Semester type"),
     *                 @OA\Property(property="semester_year", type="string", nullable=true, pattern="^[0-9]{4}$", description="Semester year"),
     *                 @OA\Property(property="pdf_1", type="string", format="binary", nullable=true, description="First PDF file (max 5MB)"),
     *                 @OA\Property(property="pdf_2", type="string", format="binary", nullable=true, description="Second PDF file (max 5MB)"),
     *                 @OA\Property(property="projektart", type="array", @OA\Items(type="string", enum={"Teamprojekt", "Studienarbeit", "Bachelorthesis", "Masterthesis"}), description="Project types"),
     *                 @OA\Property(property="status", type="string", enum={"Angebot", "Aktiv", "Fertig", "Idle"}, description="Thesis status"),
     *                 @OA\Property(property="geheim", type="string", enum={"yes", "no"}, description="Is thesis secret?"),
     *                 @OA\Property(property="startdatum", type="string", format="date", nullable=true, description="Start date"),
     *                 @OA\Property(property="enddatum", type="string", format="date", nullable=true, description="End date"),
     *                 @OA\Property(property="vortragdatum", type="string", format="date", nullable=true, description="Presentation date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful creation with redirect to professor dashboard",
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
    public function store(Request $request)
    {
        try {
            if (!auth()->check()) {
                return redirect()->route('login')->withErrors('Sie müssen sich anmelden, um eine Thesis zu erstellen.');
            }
    
            $validator = Validator::make($request->all(), [
                'name'            => 'required|string|max:255',
                'description'     => 'required|string',
                'kenntnisse'      => 'required|string',
                'notiz'           => 'nullable|string',
                'semester_type'   => 'required|in:WS,SS,N/A',
                'semester_year'   => 'nullable|digits:4',
                'pdf_1'           => 'nullable|file|mimes:pdf|max:5120',
                'pdf_2'           => 'nullable|file|mimes:pdf|max:5120',
                'projektart'      => 'required|array',
                'projektart.*'    => 'in:Teamprojekt,Studienarbeit,Bachelorthesis,Masterthesis',
                'status'          => 'required|in:Angebot,Aktiv,Fertig,Idle',
                'geheim'          => 'required|in:yes,no',
                'startdatum'      => 'nullable|date',
                'enddatum'        => 'nullable|date|after_or_equal:startdatum',
                'vortragdatum'    => 'nullable|date|after_or_equal:startdatum',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
    
            if (in_array($request->status, ['Aktiv', 'Fertig']) && count($request->projektart) !== 1) {
                return back()->withInput()->withErrors(['projektart' => 'Bei Status "Aktiv" oder "Fertig" darf nur eine Projektart ausgewählt werden.']);
            }
    
            $semester = null;
            if (in_array($request->semester_type, ['WS', 'SS'])) {
                $semester = $request->semester_type . ($request->semester_year ?? '');
            }
    
            $pdf1Path = null;
            $pdf2Path = null;
            if ($request->hasFile('pdf_1')) {
                $pdf1Path = $request->file('pdf_1')->store('thesis_pdfs', 'public');
            }
            if ($request->hasFile('pdf_2')) {
                $pdf2Path = $request->file('pdf_2')->store('thesis_pdfs', 'public');
            }
    
            $thesis = Thesis::create([
                'name'        => $request->name,
                'betreuer'    => auth()->user()->praefix . ' ' . auth()->user()->name . ' ' . auth()->user()->nachname,
                'description' => $request->description,
                'kenntnisse'  => $request->kenntnisse,
                'status'      => $request->status,
                'prof_id'     => auth()->user()->id,
                'notiz'       => $request->notiz,
                'semester'    => $semester,
                'pdf_1'       => $pdf1Path,
                'pdf_2'       => $pdf2Path,
                'projektart'  => $request->input('projektart', []),
                'geheim'      => $request->input('geheim'),
                'startdatum'  => $request->startdatum,
                'enddatum'    => $request->enddatum,
                'vortragdatum' => $request->vortragdatum,
            ]);
    
            return redirect()->route('prof.dashboard')->with('success', 'Thesis wurde erfolgreich erstellt.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified thesis
     *
     * @OA\Get(
     *     path="/prof/thesis/{id}",
     *     tags={"Thesis"},
     *     summary="Show details of a specific thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the thesis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with thesis details",
     *         @OA\JsonContent(
     *             @OA\Property(property="thesis", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="betreuer", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="kenntnisse", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="display_status", type="string"),
     *                 @OA\Property(property="interesse", type="array", @OA\Items(type="string"))
     *             ),
     *             @OA\Property(property="studentEmail", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect if unauthorized to view secret thesis",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thesis not found"
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $thesis = Thesis::findOrFail($id);
        $user = Auth::user();
    
        if ($thesis->geheim === 'yes') {
            if (!$user || $thesis->prof_id !== $user->id) {
                return redirect()->route('welcome')->with('error', 'Sie haben keine Berechtigung, diese geheime Thesis anzusehen.');
            }
        }
    
        $studentEmail = ($user && $user->role === 'Student') ? $user->email : null;
    
        if (!Auth::check() || (Auth::check() && Auth::user()->roll === 'Student')) {
            $thesis->display_status = ($thesis->status === 'Angebot') ? 'Aktiv' : 'Inaktiv';
        } else {
            $thesis->display_status = $thesis->status;
        }

        if (!$user || $thesis->prof_id !== $user->id) {
            $thesis->startdatum = null;
            $thesis->enddatum = null;
            $thesis->vortragdatum = null;
            $thesis->notiz = null;
            $thesis->geheim = null;
            $thesis->semester = null;
        }

        if (Auth::check()) {
            $thesis->interesse = $thesis->interestedUsers()
                ->wherePivot('expires_at', '>', now())
                ->pluck('email')
                ->all(); 
        } else {
            $thesis->interesse = $thesis->interestedUsers()
                ->wherePivot('expires_at', '>', now())
                ->count();
        }
    
        return view('thesis.show', compact('thesis', 'studentEmail'));
    }

    /**
     * Register or withdraw student interest in a thesis
     *
     * @OA\Post(
     *     path="/thesis/{id}/interesse",
     *     tags={"Thesis"},
     *     summary="Register or withdraw student interest in a thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the thesis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful interest registration/withdrawal with redirect",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not a student"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thesis not found"
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function interesse(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->roll !== 'Student') {
            return redirect()->back()->with('error', 'Nur Studenten können Interesse bekunden.');
        }

        $thesis = Thesis::findOrFail($id);
        $user = Auth::user();

        if ($thesis->interestedUsers->contains($user->id)) {
            $thesis->interestedUsers()->detach($user->id);
            return redirect()->back()->with('success', 'Ihr Interesse wurde erfolgreich zurückgezogen.');
        } else {
            $thesis->interestedUsers()->attach($user->id, ['expires_at' => now()->addDays(60)]);
            return redirect()->back()->with('success', 'Ihr Interesse wurde erfolgreich registriert und bleibt ab heute 60 Tage lang gültig.');
        }
    }

    /**
     * Show the form for editing the specified thesis
     *
     * @OA\Get(
     *     path="/prof/thesis/{id}/edit",
     *     tags={"Thesis"},
     *     summary="Display form to edit a thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the thesis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with the edit form view",
     *         @OA\MediaType(
     *             mediaType="text/html"
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Thesis belongs to another professor"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thesis not found"
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $thesis = Thesis::findOrFail($id);
    
        if ($thesis->prof_id !== auth()->user()->id) {
            return redirect()->route('prof.dashboard')->withErrors('Diese Thesis gehört einem anderen Professor, Sie haben keine Berechtigung, sie zu bearbeiten.');
        }
    
        return view('thesis.edit', compact('thesis'));
    }

    /**
     * Update the specified thesis in storage
     *
     * @OA\Put(
     *     path="/prof/thesis/{id}",
     *     tags={"Thesis"},
     *     summary="Update an existing thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the thesis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=255, description="Thesis title"),
     *                 @OA\Property(property="description", type="string", description="Thesis description"),
     *                 @OA\Property(property="kenntnisse", type="string", description="Required skills"),
     *                 @OA\Property(property="status", type="string", enum={"Angebot", "Aktiv", "Fertig", "Idle"}, description="Thesis status"),
     *                 @OA\Property(property="notiz", type="string", nullable=true, description="Additional notes"),
     *                 @OA\Property(property="semester_type", type="string", enum={"WS", "SS", "N/A"}, description="Semester type"),
     *                 @OA\Property(property="semester_year", type="string", nullable=true, pattern="^[0-9]{4}$", description="Semester year"),
     *                 @OA\Property(property="pdf_1", type="string", format="binary", nullable=true, description="First PDF file (max 5MB)"),
     *                 @OA\Property(property="pdf_2", type="string", format="binary", nullable=true, description="Second PDF file (max 5MB)"),
     *                 @OA\Property(property="delete_pdf_1", type="boolean", nullable=true, description="Delete first PDF"),
     *                 @OA\Property(property="delete_pdf_2", type="boolean", nullable=true, description="Delete second PDF"),
     *                 @OA\Property(property="projektart", type="array", @OA\Items(type="string", enum={"Teamprojekt", "Studienarbeit", "Bachelorthesis", "Masterthesis"}), description="Project types"),
     *                 @OA\Property(property="geheim", type="string", enum={"yes", "no"}, description="Is thesis secret?"),
     *                 @OA\Property(property="startdatum", type="string", format="date", nullable=true, description="Start date"),
     *                 @OA\Property(property="enddatum", type="string", format="date", nullable=true, description="End date"),
     *                 @OA\Property(property="vortragdatum", type="string", format="date", nullable=true, description="Presentation date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful update with redirect to thesis show page",
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
     *         response=403,
     *         description="Forbidden - Thesis belongs to another professor"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thesis not found"
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $thesis = Thesis::findOrFail($id);
    
            if ($thesis->prof_id !== auth()->user()->id) {
                return redirect()->route('prof.dashboard')->withErrors('Diese Thesis gehört einem anderen Professor, Sie haben keine Berechtigung, sie zu bearbeiten.');
            }
    
            $request->validate([
                'name'            => 'required|string|max:255',
                'description'     => 'required|string',
                'kenntnisse'      => 'required|string',
                'status' => 'required|in:Angebot,Aktiv,Fertig,Idle',
                'notiz'           => 'nullable|string',
                'semester_type'   => 'required|in:WS,SS,N/A',
                'semester_year'   => 'nullable|digits:4',
                'pdf_1'           => 'nullable|file|mimes:pdf|max:5120',
                'pdf_2'           => 'nullable|file|mimes:pdf|max:5120',
                'delete_pdf_1'    => 'nullable|boolean', 
                'delete_pdf_2'    => 'nullable|boolean', 
                'projektart'      => 'nullable|array', 
                'projektart.*'    => 'in:Teamprojekt,Studienarbeit,Bachelorthesis,Masterthesis',
                'geheim'          => 'required|in:yes,no', 
                'startdatum'      => 'nullable|date',
                'enddatum'        => 'nullable|date|after_or_equal:startdatum',
                'vortragdatum'    => 'nullable|date|after_or_equal:startdatum',
            ]);

            if (in_array($request->status, ['Aktiv', 'Fertig']) && count($request->projektart ?? []) !== 1) {
                return back()->withInput()->withErrors(['projektart' => 'Bei Status "Aktiv" oder "Fertig" darf nur eine Projektart ausgewählt werden.']);
            }
    
            if ($request->boolean('delete_pdf_1') && $thesis->pdf_1) {
                \Storage::disk('public')->delete($thesis->pdf_1);
                $thesis->pdf_1 = null; 
            }
            if ($request->boolean('delete_pdf_2') && $thesis->pdf_2) {
                \Storage::disk('public')->delete($thesis->pdf_2); 
                $thesis->pdf_2 = null;
            }
    
            if ($request->hasFile('pdf_1') && !$thesis->pdf_1) {
                $thesis->pdf_1 = $request->file('pdf_1')->store('thesis_pdfs', 'public');
            }
            if ($request->hasFile('pdf_2') && !$thesis->pdf_2) {
                $thesis->pdf_2 = $request->file('pdf_2')->store('thesis_pdfs', 'public');
            }
    
            $semester = null;
            if (in_array($request->semester_type, ['WS', 'SS'])) {
                $semester = $request->semester_type . ($request->semester_year ?? '');
            }

            $newGeheim = $request->input('geheim', 'no'); 
            if ($newGeheim === 'yes' && $thesis->geheim !== 'yes') {
                if ($thesis->interestedUsers()->count() > 0) {
                    $thesis->interestedUsers()->detach();
                }
            }

            $thesis->update([
                'name'        => $request->name,
                'description' => $request->description,
                'kenntnisse'  => $request->kenntnisse,
                'status'      => $request->status,
                'notiz'       => $request->notiz,
                'semester'    => $semester,
                'pdf_1'       => $thesis->pdf_1,
                'pdf_2'       => $thesis->pdf_2,
                'betreuer'    => auth()->user()->praefix . ' ' . auth()->user()->name . ' ' . auth()->user()->nachname,
                'projektart'  => $request->input('projektart', []),
                'geheim'      => $newGeheim,
                'vortragdatum' => $request->vortragdatum,
                'startdatum'  => $request->startdatum,
                'enddatum'    => $request->enddatum,
            ]);

            return redirect()->route('thesis.show', $thesis->id)->with('success', 'Thesis erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            return redirect()->route('thesis.edit', $id)->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified thesis from storage
     *
     * @OA\Delete(
     *     path="/prof/thesis/{id}",
     *     tags={"Thesis"},
     *     summary="Delete a thesis",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the thesis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successful deletion with redirect to professor dashboard",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Thesis belongs to another professor"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thesis not found"
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $thesis = Thesis::findOrFail($id);

            if ($thesis->prof_id !== auth()->user()->id) {
                return redirect()->route('prof.dashboard')->withErrors('Diese Thesis gehört einem anderen Professor. Sie haben keine Berechtigung, sie zu löschen.');
            }

            $thesis->delete();

            return redirect()->route('prof.dashboard')->with('success', 'Thesis wurde erfolgreich gelöscht.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the student's list of interested theses
     *
     * @OA\Get(
     *     path="/student/merkliste",
     *     tags={"Thesis"},
     *     summary="List theses the authenticated student is interested in",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with interested theses list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="theses",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="projektart", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="interesse", type="array", @OA\Items(type="string"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not a student"
     *     )
     * )
     *
     * @return \Illuminate\View\View
     */
    public function merkliste()
    {
        $user = Auth::user();
        $theses = $user->interestedTheses()->get();
    
        $theses->each(function ($thesis) {
            $thesis->interesse = $thesis->interestedUsers->pluck('email')->map(function ($email) {
                return explode('@', $email)[0];
            })->all();
        });
    
        return view('student.merkliste', compact('theses'));
    }

    /**
     * Display a listing of secret theses for the authenticated professor
     *
     * @OA\Get(
     *     path="/prof/geheimthesis",
     *     tags={"Thesis"},
     *     summary="List secret theses for the authenticated professor",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with paginated secret theses list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="theses",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="interesse", type="array", @OA\Items(type="string"))
     *                 )
     *             )
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
    public function geheimthesis()
    {
        $theses = Thesis::where('prof_id', auth()->user()->id)
                        ->where('geheim', 'yes')
                        ->paginate(15);

        $theses->each(function ($thesis) {
            $thesis->interesse = $thesis->interestedUsers()
                ->wherePivot('expires_at', '>', now())
                ->pluck('email')
                ->map(function ($email) {
                    return explode('@', $email)[0];
                })->all();
        });
        return view('prof.geheimthesis', compact('theses'));
    }

    /**
     * Display an overview of completed theses for the authenticated professor
     *
     * @OA\Get(
     *     path="/prof/uebersicht",
     *     tags={"Thesis"},
     *     summary="List completed theses with filters for the authenticated professor",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter theses by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="semester",
     *         in="query",
     *         description="Filter theses by semester",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="startdatum",
     *         in="query",
     *         description="Filter theses by start year",
     *         required=false,
     *         @OA\Schema(type="string", pattern="^[0-9]{4}$")
     *     ),
     *     @OA\Parameter(
     *         name="enddatum",
     *         in="query",
     *         description="Filter theses by end year",
     *         required=false,
     *         @OA\Schema(type="string", pattern="^[0-9]{4}$")
     *     ),
     *     @OA\Parameter(
     *         name="vortragdatum",
     *         in="query",
     *         description="Filter theses by presentation year",
     *         required=false,
     *         @OA\Schema(type="string", pattern="^[0-9]{4}$")
     *     ),
     *     @OA\Parameter(
     *         name="projektart",
     *         in="query",
     *         description="Filter theses by project type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Teamprojekt", "Studienarbeit", "Bachelorthesis", "Masterthesis"})
     *     ),
     *     @OA\Parameter(
     *         name="geheim",
     *         in="query",
     *         description="Filter theses by secret status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Ja", "Nein"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with paginated completed theses list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="theses",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="semester", type="string"),
     *                     @OA\Property(property="startdatum", type="string", format="date"),
     *                     @OA\Property(property="enddatum", type="string", format="date"),
     *                     @OA\Property(property="vortragdatum", type="string", format="date"),
     *                     @OA\Property(property="projektart", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="geheim", type="string")
     *                 )
     *             )
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
     * @return \Illuminate\View\View
     */
    public function uebersicht(Request $request)
    {
        $query = Thesis::where('prof_id', auth()->user()->id)
                       ->where('status', 'Fertig');
    
        if ($name = $request->input('name')) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($semester = $request->input('semester')) {
            $query->where('semester', 'like', '%' . $semester . '%');
        }
        if ($startdatum = $request->input('startdatum')) {
            $query->whereYear('startdatum', $startdatum);
        }
        if ($enddatum = $request->input('enddatum')) {
            $query->whereYear('enddatum', $enddatum);
        }
        if ($vortragdatum = $request->input('vortragdatum')) {
            $query->whereYear('vortragdatum', $vortragdatum);
        }
        if ($projektart = $request->input('projektart')) {
            $query->whereJsonContains('projektart', $projektart);
        }
        if ($geheim = $request->input('geheim')) {
            $query->where('geheim', $geheim === 'Ja' ? 'yes' : 'no');
        }
    
        $theses = $query->paginate(15)->appends($request->query());
    
        return view('prof.profuebersicht', compact('theses'));
    }
}