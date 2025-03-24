<?php

namespace App\Services;

use DB;
use Auth;
use Carbon;
use Illuminate\Http\Request;
use App\Menu;
use App\CustomerStatus;
use App\Customer;

class CustomerService {

    public function filterCustomers(Request $request, $statuses, $stage_id, $countOnly = false, $pageSize = 0)
    {
        $status_array = CustomerStatus::where('stage_id', $stage_id)->pluck('id')->toArray();
        $dates = $this->getDates($request);
        //dd($request);
        $query = Customer::leftJoin('customer_statuses', 'customers.status_id', '=', 'customer_statuses.id')
            ->where(function ($query) use ($stage_id, $dates, $request) {
                if (!empty($stage_id) && empty($request->search))
                    $query->where('customer_statuses.stage_id', $stage_id);

                if (!empty($request->from_date)) {
                    $column = ($request->created_updated === "created") ? 'created_at' : 'updated_at';
                    $query->whereBetween("customers.$column", $dates);
                }
                //dd(!empty($request->user_id));
                if (isset($request->user_id)) {
                    
                    $query->where('customers.user_id', $request->user_id === "null" ? null : $request->user_id);
                }

                if (isset($request->maker)) {
                    is_numeric($request->maker) ? 
                        $query->where('customers.maker', $request->maker) : 
                        $query->whereNull('customers.maker');
                }
                if (isset($request->scoring)) {
                    is_numeric($request->scoring) ? 
                        $query->where('customers.scoring', $request->scoring) : 
                        $query->whereNull('customers.scoring');
                }

                if (!empty($request->product_id)) {
                    $query->whereIn(
                        'customers.product_id', 
                        $request->product_id == 1 ? [1, 6, 7, 8, 9, 10, 11] : [$request->product_id]
                    );
                }

                if (!empty($request->source_id)) $query->where('customers.source_id', $request->source_id);
                if (!empty($request->country)) $query->where('customers.country', $request->country);
                if (!empty($request->status_id)) $query->where('customers.status_id', $request->status_id);
                if (!empty($request->scoring_interest)) $query->where('customers.scoring_interest', $request->scoring_interest);
                if (!empty($request->inquiry_product_id)) $query->where('customers.inquiry_product_id', $request->inquiry_product_id);

                if (!empty($request->search)) {
                    $normalizedSearch = $this->normalizePhoneNumber($request->search);
                    $query->where(function ($innerQuery) use ($request, $normalizedSearch) {
                        $innerQuery->orWhere('customers.name', 'like', "%{$request->search}%")
                            ->orWhere('customers.email', 'like', "%{$request->search}%")
                            ->orWhere('customers.document', 'like', "%{$request->search}%")
                            ->orWhere('customers.position', 'like', "%{$request->search}%")
                            ->orWhere('customers.business', 'like', "%{$request->search}%")
                            ->orWhere('customers.notes', 'like', "%{$request->search}%")
                            ;

                        if ($this->looksLikePhoneNumber($request->search)) {
                            $innerQuery->orWhereRaw("REPLACE(REPLACE(REPLACE(customers.phone, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                            $innerQuery->orWhereRaw("REPLACE(REPLACE(REPLACE(customers.phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                            $innerQuery->orWhereRaw("REPLACE(REPLACE(REPLACE(customers.contact_phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                        }
                    });
                }
            });

        if ($countOnly) {
            $query = $query->select(
                DB::raw('count(distinct(customers.id)) as count'),
                'customers.status_id',
                DB::raw('COALESCE(customer_statuses.name, "Sin Estado") as status_name'),
                DB::raw('COALESCE(customer_statuses.color, "#000000") as status_color'),
                
            )
            ->groupBy('customers.status_id', 'customer_statuses.name', 'customer_statuses.color') // Asegurar compatibilidad en MySQL
            ->orderBy('customer_statuses.weight', 'ASC')
            ->get();
        } else {
            
            if($pageSize>0){
                $query = $query->select(
                    'customers.*',
                    DB::raw('COALESCE(customer_statuses.name, "Sin Estado") as status_name'),
                    DB::raw('COALESCE(customer_statuses.color, "#000000") as status_color') // Color por defecto
                )->orderBy('customers.created_at', 'DESC')
                ->paginate($pageSize);
            }else{
                $query = $query->select(
                    'customers.*',
                    DB::raw('COALESCE(customer_statuses.name, "Sin Estado") as status_name'),
                    DB::raw('COALESCE(customer_statuses.color, "#000000") as status_color') // Color por defecto
                )->orderBy('customers.created_at', 'DESC')
                ->get();
            }
            
        }

        $query->action = "customers/phase/" . $stage_id;
        $query->phase_id = $stage_id;
        return $query;
    }


    public function getDates($request)
    {
        $to_date = Carbon\Carbon::today()->subDays(2); // ayer
        $from_date = Carbon\Carbon::today()->subDays(5000);
        if (isset($request->from_date) && ($request->from_date != null)) {
            $from_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->from_date);
            $to_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->to_date);
        }
        $to_date = $to_date->format('Y-m-d') . " 23:59:59";
        $from_date = $from_date->format('Y-m-d');
        return array($from_date, $to_date);
    }

    // Función para normalizar números de teléfono
    function normalizePhoneNumber($phoneNumber)
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }

    function looksLikePhoneNumber($input)
    {
        return preg_match('/^\+?\d{1,4}(\s|-)?(\d{1,4}(\s|-)?){1,4}$/', $input);
    }

}