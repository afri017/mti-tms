<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::with(['sourceData', 'destinationData'])->get();
        return view('routes.index', compact('routes'));
    }

    public function create()
    {
        $sources = Source::where('type', 'Source')->get();
        $destinations = Source::where('type', 'Destination')->get();
        return view('routes.create', compact('sources', 'destinations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source' => 'required|exists:sources,id',
            'destination' => 'required|exists:sources,id|different:source',
            'leadtime' => 'required|integer|min:0',
        ], [
            'source.required' => 'Source harus dipilih.',
            'source.exists' => 'Source tidak ditemukan.',
            'destination.required' => 'Destination harus dipilih.',
            'destination.exists' => 'Destination tidak ditemukan.',
            'destination.different' => 'Source dan Destination tidak boleh sama.',
            'leadtime.required' => 'Leadtime harus diisi.',
            'leadtime.integer' => 'Leadtime harus berupa angka.',
            'leadtime.min' => 'Leadtime minimal 0 hari.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 🔍 Cek duplikat kombinasi source + destination
        $exists = Route::where('source', $request->source)
            ->where('destination', $request->destination)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Kombinasi Source dan Destination sudah ada!')
                ->withInput();
        }

        // 🔍 Generate ID baru
        $routeId = Route::generateRouteId();

        // 🔍 Cek duplikat ID (meskipun jarang, tetap aman)
        if (Route::where('route', $routeId)->exists()) {
            return redirect()->back()
                ->with('error', 'Terjadi duplikasi ID route. Silakan coba lagi.')
                ->withInput();
        }

        // 🔄 Simpan data baru
        Route::create([
            'route' => $routeId,
            'source' => $request->source,
            'destination' => $request->destination,
            'leadtime' => $request->leadtime,
            'created_by' => auth()->user()->name ?? 'system',
            'last_update' => now(),
        ]);

        return redirect()->route('routes.index')->with('success', 'Route berhasil disimpan!');
    }

    public function edit($id)
    {
        $route = Route::findOrFail($id);
        $sources = Source::where('type', 'Source')->get();
        $destinations = Source::where('type', 'Destination')->get();
        return view('routes.edit', compact('route', 'sources', 'destinations'));
    }

    public function update(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'source' => 'required|exists:sources,id',
            'destination' => 'required|exists:sources,id|different:source',
            'leadtime' => 'required|integer|min:0',
        ], [
            'source.required' => 'Source harus dipilih.',
            'source.exists' => 'Source tidak ditemukan.',
            'destination.required' => 'Destination harus dipilih.',
            'destination.exists' => 'Destination tidak ditemukan.',
            'destination.different' => 'Source dan Destination tidak boleh sama.',
            'leadtime.required' => 'Leadtime harus diisi.',
            'leadtime.integer' => 'Leadtime harus berupa angka.',
            'leadtime.min' => 'Leadtime minimal 0 hari.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek apakah kombinasi source + destination sudah ada di route lain
        $duplicate = Route::where('source', $request->source)
            ->where('destination', $request->destination)
            ->where('route', '!=', $route->route)
            ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'Kombinasi Source dan Destination sudah ada pada route lain.'])
                ->withInput();
        }

        $route->update([
            'source' => $request->source,
            'destination' => $request->destination,
            'leadtime' => $request->leadtime,
            'update_by' => auth()->user()->name ?? 'system',
            'last_update' => now(),
        ]);

        return redirect()->route('routes.index')
            ->with('success', 'Route berhasil diperbarui.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Route berhasil dihapus');
    }
}
