<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompetitorStore;
use App\Models\Franchise;

class CompetitorStoreController extends Controller
{
    public function index(Request $request)
    {
        $years = CompetitorStore::select('opened_year')
            ->distinct()
            ->orderBy('opened_year', 'desc')
            ->pluck('opened_year');

        $model = CompetitorStore::with('franchise')
            ->when($request->input('years'), function ($q) use ($request) {
                $q->whereIn('opened_year', (array) $request->input('years'));
            })
            ->orderBy('opened_year', 'desc')
            ->get();

        return view('competitor_stores.index', [
            'model' => $model,
            'years' => $years,
            'request' => $request
        ]);
    }

    public function create()
    {
        $franchises = Franchise::all();
        return view('competitor_stores.create', compact('franchises'));
    }

    public function store(Request $request)
    {
        $model = new CompetitorStore;
        $model->name = $request->name;
        $model->address = $request->address;
        $model->latitude = $request->latitude;
        $model->longitude = $request->longitude;
        $model->franchise_id = $request->franchise_id;
        $model->opened_year = $request->opened_year;
        $model->save();
        return redirect('/competitor-stores');
    }

    public function edit($id)
    {
        $model = CompetitorStore::find($id);
        $franchises = Franchise::all();
        return view('competitor_stores.edit', compact('model','franchises'));
    }

    public function update(Request $request, $id)
    {
        $model = CompetitorStore::find($id);
        $model->name = $request->name;
        $model->address = $request->address;
        $model->latitude = $request->latitude;
        $model->longitude = $request->longitude;
        $model->franchise_id = $request->franchise_id;
        $model->opened_year = $request->opened_year;
        $model->save();
        return redirect('/competitor-stores');
    }

    public function destroy($id)
    {
        $model = CompetitorStore::find($id);
        $model->delete();
        return redirect('/competitor-stores');
    }
}
