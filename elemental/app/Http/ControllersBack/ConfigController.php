<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Config;
use DB;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = Config::all();
        return view('settings.index', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Config::find($id);
        return view('settings.edit', compact('model'));
    }

    
    public function update(Request $request, $id)
    {
        $model = Config::find($id);
        $model->name = $request->name;
        $model->value = $request->value;
        $model->save();
        return redirect('/settings');
    }
}