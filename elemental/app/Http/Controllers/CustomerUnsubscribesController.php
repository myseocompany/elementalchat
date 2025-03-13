<?php

namespace App\Http\Controllers;

use App\CustomerUnsubscribe;
use Illuminate\Http\Request;


class CustomerUnsubscribesController extends Controller{

    public function index(){
        $model = CustomerUnsubscribe::orderBy("created_at", "DESC")->paginate(10);


        return view('customers_unsubscribes.index', compact('model'));
    }

    public function save(Request $request){
        $model = new CustomerUnsubscribe;
        $model->phone = $request->phone;

        $model->save();

        return redirect('/customer_unsubscribes');

    }

    public function edit(Request $request, $phone){
        $model = CustomerUnsubscribe::where("phone", $phone)->first();

        

        return view('customers_unsubscribes.edit', compact('model'));
    }

    public function update(Request $request, $phone){
        $model = CustomerUnsubscribe::where("phone", $phone)->first();
        $model->phone = $request->phone;
        $model->save();

        return redirect('/customer_unsubscribes');

    }

    public function destroy(Request $request, $phone){
        $model = CustomerUnsubscribe::where("phone", $phone)->first();
        
        if ($model->delete()) {

            return redirect('customer_unsubscribes')->with('status', 'El telefono <strong>' . $model->phone . '</strong> fué eliminado con éxito!');
        }

        return redirect('/customer_unsubscribes');

    }

}