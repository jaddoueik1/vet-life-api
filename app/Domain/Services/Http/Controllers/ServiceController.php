<?php

namespace App\Domain\Services\Http\Controllers;

use App\Domain\Services\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        return Service::create($data);
    }

    public function show(Service $service)
    {
        return $service;
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'duration' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);


        $service->update($data);

        return $service;
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->noContent();
    }
}
