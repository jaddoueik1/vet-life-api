<?php

namespace App\Domain\Vendors\Http\Controllers;

use App\Domain\Vendors\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VendorController extends Controller
{
    public function index()
    {
        return Vendor::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'main_contact_name' => 'nullable|string',
            'main_contact_email' => 'nullable|email',
            'main_contact_phone' => 'nullable|string',
            'secondary_contact_name' => 'nullable|string',
            'secondary_contact_email' => 'nullable|email',
            'secondary_contact_phone' => 'nullable|string',
        ]);

        return Vendor::create($data);
    }

    public function show(Vendor $vendor)
    {
        return $vendor;
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'main_contact_name' => 'nullable|string',
            'main_contact_email' => 'nullable|email',
            'main_contact_phone' => 'nullable|string',
            'secondary_contact_name' => 'nullable|string',
            'secondary_contact_email' => 'nullable|email',
            'secondary_contact_phone' => 'nullable|string',
        ]);

        $vendor->update($data);

        return $vendor;
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->noContent();
    }
}
