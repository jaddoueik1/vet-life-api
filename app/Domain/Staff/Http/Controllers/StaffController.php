<?php

namespace App\Domain\Staff\Http\Controllers;

use App\Domain\Staff\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StaffController extends Controller
{
    public function index()
    {
        return StaffMember::where('role', StaffMember::ROLE_STAFF)->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:staff_members,email',
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $staff = StaffMember::create(array_merge($data, [
            'role' => StaffMember::ROLE_STAFF,
        ]));

        return $staff;
    }

    public function show(StaffMember $staff)
    {
        $this->ensureStaffMember($staff);

        return $staff;
    }

    public function update(Request $request, StaffMember $staff)
    {
        $this->ensureStaffMember($staff);

        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:staff_members,email,' . $staff->id,
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $staff->update($data);

        return $staff;
    }

    public function destroy(StaffMember $staff)
    {
        $this->ensureStaffMember($staff);

        $staff->delete();

        return response()->noContent();
    }

    protected function ensureStaffMember(StaffMember $staff): void
    {
        abort_unless($staff->role === StaffMember::ROLE_STAFF, 404);
    }
}
