<?php

namespace App\Domain\Staff\Http\Controllers;

use App\Domain\Staff\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VeterinarianController extends Controller
{
    public function index()
    {
        return StaffMember::where('role', StaffMember::ROLE_VETERINARIAN)->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:staff_members,email|unique:users,email',
            'phone' => 'nullable|string',
            'specialization' => 'nullable|string',
            'password' => 'required|string|min:8',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $user = \App\Domain\Users\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
            ]);

            $role = \App\Domain\Users\Models\Role::where('slug', 'veterinarian')->firstOrFail();
            $user->roles()->attach($role);

            $veterinarian = StaffMember::create(array_merge($data, [
                'role' => StaffMember::ROLE_VETERINARIAN,
            ]));

            return $veterinarian;
        });
    }

    public function show(StaffMember $veterinarian)
    {
        $this->ensureVeterinarian($veterinarian);

        return $veterinarian;
    }

    public function update(Request $request, StaffMember $veterinarian)
    {
        $this->ensureVeterinarian($veterinarian);

        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:staff_members,email,' . $veterinarian->id,
            'phone' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $veterinarian->update($data);

        return $veterinarian;
    }

    public function destroy(StaffMember $veterinarian)
    {
        $this->ensureVeterinarian($veterinarian);

        \Illuminate\Support\Facades\DB::transaction(function () use ($veterinarian) {
            $user = \App\Domain\Users\Models\User::where('email', $veterinarian->email)->first();

            if ($user && $user->hasRole('veterinarian')) {
                 // Detach roles to be clean, though user delete might handle cascade
                 $user->roles()->detach();
                 $user->delete();
            }

            $veterinarian->delete();
        });

        return response()->noContent();
    }

    protected function ensureVeterinarian(StaffMember $veterinarian): void
    {
        abort_unless($veterinarian->role === StaffMember::ROLE_VETERINARIAN, 404);
    }
}
