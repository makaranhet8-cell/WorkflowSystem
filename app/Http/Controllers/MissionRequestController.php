<?php
namespace App\Http\Controllers;

use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionRequestController extends Controller
{
    public function index()
{
    /** @var User $user */
    $user = Auth::user();

    // បង្កើត Query មូលដ្ឋាន
    $query = MissionRequest::with('user.departments')->latest();

    // បន្ថែមលក្ខខណ្ឌចម្រោះសម្រាប់ Role Admin
    if ($user->hasRole('admin')) {
        $adminDeptIds = $user->departments->pluck('id');

        // បង្ខំឱ្យបង្ហាញតែសំណើរបស់បុគ្គលិកដែលនៅក្នុង Department ជាមួយ Admin ប៉ុណ្ណោះ
        $query->whereHas('user.departments', function($q) use ($adminDeptIds) {
            $q->whereIn('departments.id', $adminDeptIds);
        });
    }
    // សម្រាប់បុគ្គលិកធម្មតា (Staff) ឱ្យឃើញតែសំណើផ្ទាល់ខ្លួន
    elseif (!$user->isApproverOrDepartmentAdmin()) {
        $query->where('user_id', $user->id);
    }

    $missionRequests = $query->get();

    return view('mission_requests.index', compact('missionRequests'));
}

    public function create()
    {
        $users = User::whereNotIn('role', [
            'system_admin','admin', 'ceo', 'hr_manager', 'team_leader', 'department_admin', 'cfo'
        ])->get();

        return view('mission_requests.create', compact('users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'destination' => 'required|string|max:255',
            'purpose'     => 'required|string|max:500',
            'start_date'  => 'required|date', // ដក after:today ចេញសិនបើអ្នកចង់តេស្តថ្ងៃនេះ
            'end_date'    => 'required|date|after_or_equal:start_date',
        ];

        /** @var User|null $user */
        $user = Auth::user();
        $userId = Auth::id();

        // បើកសិទ្ធិឱ្យ Admin រើស User បាន
        if ($user instanceof User && $user->isDepartmentAdmin()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        if ($user instanceof User && $user->isDepartmentAdmin()) {
            $userId = $request->input('user_id');
        }

        MissionRequest::create([
            'user_id'     => $userId,
            'destination' => $request->destination,
            'purpose'     => $request->purpose,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'status'      => 'pending_tl', // កុំភ្លេចដាក់ status ដើម
        ]);

        // ប្តូរការ Redirect ទៅកាន់ទំព័របញ្ជី Mission Requests វិញ
        return redirect()->route('mission-requests.index')->with('success', 'Mission request submitted successfully.');
    }

    public function edit($id)
    {
        $missionRequest = MissionRequest::findOrFail($id);
        return view('mission_requests.edit', compact('missionRequest'));
    }

    public function update(Request $request, $id)
    {
        $missionRequest = MissionRequest::findOrFail($id);

        if ($request->has('user_name')) {
        $missionRequest->user->update([
            'name' => $request->user_name
        ]);
    }

        // ត្រូវ Validate មុននឹង Update
        $request->validate([
            'destination' => 'required|string',
            'purpose'     => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        // Update ព័ត៌មាន (ប្រសិនបើចង់ Update ឈ្មោះ User ត្រូវប្រើ Logic ក្នុង Response មុន)
        $missionRequest->update([
            'destination' => $request->destination,
            'purpose'     => $request->purpose,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        return redirect()->route('mission-requests.index')->with('success', 'Updated successfully!');
    }

    public function destroy($id)
    {
        $missionRequest = MissionRequest::findOrFail($id);
        $missionRequest->delete();

        return redirect()->route('mission-requests.index')->with('success', 'លុបបានជោគជ័យ!');
    }

    public function show(MissionRequest $missionRequest)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! ($user instanceof User) || ($missionRequest->user_id !== $user->id && ! $user->isApproverOrDepartmentAdmin())) {
            abort(403);
        }

        return view('mission_requests.show', compact('missionRequest'));
    }
}
