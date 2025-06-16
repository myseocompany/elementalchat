<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompetitorStore;
use App\Models\Franchise;

class CompetitorStoreController extends Controller
{
    public function index(Request $request)
    {
        $model = CompetitorStore::with('franchise')
            ->where(function($q) use ($request){
                if(isset($request->year))
                    $q->where('opened_year', $request->year);
            })
            ->orderBy('opened_year', 'desc')
            ->get();

        $years = CompetitorStore::select('opened_year')->distinct()->orderBy('opened_year', 'desc')->pluck('opened_year');
        return view('competitor_stores.index', compact('model','years','request'));
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
