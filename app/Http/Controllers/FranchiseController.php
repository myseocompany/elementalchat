<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franchise;

class FranchiseController extends Controller
{
    public function index()
    {
        $model = Franchise::all();
        return view('franchises.index', compact('model'));
    }

    public function create()
    {
        return view('franchises.create');
    }

    public function store(Request $request)
    {
        $model = new Franchise();
        $model->name = $request->name;
        $model->color = $request->color;
        $model->save();
        return redirect('/franchises');
    }

    public function edit($id)
    {
        $model = Franchise::find($id);
        return view('franchises.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = Franchise::find($id);
        $model->name = $request->name;
        $model->color = $request->color;
        $model->save();
        return redirect('/franchises');
    }

    public function destroy($id)
    {
        $model = Franchise::find($id);
        $model->delete();
        return redirect('/franchises');
    }
}
