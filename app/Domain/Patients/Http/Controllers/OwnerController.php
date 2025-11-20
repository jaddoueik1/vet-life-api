<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OwnerController extends Controller
{
    public function index()
    {
        return Owner::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);
        return Owner::create($data);
    }

    public function show(Owner $owner)
    {
        return $owner;
    }

    public function update(Request $request, Owner $owner)
    {
        $owner->update($request->all());
        return $owner;
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();
        return response()->noContent();
    }
}
