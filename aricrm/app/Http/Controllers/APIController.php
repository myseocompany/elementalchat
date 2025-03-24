<?php
//PARA TU PIEL
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;

use App\Customer;
use App\CustomerStatus;
use App\CustomerAudience;
use App\User;
use App\CustomerSource;
use App\CustomerHistory;
use App\EmailBrochure;
use App\Action;
use App\Project;

use App\Email;
use App\ActionType;
use Auth;
use Carbon;
use App\SendWithData;
use App\AudienceCustomer;
use App\RequestLogs;
use App\Session;
use App\Order;
use App\Log;

class APIController extends Controller
{

    protected $attributes = ['status_name'];

    protected $appends = ['status_name'];

    protected $status_name;

    protected $menu = "1️⃣ Cotizar una fórmula médica
2️⃣ Cotizar un producto específico.
3️⃣ Conocer productos para tu tipo de piel
4️⃣ Contactar un asesor";

    protected $cierre = "¡Perfecto! Tenemos tu información, un asesor se contactará contigo lo más pronto posible.

    0️⃣. Para volver al menú inicial.";

    protected $formulado = "Eres un paciente formulado por un excelente Dermátologo (@).
Recuerda seguir las indicaciones y recomendaciones y no olvides tu cita de control.
Te deseamos excelente resultado en tu tratamiento";


    public function __construct() {}

    public function index(Request $request)
    {
        return $this->customers($request);
    }




    /*******************
    Desarrollador: Nicolas Navarro
    Objeto: recibir datos de dialogflow
    2022-06-09
     ******************/
    public function opendialog(Request $request)
    {
        /*
        $model = new RequestLogs;
        $model->text = $request;
        $model->save();
        */

        $data = $request->json()->all();
        //dd($request->json()->all());
        if (array_key_exists("queryResult", $data) && array_key_exists("action", $data["queryResult"])) {
            $action =  $data["queryResult"]['action'];
            $return = "";
            switch ($action) {
                case 'saveCustomer':
                    $return = $this->saveCustomerDialog($request);
                    break;
                case 'getTecnicalInformation':
                    $return = $this->getTecnicalInformation($request);
                    break;
                case 'showImage':
                    $return = $this->showImage($request);
                    break;
                case 'saveOrder':
                    $return = $this->saveOrder($request);
                    break;

                default:
                    $return = $this->getDefault();
                    break;
            }
        } else {
            $return = $this->getJSON('no existe el objeto ["queryResult"]["action"]');
            //$return = $this->getDefault();
        }
        return $return;
    }


    public function getTecnicalInformation($request)
    {
        $data = $request->json()->all();
        $params = $data["queryResult"]['parameters'];

        $machine = $params["Machine"];
        if ($machine == 1) {
            $texto0 = 'Nuestro soporte para dominadas y fondos cuenta con:

Agarres antideslizantes
Acabados en pintura electroestática

El peso máximo de usuario soportado es de 120 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 535 mm
Ancho: 650 mm
Largo: 1070 mm

Material:
Tubería de lámina redonda, diámetro 3/4 ̈, calibre 2mm,
curvada mecanizada sin juntas de soldadura.';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 2) {
            $texto0 = 'Nuestro soporte para dominadas y fondos con abdomen aéreo cuenta con: 

Agarres antideslizantes
Acabados en pintura electroestática

El peso máximo de usuario soportado es de 120 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 535 mm
Ancho: 800 mm
Largo: 1070 mm

Material:
Tubería de lámina redonda, diámetro 3/4̈, calibre 2mm,
curvada mecanizada sin juntas de soldadura.';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 3) {
            $texto0 = 'Nuestro banco multifuncional cuenta con: 

Cojineria en espuma prensada
Acabados en pintura electroestática

El peso máximo de usuario soportado es de 130 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 1300 mm
Ancho: 500 mm
Largo: 1210 mm

Material:
Perfilería estructural 40x40mm';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 4) {
            $texto0 = 'Nuestro banco plano cuenta con: 

Cojineria en espuma prensada
Acabados en pintura electroestática

El peso máximo de usuario soportado es de 130 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 400 mm
Ancho: 450 mm
Largo: 1160 mm

Material:
Perfilería estructural 40x40mm';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 5) {
            $texto0 = 'Nuestra polea cuenta con: 

Agarres antideslizantes
Acabados en pintura electroestática

El peso máximo soportado es de 120 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 2100 mm
Ancho: 390 mm
Largo: 500 mm

Material:
Perfilería estructural 40x40mm';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 6) {
            $texto0 = 'Nuestro rack para sentadilla cuenta con: 
Estructura desarmable para más fácil transporte
Acabados en pintura electroestática

El peso máximo soportado es de 180 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Alto: 1560 mm
Ancho: 600 mm
Largo mínimo: 11500 mm
Largo máximo: 1300 mm

Material:
Perfilería estructural 40x40mm';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 7) {
            $texto0 = 'Nuestra escalera multifuncional cuenta con: 

Estructura plegable para optimizar espacio cuando no está en uso
Cojineria en espuma prensada
Acabados en pintura electroestática

El peso máximo de usuario soportado es de 180 kg';
            $texto1 = 'Sus dimensiones 📐 son:

Equipo abierto
Alto: 2140 mm
Ancho: 1070 mm
Largo: 1540 mm

Equipo cerrado
Alto: 2010 mm
Ancho: 1070 mm
Largo: 680 mm

Material:
Tubería estructural ¾”, perfilería estructural 40x40mm.';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        if ($machine == 8) {
            $texto0 = 'Nuestras barras, discos y mancuernas cuentan con:

Discos: Perforación de 1”. Fundición gris recubierto con pintura estética
Barras: Acero zincado
Mancuernas: Discos de hierro, barra en tubería inoxidable grabada';
            $texto1 = '';
            $texto2 = "¿Estas interesado en adquirir este producto?
(Si - No)";
        }
        return response()->json(array(
            "fulfillmentMessages" => array(
                $this->getFulfillmentText($texto0),
                $this->getFulfillmentText($texto1),
                $this->getFulfillmentText($texto2),
            )
        ));
    }

    public function showImage($request)
    {
        $data = $request->json()->all();
        return response()->json(array(
            "fulfillmentMessages" => array(
                $this->getFulfillmentImage("https://trujillogutierrez.com.co/site/images/proyectos/acerca/equipo1.jpg"),
            )
        ));
    }


    public function saveCustomerDialog(Request $request)
    {
        $data = $request->json()->all();
        $params = $data["queryResult"]['parameters'];
        if (isset($params["name"]))
            $request->name = $params["name"];
        else
            $request->name = $params["phone"];

        if (isset($params["phone"]))
            $request->phone = $params["phone"];

        /*  GUARDO LA SESSION */
        if (isset($params["session"])) {
            $request->session_id = $params["session"];
        } else {
            $request->session_id = $this->getSession($request);
        }


        if (isset($params["source_id"]))
            $request->source_id = $params["source_id"];
        $this->saveAPI($request);
        $menu = "1️⃣ Cotizar una fórmula médica
2️⃣ Cotizar un producto específico.
3️⃣ Conocer productos para tu tipo de piel
4️⃣ Contactar un asesor";
        return response()->json(array(
            "fulfillmentMessages" => array(
                $this->getFulfillmentText($menu),
            )
        ));
    }
    public function getFulfillmentText($str)
    {
        return array(
            "text" => array(
                "text" => array($str),
            ),
        );
    }

    public function getFulfillmentImage($str)
    {
        return array(
            "image" => array(
                "imageUri" => array($str),
            ),
        );
    }

    public function getDefault()
    {
        return response()->json(array(
            "fulfillmentText" => 'Error: accion desconocida',
        ));
    }
    public function getJSON($str)
    {
        return response()->json(array(
            "fulfillmentText" => $str,
        ));
    }


    /***********Fin de Dialog flow***********/

    public function getPendingActions()
    {
        $model = Action::whereNotNull('due_date')
            ->whereNull('delivery_date')
            ->where('creator_user_id', "=", Auth::id())
            ->get();
        //dd($model);
        return $model;
    }

    public function customers(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);

        $model = $this->getModel($request, $statuses, 'customers');
        $customersGroup = $this->countFilterCustomers($request, $statuses);

        $projects = Project::all();

        $sources = CustomerSource::orderby('name')->get();

        $pending_actions = $this->getPendingActions();
        //dd($pending_actions);

        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources', 'projects', 'pending_actions'));
    }

    public function leads(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);
        $model = $this->getModel($request, $statuses, 'leads');
        $customersGroup = $this->countFilterCustomers($request, $statuses);
        $projects = Project::all();
        $pending_actions = $this->getPendingActions();


        $sources = CustomerSource::all();


        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources', 'projects', 'pending_actions'));
    }

    public function getModel(Request $request, $statuses, $action)
    {
        $model = $this->filterModel($request, $statuses);


        $model->getActualRows = $model->currentPage() * $model->perPage();

        if ($model->perPage() > $model->total())
            $model->getActualRows = $model->total();
        foreach ($model as $items) {
            if (isset($items->status_id)) {
                $status = CustomerStatus::find($items->status_id);
                if (isset($status))
                    $items->status_name = $status->name;
            }
        }
        $model->action = $action;
        return $model;
    }

    public function getUsers()
    {
        return  User::orderBy('name')
            ->where('users.status_id', 1)
            ->get();
    }

    public function getStatuses(Request $request, $step)
    {
        $statuses;
        if (isset($request->from_date) || ($request->from_date != ""))
            $statuses = $this->getAllStatusID();
        else
            $statuses = $this->getStatusID($request, $step);
        return $statuses;
    }


    public function filterModel(Request $request, $statuses)
    {

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                if (isset($request->from_date) && ($request->from_date != null)) {
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->whereBetween('updated_at', array($request->from_date, $request->to_date));
                    else
                        $query = $query->whereBetween('created_at', array($request->from_date, $request->to_date));
                }
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query = $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query = $query->where('source_id', $request->source_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->kpi)  && ($request->kpi != null))
                    $query = $query->where('kpi', $request->kpi);
                if (isset($request->status_id)  && ($request->status_id != null))
                    $query = $query->where('status_id', $request->status_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->search)) {
                    $query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('customers.name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.email',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.document', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.position', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.business', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.city',    "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.country', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_phone2', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_email', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_position', "like", "%" . $request->search . "%");
                        }
                    );
                }
            }


        )
            ->orderBy('status_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return $model;
    }

    public function filterModelFull(Request $request, $statuses)
    {

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                if (isset($request->from_date) && ($request->from_date != null)) {
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->whereBetween('updated_at', array($request->from_date, $request->to_date));
                    else
                        $query = $query->whereBetween('created_at', array($request->from_date, $request->to_date));
                }
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query = $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query = $query->where('source_id', $request->source_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->kpi)  && ($request->kpi != null))
                    $query = $query->where('kpi', $request->kpi);
                if (isset($request->search)) {
                    $query = $query->orwhere('customers.name', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.email',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.document', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.business', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.position', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.city',    "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.country', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                }
            }


        )
            ->orderBy('status_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $model;
    }

    public function countFilterCustomers($request, $statuses)
    {
        //$customersGroup = Customer::wherein('customers.status_id', $statuses)

        $customersGroup = Customer::wherein('customers.status_id', $statuses)
            ->rightJoin("customer_statuses", 'customers.status_id', '=', 'customer_statuses.id')
            ->where(
                // Búsqueda por...
                function ($query) use ($request) {
                    if (isset($request->from_date) && ($request->from_date != null)) {
                        if (isset($request->user_id)  && ($request->user_id != null))
                            $query = $query->whereBetween('customers.updated_at', array($request->from_date, $request->to_date));
                        else
                            $query = $query->whereBetween('customers.created_at', array($request->from_date, $request->to_date));
                    }
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->where('customers.user_id', $request->user_id);
                    if (isset($request->source_id)  && ($request->source_id != null))
                        $query = $query->where('customers.source_id', $request->source_id);
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('customers.status_id', $request->status_id);

                    if (isset($request->project_id)  && ($request->project_id != null))
                        $query = $query->where('project_id', $request->project_id);
                    if (isset($request->kpi)  && ($request->kpi != null))
                        $query = $query->where('kpi', $request->kpi);
                    $query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('customers.name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.email',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.document', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.position', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.business', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.city',    "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.country', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_phone2', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_email', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_position', "like", "%" . $request->search . "%");
                        }
                    );
                }
            )
            ->select(DB::raw('customers.status_id as status_id, count(customers.id) as count'))
            ->groupBy('status_id')
            ->groupBy('weight')

            ->orderBy('weight', 'ASC')

            ->get();

        foreach ($customersGroup as $item) {
            $included = false;
            foreach ($statuses as $status => $value) {
                if ($value == $item->status_id) {
                    $included = true;
                }
            }
            if ($included) {
                $item->color = CustomerStatus::getColor($item->status_id);
                $item->name = CustomerStatus::getName($item->status_id);
                $item->id = $item->status_id;
            }
        }
        return $customersGroup;
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $customers_statuses = CustomerStatus::all();
        $customer_sources = CustomerSource::all();
        $projects = Project::all();
        return view('customers.create', compact('customers_statuses', 'users', 'customer_sources', 'customersGroup', 'projects'));
    }



    public function storeFromRequest(Request $request)
    {
        $model = new Customer;
        $model->name = $request->name;
        $model->document = $request->document;
        $model->position = $request->position;
        $model->business = $request->business;
        $model->phone = $request->phone;
        $model->phone2 = $request->phone2;
        $model->email = $request->email;
        $model->notes = $request->notes;
        $model->kpi = $request->kpi;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->country = $request->country;
        $model->department = $request->department;
        $model->bought_products = $request->bought_products;
        $model->status_id = $request->status_id;
        $model->user_id = $request->user_id;
        $model->source_id = $request->source_id;
        $model->technical_visit = $request->technical_visit;
        $model->project_id = $request->project_id;

        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;




        if ($model->save()) {

            //$this->sendWelcomeMail($model);

            //$this->sendMail(1, $model);
            return redirect('customers/' . $model->id . '/show')->with('status', 'El Cliente <strong>' . $model->name . '</strong> fué añadido con éxito!');
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //    $count = $this->isEqual($request);
        $similar = $this->getSimilar($request);

        if ($similar->count() == 0)

            return $this->storeFromRequest($request);
        else
            return redirect('/customers/' . $similar[0]->id . '/show');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $model = Customer::find($id);
        $actions = Action::where('customer_id', '=', $id)->orderby("created_at", "DESC")->get();
        $action_options = ActionType::all();
        $histories = CustomerHistory::where('customer_id', '=', $id)->get();
        $email_options = Email::all();
        $statuses_options = CustomerStatus::orderBy("weight", "ASC")->get();
        $actual = true;
        $today = Carbon\Carbon::now();

        $pending_action = Action::find($request->pending_action_id);


        return view('customers.show', compact('model', 'histories', 'actions', 'action_options', 'email_options', 'statuses_options', 'actual', 'today', 'pending_action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Customer::find($id);
        $customer_statuses = CustomerStatus::orderBy("weight", "ASC")->get();
        $customer_sources = CustomerSource::all();
        $users = User::all();

        $projects = Project::all();



        return view('customers.edit', compact('model', 'customer_statuses', 'customersGroup', 'users', 'customer_sources', 'projects'));
    }


    public function assignMe($id)
    {
        $model = Customer::find($id);
        if (is_null($model->user_id) || $model->user_id == 0) {
            $user =  Auth::id();
            $model->user_id = $user;
            $model->save();
        }
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = Customer::find($id);


        $cHistory = new CustomerHistory;
        $cHistory->saveFromModel($model);

        $model->name = $request->name;
        $model->document = $request->document;
        $model->position = $request->position;
        $model->business = $request->business;
        $model->phone = $request->phone;
        $model->email = $request->email;
        $model->notes = $request->notes;
        $model->kpi = $request->kpi;
        $model->phone2 = $request->phone2;
        $model->department = $request->department;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->country = $request->country;
        $model->technical_visit = $request->technical_visit;
        $model->bought_products = $request->bought_products;
        $model->user_id = $request->user_id;
        $model->source_id = $request->source_id;
        $model->status_id = $request->status_id;
        $model->project_id = $request->project_id;


        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;

        if ($model->save()) {
            return redirect('leads')->with('statusone', 'El Cliente <strong>' . $model->name . '</strong> fué modificado con éxito!');
        }
    }

    // Color



    public function filterCustomers($request)
    {
        return Customer::where(
            function ($query) use ($request) {
                if (sizeof($request->status_id))
                    $query = $query->where('customers.status_id', "=", $request->status_id);
            }
        )
            ->select(DB::raw('customers.*'))

            ->orderBy('customers.status_id', 'asc', 'created_at', 'asc')
            ->paginate(20);
    }

    function getStatusID($request, $stage_id)
    {
        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach ($paramenters as $key => $value) {
            if (strpos($value, "status_id") !== false && (str_replace("status_id=", "", $value) != 0)) {
                $res[] = str_replace("status_id=", "", $value);
            }
        }
        if (!count($res)) {

            $model = CustomerStatus::where("stage_id", $stage_id)
                ->orderBy("weight", "ASC")
                ->get();
            //$model = CustomerStatus::all();

            foreach ($model as $item)
                $res[] = $item->id;
        }

        return $res;
    }
    // Enviar email
    public function sendMail($id, $user)
    {
        $model = Email::find($id);
        $subjet = 'Gracias por escribirnos';


        Email::raw($model->body, function ($message) use ($user, $subjet) {
            $message->from('nicolas@myseocompany.co', 'My SEO Company');

            $message->to($user->email, $user->user_name)->subject($subjet);
        });
    }

    public function mail($cui)
    {
        //$model = Email::find(1);
        $customer = Customer::find($cui);
        $subjet = 'Bro';

        //dd($customer);
        /*
    Mail::raw($model->body, function ($message) use ($customer, $subjet){
        $message->from('noresponder@mqe.com.co', 'Maquiempanadas');

        $message->to($customer->email, $customer->user_name)->subject($subjet);   
    });
*/

        $emailcontent = array(
            'subject' => 'Gracias por contactarme',
            'emailmessage' => 'Este es el contenido',
            'customer_id' => $cui
        );

        //dd($emailcontent);
        // Mail::send('emails.brochure', $emailcontent, function ($message) use ($customer){

        //         $message->subject('MQE');

        //         $message->to('nicolas@myseocompany.co');

        //     });


    }








    function getAllStatusID()
    {

        $res = array();
        $model = CustomerStatus::orderBy("weight", "ASC")->get();
        //$model = CustomerStatus::all();

        foreach ($model as $item)
            $res[] = $item->id;
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Customer::find($id);
        if ($model->delete()) {
            return redirect('customers')->with('statustwo', 'El Cliente <strong>' . $model->name . '</strong> fué eliminado con éxito!');
        }
    }
    /*

    public function saveAPICustomer($request, $model){

        
        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->notes       = $request->message;

        if (isset($request->poject_id)){
            $model->project_id  = $request->project_id;
        }
       if (isset($request->message)){
            $model->notes  = $request->message;
        }

        $model->rooms  = $request->rooms;
        
        $model->source_id  = 10;
        
        if( ($request->source_id != null)  && (intval($request->source_id )!=0))
               $model->source_id  = $request->source_id;
           else{
               if(isset($request->source_id) ) {
                   switch ($request->source_id) {
                       case 'ig':
                           $model->source_id = 5;
                           break;
                       case 'fb':
                           $model->source_id = 3;
                           break;
                           case 'ln':
                            $model->source_id = 4;
                            break;
                       default:
                           break;
                   }
               }
           }

        

        if( ($request->product != null)  && (intval($request->product )!=0)){
            $model->bought_products  = $request->product;
        
        }


        $model->cid = $request->cid;
        $model->src = $request->src;
        if(isset($request->time_to_buy))
            $model->notes .= "tiempo: " . $request->time_to_buy." | nuevo: ".$request->new_or_used. " | presupuesto familiar: ".$request->family_income." | presupuesto->casa: ".$request->budget;    
        
        $model->department = $request->department;
        $model->status_id = 1;

        $model->save();

        // guardo una acción
        $this->addAction($model);
        
        return $model;
    }
*/
    public function saveAPICustomer($request)
    {
        $model = new Customer;
        $model->name = $request->name;
        $model->phone = $request->phone;
        $model->phone2 = $request->phone2;
        $model->email = $request->email;
        $model->country = $request->country;
        $model->city = $request->city;
        $model->status_id = $request->status_id;

        $model->adset_name = $request->adset_name;
        $model->campaign_name = $request->campaign_name;
        $model->ad_name = $request->ad_name;

        /*
    $model->utm_content = $request->utm_content;
    $model->utm_term = $request->utm_term;
    */

        if (isset($request->product_id))
            $model->product_id = $request->product_id;
        if (isset($request->technical_visit))
            $model->technical_visit = $request->technical_visit;
        if (isset($request->name)) {
            $model->name = $request->name;
            $model->save();
        }



        if (isset($request->count_empanadas))
            $model->count_empanadas = $request->count_empanadas;
        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        $model->department = $request->department;
        if (isset($request->rd_id))
            $model->cid = $request->rd_id;
        if (isset($request->score))
            $model->score = $request->score;

        if (isset($request->status) && ($request->source == "Maquiempanadas - MQE_Form leads desmechadora")) {
            $model->status_id = 41;
        } else {
            $model->status_id = 1;
        }
        if (isset($request->notes))
            $model->notes .= " " . $request->notes;

        if (isset($request->session_id))
            $model->session_id = $request->session_id;

        $model->source_id = $this->getSource($request);

        /*  
    if (isset($request->utm_source)){
        $model->utm_source = $request->utm_source;
    }
    if (isset($request->utm_medium)){
        $model->utm_medium = $request->utm_medium;
    }
    if (isset($request->utm_campaign)){
        $model->utm_campaign = $request->utm_campaign;
    }
    if (isset($request->utm_content)){
        $model->utm_content = $request->utm_content;
    }
    if (isset($request->utm_term)){
        $model->utm_term = $request->utm_term;
    }

    */

        /*
    if($model->source_id == 26){
        if(isset($request->utm_source)){
            $model->utm_source = $request->utm_source;
        }
        if(isset($request->utm_medium)){
             $model->utm_medium = $request->utm_medium;
        }
        if(isset($request->utm_campaign)){
             $model->utm_campaign = $request->utm_campaign;
        }
        if(isset($request->utm_content)){
             $model->utm_content = $request->utm_content;
        }
        if(isset($request->utm_adset_name)){
             $model->utm_adset_name = $request->utm_adset_name;
        }
    }
    */




        $model->save();
        //$this->saveSession($model->id, $request->session_id);
        //$this->sendToRDStation($model);
        //$this->sendWelcomeMail($model);

        return $model;
    }
    public function saveAPICustomerJsonn($request, $model)
    {


        $model->request = $request->json()->all();

        //$model = new Customer;
        if (isset($request->name)) $model->name        = $request->name;
        if (isset($request->phone)) $model->phone       = $request->phone;
        if (isset($request->phone2)) $model->phone2      = $request->phone2;
        if (isset($request->email)) $model->email       = $request->email;
        if (isset($request->country)) $model->country     = $request->country;
        if (isset($request->city)) $model->city        = $request->city;

        if (isset($request->poject_id)) {
            $model->project_id  = $request->project_id;
        }
        if (isset($request->message)) {
            $model->notes  = $request->message;
        }


        if (isset($request->session)) {
            $model->session  = $request->session;
        }


        if (isset($request->ad_name)) {
            $model->ad_name  = $request->ad_name;
        }
        if (isset($request->adset_name)) {
            $model->adset_name  = $request->adset_name;
        }
        if (isset($request->campaign_name)) {
            $model->campaign_name  = $request->campaign_name;
        }
        if (isset($request->form_name)) {
            $model->form_name  = $request->form_name;
        }

        if (isset($request->source_id)) {
            if (isset($request->platform) && $request->platform != null) {
                switch ($request->platform) {
                    case 'fb':
                        $model->source_id = 3;
                        break;
                    case 'ig':
                        $model->source_id = 7;
                        break;
                    default:
                        $model->source_id = $request->source_id;
                        break;
                }
            } else {
                $model->source_id = $request->source_id;
            }
        }

        $model->status_id = 1;
        $model->save();

        // guardo una acción
        $this->addAction($model);

        return $model;
    }

    public function addAction($model)
    {
        $action = new Action;
        $action->note = ":Solicitó datos desde ";
        if (isset($model->source))
            $action->note .= $model->source->name;
        $action->note .= " el " . $model->created_at;

        $action->type_id = $this->customerSourceToActionType($model->source_id); // visita web
        //$action->note .= "fuente ".$model->source_id." accion " . $action->type_id;
        $action->customer_id = $model->id;
        $action->save();
    }

    public function customerSourceToActionType($source_id)
    {
        $action_id = 0;
        switch ($source_id) {
            case 10: //  Web
                $action_id = 3;
                break;
            case 12: // Whatsapp
                $action_id = 8;
                break;
            case 15: // Calculadora Web
                $action_id = 3;
                break;
            case 26: // Whatsapp
                $action_id = 8;
                break;
            case 33: // redes sociales
                $action_id = 6;
                break;
            case 6: // fb lead
                $action_id = 6;
                break;
            case 39: // fb Messenger
                $action_id = 6;
                break;
            case 40: // ínstagram
                $action_id = 7;
                break;
            case 41: // chat bot
                $action_id = 6;
                break;
        }
        return $action_id;
    }
    public function isEqual($request)
    {
        //dd($request);
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query = $query->where('user_id', $request->user_id);

                if (isset($request->source_id)  && ($request->source_id != null))
                    $query = $query->where('source_id', $request->source_id);

                if (isset($request->status_id)  && ($request->status_id != null))
                    $query = $query->where('status_id', $request->status_id);

                if (isset($request->business)  && ($request->business != null))
                    $query = $query->where('business', $request->business);

                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->where('phone', $request->phone);

                if (isset($request->email)  && ($request->email != null))
                    $query = $query->whereRaw('lower(email) = lower("' . $request->email . '")');

                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->where('phone2', $request->phone2);

                if (isset($request->notes)  && ($request->notes != null))
                    $query = $query->where('notes', $request->notes);

                if (isset($request->city)  && ($request->city != null))
                    $query = $query->where('city', $request->city);

                if (isset($request->country)  && ($request->country != null))
                    $query = $query->where('country', $request->country);
            }
        )
            ->count();
        return $model;
    }


    public function getEmailByProjectId($project_id)
    {

        $project_id_laquinta = '1';
        $project_id_torres = '2';


        $email_id_laquinta = 2;
        $email_id_torres = 4;

        $email_id = 0;


        switch ($project_id) {
            case $project_id_laquinta:
                $email_id = $email_id_laquinta;
                break;
            case $project_id_torres:
                $email_id = $email_id_torres;
                break;
        }

        return $email_id;
    }

    public function sendWelcomeMail($customer)
    {
        $email_id = $this->getEmailByProjectId($customer->project_id);


        if ($email_id != 0) {
            $email = Email::find($email_id);
            // $email, $user, $count, $sended_at
            Email::addEmailQueue($email, $customer, 0, Carbon\Carbon::now());
            $this->storeEmailAction($email, $customer, "Correo automático de bienvenida");
        }
    }

    public function redirectingPage1()
    {
        return redirect('https://myseocompany.co');
    }

    public function redirectingPage2()
    {
        return redirect('https://myseocompany.co');
    }

    public function redirectingPage3($text)
    {
        return redirect('https://myseocompany.co');
    }

    public function redirectingPage4()
    {
        return redirect('https://myseocompany.co');
    }

    public function sendToWP($phone)
    {
        return redirect('https://wa.me/57' . $phone);
    }

    public function toLocal57($phone)
    {
        return substr($phone, 2, 10);
    }

    public function saveAPI(Request $request)
    {

        $this->saveLogFromRequest($request);


        // vericamos que no se inserte 2 veces
        $count = $this->isEqual($request);
        $similar = $this->getSimilar($request);


        if (is_null($count) || ($count == 0)) {
            // verificamos uno similar


            if ($similar->count() == 0) {
                $model = $this->saveAPICustomer($request);
                $this->storeActionAPI($request, $model->id);
            } else {

                $model = $similar[0];
                
                $this->storeActionAPI($request, $model->id);
                $this->updateCreateDate($request, $model->id);
            }
        } else {
            $model = $similar[0];
            if ($model && $model->status_id == 1) { //miro si está nuevo


                $cHistory = new CustomerHistory;
                $cHistory->saveFromModel($model);
                //$model->status_id  = 1; // se cambia a nuevo 1
                $model->save();
            }

            if (($model->name == null) && (isset($request->name))) {
                $model->name = $request->name;
                $model->save();
            }




            $this->storeActionAPI($request, $similar[0]->id);


            if (isset($request->session_id)) {

                $model = Customer::where("email", $request->email)->first();
                $this->saveSession($model->id, $request->session_id);
            }
        }
    }
    public function getURl($url)
    {
        // Crear un nuevo recurso cURL
        $ch = curl_init();

        // Configurar URL y otras opciones apropiadas
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Capturar la URL y pasarla al navegador
        curl_exec($ch);

        // Cerrar el recurso cURL y liberar recursos del sistema
        curl_close($ch);
    }

    public function saveCustomerCalculate(Request $request)
    {

        //Customer::create($request->all());
        $count = $this->isEqual($request);

        if (is_null($count) || $count == 0) {
            $similar = $this->getSimilar($request);


            if ($similar->count() == 0) {

                $model = $this->saveAPICustomer($request);




                //$this->sendWelcomeMail($model);

                /* Validar si el envio del formulario viene desde la calculadora */
                if ($request->source_id == 15) {
                    $this->getURL("https://myseocompany.co");
                    return response()->json(['yes' => 'Validado correctamente']);;
                } else {

                    if ($request->project_id == '1') {
                        return $this->redirectingPage1();
                    } else {
                        return $this->redirectingPage2();
                    }
                }
            }
            // este cliente ya existe. Se agrega una nueva nota
            else {

                $model = $similar[0];


                $this->storeActionAPI($request, $model->id);
                $this->updateAPICustomer($request, $model->id);

                /* Validar si el envio del formulario viene desde la calculadora */
                if ($request->source_id == 15) {
                    /* curl init -> para peticion http */
                    /*
                    $cliente = curl_init();
                    curl_setopt($cliente, CURLOPT_URL, "gracias-calculadora.html");
                    curl_setopt($cliente, CURLOPT_HEADER, 0);
                    curl_exec($cliente);
                    curl_close($cliente);*/
                    return response()->json(['yes' => 'Validado correctamente']);;
                } else {
                    if ($request->project_id == '1') {
                        return $this->redirectingPage1();
                    } else {
                        return $this->redirectingPage2();
                    }
                }
            }
        } else {


            $similar = $this->getSimilar($request);


            $model = $similar[0];


            $this->storeActionAPI($request, $model->id);
            $this->updateAPICustomer($request, $model->id);

            if ($request->source_id == 15) {
                /* curl init -> para peticion http */
                /*
                $cliente = curl_init();
                curl_setopt($cliente, CURLOPT_URL, "gracias-calculadora.html");
                curl_setopt($cliente, CURLOPT_HEADER, 0);
                curl_exec($cliente);
                curl_close($cliente);*/
                return response()->json(['yes' => 'Validado correctamente']);;
            } else {
                if ($request->project_id == '1') {
                    return $this->redirectingPage1();
                } else {
                    return $this->redirectingPage2();
                }
            }
        }

        if ($request->source_id == 15) {
            /* curl init -> para peticion http */
            /*
            $cliente = curl_init();
            curl_setopt($cliente, CURLOPT_URL, "gracias-calculadora.html");
            curl_setopt($cliente, CURLOPT_HEADER, 0);
            curl_exec($cliente);
            curl_close($cliente);*/
            return response()->json(['yes' => 'Validado correctamente']);;
        } else {
            if ($request->project_id == '1') {
                return $this->redirectingPage1();
            } else {
                return $this->redirectingPage2();
            }
        }
    }

    public function storeActionAPI(Request $request, $customer_id)
    {

        $model = new Action;

        $str = "";
        if (isset($request->phone))
            $str .= " telefono1:" . $request->phone;
        if (isset($request->phone2))
            $str .= " telefono2:" . $request->phone2;
        if (isset($request->email))
            $str .= " email:" . $request->email;
        if (isset($request->city))
            $str .= " ciudad:" . $request->city;
        if (isset($request->country))
            $str .= " pais:" . $request->country;

        if (isset($request->name))
            $str .= " Nombre:" . $request->name;
        if (isset($request->notes))
            $str .= " Nota:" . $request->notes;



        if (isset($request->source_id))
            $str .= " Fuente:" . $request->source_id;

        //$model->note = $request->notes . $str;
        $model->type_id = 16; // actualización


        $source_id = $this->getCustomerSource($request);
        $model->type_id = $this->customerSourceToActionType($source_id); // actualización

        if (isset($request->content)) {
            $model->type_id = 39; // WhatsApp de entrada
            $str .= " Mensaje:" . $request->content;
        }

        $model->note .= $str;

        //dd($request);

        $model->customer_id = $customer_id;

        $model->save();

        $this->createPendingAction($request, $customer_id);

        return back();
    }


    public function createPendingAction(Request $request, $customer_id)
    {

        $due_date = Carbon\Carbon::now();

        // Verificar si ya existe una acción con due_date y delivery_date no vacíos
        $existingAction = Action::where('customer_id', $customer_id)
            ->whereNotNull('due_date')
            ->whereNull('delivery_date')
            ->first();

        // Si no existe una acción existente, crea una nueva
        if (!$existingAction) {
            $model = new Action;

            $model->type_id = 43; // WP de salida
            $model->creator_user_id = 0; // WP de salida

            $model->customer_id = $customer_id;
            $model->note = "Responder por WhatsApp";

            $model->due_date = $due_date;
            //dd($model);
            $model->save();
        }
    }

    public function getCustomerSource(Request $request)
    {
        $source_id = 10;
        if (($request->source_id != null)  && (intval($request->source_id) != 0))
            $source_id  = $request->source_id;
        else {
            if (isset($request->source_id)) {
                switch ($request->source_id) {
                    case 'ig':
                        $source_id = 40;
                        break;
                    case 'fb':
                        $source_id = 6;
                        break;
                    default:
                        break;
                }
            }
        }
        return $source_id;
    }

    public function updateAPICustomer(Request $request, $customer_id)
    {

        $action = new Action;

        /*
        $action->note = "se actualizaron los datos del cliente ";
        $action->type_id = 16; // actualización
        $action->customer_id = $customer_id;
        $action->save();
        */

        $model = Customer::find($customer_id);

        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->project_id  = $request->project_id;
        $model->rooms  = $request->rooms;


        $model->source_id  = $this->getCustomerSource($request);



        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        $model->department = $request->department;
        $model->notes .= " tiempo: " . $request->time_to_buy . " | nuevo: " . $request->new_or_used . " | presupuesto familiar: " . $request->family_income . " | presupuesto casa: " . $request->budget;
        $model->status_id = 1;

        $model->save();

        // guardo una acción
        $this->addAction($model);
    }

    public function storeEmailAction($mail, $customer, $note)
    {
        $today = Carbon\Carbon::now();
        // envio mail
        $action_type_id = 2;

        $model = new Action;

        $model->note = $note;
        $model->type_id = $action_type_id;
        $model->creator_user_id = Auth::id();
        $model->customer_id = $model->customer_id;


        $model->delivery_date = $today;
        $model->save();
    }

    public function changeCustomerStatus($request, $customer)
    {
        if (!is_null($request->status_id)) {
            $cHistory = new CustomerHistory;
            $cHistory->saveFromModel($customer);
            $customer->status_id = $request->status_id;
            $customer->save();
        }
    }

    public function createNewAction($request)
    {
        $due_date = Carbon\Carbon::parse($request->due_date);

        $model = new Action;

        $model->type_id = $request->type_id;
        $model->creator_user_id = Auth::id();
        $model->customer_id = $request->customer_id;
        $model->note = $request->note;

        if (isset($request->due_date) && ($request->due_date != "")) {
            $model->due_date = $due_date;
        }
        $model->save();
    }

    public function updateAction($request)
    {
        $today = Carbon\Carbon::now();

        $model = Action::find($request->pending_action_id);

        $model->type_id = $request->type_id;
        $model->creator_user_id = Auth::id();
        //$model->customer_id = $request->customer_id;
        $model->note = $model->note . " / " . $request->note;
        $model->delivery_date = $today;
        $model->save();
    }
    public function storeAction(Request $request)
    {


        //dd($today);
        $customer = Customer::find($request->customer_id);
        if (is_null($request->type_id)) {
            return back()->with('statustwo', 'El Cliente <strong>' . $customer->name . '</strong> no fue modificado!');
        }
        if (!isset($request->pending_action_id))
            $this->createNewAction($request);
        else
            $this->updateAction($request);

        $this->changeCustomerStatus($request, $customer);




        return redirect('/customers/' . $request->customer_id . '/show')->with('statusone', 'El Cliente <strong>' . $customer->name . '</strong> fué modificado con éxito!');
    }

    public function storeMail(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $email = Email::find($request->email_id);

        $count = Email::sendUserEmail($request->customer_id, $email->subject, $email->view, $email->id);
        if ($count > 0) {
            Action::saveActionManually($request->customer_id, $request->email_id, 5);
        } else {
            Action::saveActionManually($request->customer_id, $request->email_id, 2);
        }

        //Email::addEmailQueue($email, $customer, 0, Carbon\Carbon::now());
        /*
        $emailcontent = array (
            'subject' => $email->subject,
            'emailmessage' => 'Este es el contenido',
            'customer_id' => $model->id,
            'email_id' => $email->id,
            'model' => $model,
             );
        
        Mail::send($email->view, $emailcontent, function ($message) use ($model, $email){
                    $message->subject($email->subject);
                    $message->to($model->email);
            });
        */
        //Action::saveAction($customer->id,$email->id, 2);
        return back();
    }



    public function excel(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 2);

        $model = $this->filterModelFull($request, $statuses);
        $customersGroup = $this->countFilterCustomers($request, $statuses);

        $sources = CustomerSource::all();

        return view('customers.excel', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources'));
    }

    /*
    public function updateAPICustomer2(Request $request, $customer_id)
    {

        $customer = Customer::find($customer_id);
        $model = new Action;


        $model->note = "se actualizaron los datos del cliente " . $customer->created_at;
        $model->type_id = 16; // actualización
        $model->customer_id = $customer_id;
        $model->save();


        $mytime = Carbon\Carbon::now();
        //$customer->created_at = $mytime->toDateTimeString();
        $customer->status_id = 19;
        if($customer->name=="")
            $customer->name = $request->name;
        if($customer->phone=="")
            $customer->phone = $request->phone;
        if($customer->email=="")
            $customer->email = $request->email;
        
        $customer->project_id = $request->project_id;
        $customer->source_id = $request->source_id;

        
        $customer->save();

        $this->addAction($customer);


        return back();
    }
    */


    public function trackWPAction($cid,  $aid, $tid, $msg,  Request $request)
    {
        $msg = urldecode($msg);
        $model = Customer::find($cid);

        if ($model) {

            $model->status_id  = 2;

            $cHistory = new CustomerHistory;
            $cHistory->saveFromModel($model);

            $model->save();

            $cAudience = AudienceCustomer::where('audience_id', $aid)
                ->where('customer_id', $cid)
                ->first();


            $cAudience->sended_at = Carbon\Carbon::now();
            if ($cAudience->save()) {
                echo "guardado";
            }
            $this->saveAction($cid, null, $tid, $msg);
        }
    }

    public function trackAction($cid,  $sid, $tid, $pid, Request $request)
    {
        //dd($request->header('User-Agent');
        if (strpos($request->header('User-Agent'), "WhatsApp") === false) {
            $model = Customer::find($cid);
            if ($model) {
                //$model->notes .= $request;
                if ($sid == 9)
                    $model->status_id  = $sid;
                if ($sid == 1) {
                    $model->status_id  = 2;
                    $model->scoring = 1;
                }

                $cHistory = new CustomerHistory;
                $cHistory->saveFromModel($model);

                $model->save();

                $cAudience = CustomerAudience::where('customer_id', $cid)->first();
                $cAudience->sended_at = Carbon\Carbon::now();
                if ($cAudience->save()) {
                    echo "guardado";
                }



                if ($sid == 9) { // descartado
                    $this->saveAction($cid, null, $tid, "No le interesa recibir más información" . $request->header('User-Agent') . " NO -" . strpos($request->header('User-Agent'), "WhatsApp"));

                    return redirect('https://myseocompany.co');
                }
                if (($sid == 1)) {
                    $this->saveAction($cid, null, $tid, "Le interesa recibir más información" . $request->header('User-Agent'));

                    if ($pid == 1) // la quinta
                        return redirect('https://www.youtube.com/watch?v=rr0pEeyLS58');
                    if ($pid == 2) // la quinta
                        return redirect('https://www.youtube.com/watch?v=QkQSQ_tTebc');
                }
            } else {
                return redirect('https://myseocompany.co');
            }
        }
    }


    public  function saveAction($cid, $oid, $tid, $str)
    {
        $model = new Action;
        $model->customer_id = $cid;
        $model->object_id = $oid;
        $model->type_id = $tid;
        $model->note = $str;
        $model->creator_user_id = Auth::id();
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');
        $model->delivery_date = $date;

        $model->save();
    }

    public function getSession(Request $request)
    {
        $data = $request->json()->all();
        $name = $data["queryResult"]["outputContexts"][0]['name'];


        $start = strpos($name, "sessions/") + 9;
        $end = strpos($name, "/contexts");
        $str = substr($name, $start, ($end - $start));


        return $str;
    }

    public function saveSession($customer_id, $session_id)
    {
        $model = Session::where('session_id', $session_id)->first();

        if (!$model) {
            $model = new Session;
        }
        $model->session_id = $session_id;
        $model->customer_id = $customer_id;
        $model->save();
        return $model;
    }


    public function storeAPICustomer($request, $model)
    {

        $model->request = json_encode($request->all());
        $model->name = $request->name;
        $model->phone = $request->phone;
        $model->phone2 = $request->phone2;
        $model->email = $request->email;
        $model->country = $request->country;
        $model->city = $request->city;
        $model->address = $request->address;

        $model->status_id = 1;

        if (isset($request->status_id))
            $model->status_id = $request->status_id;

        if (isset($request->product_id))
            $model->product_id = $request->product_id;
        if (isset($request->technical_visit))
            $model->technical_visit = $request->technical_visit;

        if (isset($request->count_empanadas))
            $model->count_empanadas = $request->count_empanadas;
        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        $model->department = $request->department;
        if (isset($request->rd_id))
            $model->cid = $request->rd_id;
        if (isset($request->score))
            $model->score = $request->score;



        $model->source_id = $this->getSource($request);



        $model->notes .= $request->notes;

        if (isset($request->session_id))
            $model->session_id = $request->session_id;

        $model->save();
        $this->saveSession($model->id, $request->session_id);


        return $model;
    }
    public function getSource($request)
    {
        $source_id = 10;
        if (isset($request->source_id))
            $source_id = $request->source_id;
        else
            $source_id = 10;

        if (isset($request->campaign) && ($request->campaign == 'Facebook'))
            $source_id = 17;
        elseif (isset($request->campaign) && ($request->campaign == 'NewJersey'))
            $source_id = 19;
        elseif (isset($request->campaign) && ($request->campaign == 'USA'))
            $source_id = 16;
        elseif (isset($request->campaign) && ($request->campaign == '500'))
            $source_id = 15;
        elseif (isset($request->campaign) && ($request->campaign == 'Facebook New Jersey'))
            $source_id = 22;
        elseif (isset($request->campaign) && ($request->campaign == 'Leads Black Friday 2018'))
            $source_id = 24;
        elseif (isset($request->campaign) && ($request->campaign == 'Landing Desmechadora'))
            $source_id = 28;
        elseif (isset($request->campaign) && ($request->campaign == 'Landing Bogota'))
            $source_id = 30;
        elseif (isset($request->campaign) && ($request->campaign == 'Landing Promo Navideña'))
            $source_id = 32;
        elseif (isset($request->platform) && ($request->platform == 'fb'))
            $source_id = 17;
        elseif (isset($request->platform) && ($request->platform == 'ig'))
            $source_id = 31;
        return $source_id;
    }

    public function saveOrder(Request $request)
    {
        $data = $request->json()->all();

        $params = $data["queryResult"]['parameters'];
        $action =  $data["queryResult"]['action'];

        $fullfillment = "";

        $session_id = $this->getSession($request);
        $session = Session::where('session_id', $session_id)->first();
        //dd($session);
        // verifico que hay un cliente
        if (!isset($params["moreProducts"])) {

            if ($session) {
                $customer = Customer::find($session->customer_id);

                if ($customer) {
                    // verifico que no exista la orden
                    //$model = Order::where('session_id', $session_id)->first();
                    //if(!$model)
                    $model = new Order;

                    $model->customer_id = $customer->id;

                    if (isset($params["phone"]))
                        $model->phone = $params["phone"];

                    if (isset($params["delivery_date"])) {
                        $date = $params["delivery_date"];
                        if (is_array($date))
                            $date = Carbon\Carbon::parse($date['date_time']);
                        else
                            $date = Carbon\Carbon::parse($params["delivery_date"]);
                        $model->delivery_date = $date->format('Y-m-d H:m:s');
                    }
                    if (isset($customer->name))
                        $model->delivery_name       = $customer->name;

                    if (isset($customer->address))
                        $model->delivery_address       = $customer->address;

                    if (isset($customer->phone))
                        $model->delivery_phone       = $customer->phone;
                    $model->session_id = $session_id;
                    if (isset($params["product"]))
                        $model->notes = $params["product"];

                    $model->save();

                    $this->saveOrderProduct($model, $params);
                }
                $fullfillment = $this->cierre;
            }
        } else {
            //dd($params["entro"]);
            $model = Order::where('session_id', $session_id)->first();
            $this->saveOrderProduct($model, $params);
            $fullfillment = $this->cierre;
        }

        return response()->json(array(
            "fulfillmentText" => $fullfillment,
        ));
    }

    public function saveOrderProduct($order, $params) {}

    public function saveAPICutomerPabbly(Request $request)
    {

        $json = $request->json()->all();
        if (isset($json["leads"]))
            $data = $json["leads"][0];

        $tags = "";


        $model = new Customer;

        $status_id = 7;

        //dd($request);
        $model->request = $request;


        if (isset($request->name)) {
            $model->name = $request->name;
        }

        if (isset($request->notes)) {
            $model->notes .= $request->notes;
        }
        if (isset($request->phone)) {
            $model->phone = $request->phone;
        }

        if (isset($request->source_id)) {
            $model->source_id = $request->source_id;
        }



        $this->saveAPIPabbly($model, "");
    }
    public function saveAPIPabbly($request_model, $opportunity)
    {



        //   igual   |   similar     
        //     No          No      crea    
        //     No          Si      actualiza
        //     Si                  actualiza                       

        // vericamos que no se inserte 2 veces
        $equal = $this->isEqualModel($request_model);

        //dd($status);
        if (!$equal) {
            //dd($status); 
            // verificamos uno similar
            $similar = $this->getSimilarModel($request_model);

            if ($similar) {
                // creo un nuevo registro
                $model = $similar;


                $model->name = $request_model->name;

                $model->notes = $request_model->notes;
                $model->status_id = 7;

                $model->save();
                $this->updateCustomerHistory($opportunity, $model, $request_model);
            } else {
                $request_model->status_id = 7;
                $request_model->save();

                $model = $request_model;
            }
        } else {
            $model = $equal;
            $model->name = $request_model->name;
            $model->status_id = 7;
            $model->save();
        }
        $this->storeActionAPIPabbly($request_model, $model->id);
        return $model;
    }
    public function storeActionAPIPabbly($request, $customer_id)
    {

        $model = new Action;

        $model->note = $request->notes;
        //$model->note .= json_encode($request);
        $model->type_id = 39; // actualización 



        $model->customer_id = $customer_id;

        $model->save();

        return back();
    }

    public function isEqualModel($request)
    {
        //dd($request);
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->where('phone', $request->phone);
                /*    
                        if(isset($request->user_id)  && ($request->user_id!=null))
                            $query = $query->where('user_id', $request->user_id);
     
                        if(isset($request->source_id)  && ($request->source_id!=null))
                            $query = $query->where('source_id', $request->source_id);
    
                        if(isset($request->status_id)  && ($request->status_id!=null))
                            $query = $query->where('status_id', $request->status_id);
                        
                        if(isset($request->business)  && ($request->business!=null))
                            $query = $query->where('business', $request->business);
    
                        
    
                        if(isset($request->email)  && ($request->email!=null))
                            $query = $query->whereRaw('lower(email) = lower("'.$request->email.'")');
    
                        if(isset($request->phone2)  && ($request->phone2!=null))
                            $query = $query->where('phone2', $request->phone2);
    
                        if(isset($request->notes)  && ($request->notes!=null))
                            $query = $query->where('notes', $request->notes);
    
                        if(isset($request->city)  && ($request->city!=null))
                            $query = $query->where('city', $request->city);
    
                        if(isset($request->country)  && ($request->country!=null))
                            $query = $query->where('country', $request->country);
    
                        if(isset($request->fit_score)  && ($request->fit_score!=null))
                            $query = $query->where('scoring_profile', $request->fit_score);
    
                        if(isset($request->interest)  && ($request->interest!=null))
                            $query = $query->where('scoring_interest', $request->interest);
    
                        */
            }
        )
            ->first();
        return $model;
    }


    public function getSimilarModel($request)
    {
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {
                /*
                if(isset($request->phone)  && ($request->phone!=null) && ($request->phone!='NA'))
                    $query->orwhere('phone', $request->phone);
                
                if(isset($request->phone)  && ($request->phone!=null) && ($request->phone!='NA'))
                    $query->orwhere('phone2', $request->phone);
    
                if(isset($request->phone2)  && ($request->phone2!=null) && ($request->phone!='NA'))
                    $query->orwhere('phone', $request->phone2);
    
                if(isset($request->phone2)  && ($request->phone2!=null) && ($request->phone!='NA'))
                    $query->orwhere('phone2', $request->phone2);
    
    */
                $normalizedSearch = "";

                if ($request->phone && $request->phone != 'NA') {
                    $query->orWhere('phone', $request->phone)
                        ->orWhere('phone2', $request->phone);
                    $normalizedSearch = $this->normalizePhoneNumber($request->phone);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.contact_phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                }

                if ($request->phone2 && $request->phone2 != 'NA') {
                    $query->orWhere('phone', $request->phone2)
                        ->orWhere('phone2', $request->phone2);
                    $normalizedSearch = $this->normalizePhoneNumber($request->phone);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                    $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.contact_phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
                }

                if (isset($request->email)  && ($request->email != null))
                    $query->orWhere('email', strtolower($request->email));
            }
        )->first();
        //dd($model);
        return $model;
    }

    public function updateCustomerHistory($opportunity, $model, $rd_model)
    {
        // actuliza el existente
        if (($opportunity == "false") && ($model->status_id == 18 || $model->status_id == 36)) {
            //nuevo - no contesta - calificado
            $model->status_id = 36;
        } elseif (($opportunity == "true") && ($model->status_id == 1 || $model->status_id == 18 || $model->status_id == 36 || $model->status_id == 19)) {
            //nuevo - no contesta - calificado - oportunidad
            $model->status_id = 19;
        }
        if ($model->source_id == 23 || $model->source_id == 37) {
            $model->rd_public_url = $rd_model->rd_public_url;
            $model->scoring_profile = $rd_model->scoring_profile;
            $model->scoring_interest = $rd_model->scoring_interest;
        }
        $this->updateAPICustomerRD($model);
    }


    public function updateAPICustomerRD($model)
    {

        $model->save();

        $cHistory = new CustomerHistory;
        $cHistory->saveFromModel($model);
    }


    function normalizePhoneNumber($phoneNumber)
    {
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber);
        return substr($digits, -10);
    }



    function looksLikePhoneNumber($input)
    {
        // Esta expresión regular es más flexible y debería coincidir con una variedad
        // más amplia de formatos de números de teléfono.
        return preg_match('/^\+?\d{1,4}(\s|-)?(\d{1,4}(\s|-)?){1,4}$/', $input);
    }

    public function getSimilar($request)
    {
        $query = Customer::query();
        $normalizedSearch = "";

        if ($request->phone && $request->phone != 'NA') {
            $query->orWhere('phone', $request->phone)
                ->orWhere('phone2', $request->phone);
            $normalizedSearch = $this->normalizePhoneNumber($request->phone);


            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.contact_phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
        }

        if ($request->phone2 && $request->phone2 != 'NA') {
            $query->orWhere('phone', $request->phone2)
                ->orWhere('phone2', $request->phone2);
            $normalizedSearch = $this->normalizePhoneNumber($request->phone);
            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
            $query->orwhereRaw("REPLACE(REPLACE(REPLACE(customers.contact_phone2, ' ', ''), '-', ''), '(', '') LIKE ?", ["%$normalizedSearch%"]);
        }

        if ($request->email) {
            $query->orWhere('email', strtolower($request->email));
        }

        return $query->get();
    }

    public function saveLogFromRequest(Request $request)
    {
        $model = new Log();

        // Verificar si la solicitud es JSON
        if ($request->isJson()) {
            $requestData = $request->json()->all();
        } else {
            // Si no es JSON, obtener todos los datos de la solicitud
            $requestData = $request->all();
        }

        // Guardar la solicitud como JSON
        $model->request = json_encode($requestData);
        $model->save();
    }
    public function updateCreateDate(Request $request, $customer_id)
    {

        $customer = Customer::find($customer_id);


        $model = new Action;
        $model->note .= "se actualizó la fecha de creación " . $customer->created_at;
        $model->type_id = 16; // actualización
        $model->customer_id = $customer_id;
        $model->save();


        $mytime = Carbon\Carbon::now();
        $customer->created_at = $mytime->toDateTimeString();
        if (isset($request->status_id))
            $customer->status_id = $request->status_id;

        //$customer->status_id = 63; // pendiente

        // ajusto la nota
        if (isset($request->notes)) {
            if (is_null($customer->notes))
                $customer->notes = "Actualizó " . $request->notes;
            else
                $customer->notes .= "Actualizó " . $request->notes;
        }
        $customer->save();


        return back();
    }
}
