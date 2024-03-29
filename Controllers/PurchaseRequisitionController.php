<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use App\Mail\purchase_requisition_approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\purchase_requisition;
use App\Projectphase;
use App\Project;
use App\Projecttask;
use App\User;
use App\Employee_records;
use App\materialmaster;
use App\Currency;
use App\country;
use App\purchase_item;
use Illuminate\Support\Facades\DB;
use App\gl;
use App\purchaseorder_item;
use App\GlAccount;

class PurchaseRequisitionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        Mail::to('tester9065@gmail.com')->send( new purchase_requisition_approval());
//        Mail::raw('Text', function ($message){
//            $message->to('tester9065@gmail.com');
//        });

        $purchase_requisition_data = purchase_requisition::where('company_id', Auth::user()->company_id)->get();;


        $createdon = array();
        $createdby = array();
        $changedby = array();
        $country = array();


        foreach ($purchase_requisition_data as $key => $value) {



            $purchase_item[$key] = purchase_item::where('requisition_number', $value->requisition_number)
                                ->where('company_id', Auth::user()->company_id)->first();



            $created_date[$key] = isset($purchase_item[$key]->created_on) ? $purchase_item[$key]->created_on : '';
            if (empty($created_date[$key])) {
                $createdon = null;
            } else {
                $createdon = date("Y-m-d", strtotime($created_date[$key]));
            }



            $purchase_item[$key] = (count(purchase_item::where('requisition_number', $value->requisition_number)->first()) > 0) ? purchase_item::where('requisition_number', $value->requisition_number)->first() : array();
            if (count($purchase_item[$key]) > 0) {
                $createdby[$key] = ($purchase_item[$key]->created_by != '') ? User::where('id', $purchase_item[$key]->created_by)->first()['original']['name'] : '';
                $changedby[$key] = ($purchase_item[$key]->changed_by != '') ? User::where('id', $purchase_item[$key]->changed_by)->first()['original']['name'] : '';
                $country[$key] = ($purchase_item[$key]->country != '') ? country::where('id', $purchase_item[$key]->country)->first()['original']['country_name'] : '';
            } else {
                $createdby[$key] = '';
                $changedby[$key] = '';
                $country[$key] = '';
            }

            if (isset($value->approver_1)) {
                $approver[$key][0] = Employee_records::where('employee_id', $value->approver_1)->first()['original']['employee_first_name'];
            }
            if (isset($value->approver_2)) {
                $approver[$key][1] = Employee_records::where('employee_id', $value->approver_2)->first()['original']['employee_first_name'];
            }
            if (isset($value->approver_3)) {
                $approver[$key][2] = Employee_records::where('employee_id', $value->approver_3)->first()['original']['employee_first_name'];
            }
            if (isset($value->approver_4)) {
                $approver[$key][3] = Employee_records::where('employee_id', $value->approver_4)->first()['original']['employee_first_name'];
            }
        }
        return view('admin.purchase_requisition.index', compact('createdon', 'purchase_item', 'approver', 'country', 'purchase_requisition_data', 'createdby', 'changedby'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = array();
        $country_data = country::all();
        foreach ($country_data as $value) {
            $country[$value->id] = $value->country_name;
        }
        // get the requestor id 
        $user = Auth::user();
        $username = 'you are not logged in';
        if (Auth::check()) {
            $username = $user->name;
        }
        $requestedby = array();
        $temp = Employee_records::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();

        foreach ($temp as $value) {

            $requestedby[$value->employee_id] = $value->employee_id . ' ( ' . (isset($value->employee_first_name) ? $value->employee_first_name : '') . ' )';
        }

        $requestor = User::where('name', $username)->first();

        $requestor = $requestor['id'];

        $requestor_data = User::all();
        foreach ($requestor_data as $value) {
            $requestors[$value->id] = $value->name;
        }

        $changed_by = $requestor;
        $created_by = $requestor;
        // get record of the id trying to edit
//        $purchase_requisition = purchase_requisition::find($id);
        //get material records
        $material = array();
        $temp = materialmaster::all();
        foreach ($temp as $value) {
            $material[$value['material_number']] = $value['material_name'];
        }

        $phase_ids = array();
        $project_ids = array();
        $task_ids = array();

        //make associative arrays
        $phase_data = Projectphase::all();
        foreach ($phase_data as $value) {
            $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
        }
        $phase_ids[0] = " _blank_ ";


        $project_data = Project::where('company_id', Auth::user()->company_id)->get();
        foreach ($project_data as $value) {
            $project_ids[$value['id']] = $value['project_Id'] . ' (' . $value['project_name'] . ') ';
        }
        $project_ids[0] = " _blank_ ";

        $tasks = Projecttask::where('company_id', Auth::user()->company_id)->get();
        foreach ($tasks as $value) {
            $task_ids[$value['id']] = $value['task_Id'] . ' (' . $value['task_name'] . ') ';
        }
        $task_ids[0] = " _blank_ ";

        //get currency from currency table
        $currency = array();
        $temp = null;
        $temp = Currency::all();
        foreach ($temp as $value) {
            $currency[$value['id']] = $value['short_code'];
        }

        //value are static as module not 
        $purchase_order_data = \App\purchase_order::where('company_id', Auth::user()->company_id)->get();
        $purchase_order_number = array();
        foreach ($purchase_order_data as $value) {

            $purchase_order_number[$value->purchase_order_number] = $value->purchase_order_number;
        }

        $vendor = array();
        $vendor_data = \App\vendor::all();
        foreach ($vendor_data as $value) {
            $vendor[$value->id] = $value->name;
        }
        //check for status
        $status = '';

        ## demo purposes only once modules are ready use actual data
        $gl = GlAccount::where('company_id', Auth::user()->company_id)->get();
        $g_l_account = array();
        foreach ($gl as $value) {
            $g_l_account[$value->number] = $value->number;
        }

        $cost_center = array();
        $cost_center_data = \App\Cost_centres::where('company_id', Auth::user()->company_id)->get();
        foreach ($cost_center_data as $value) {

            $cost_center[$value->cost_id] = $value->cost_centre;
        }
        $cost_center[0] = " _blank_ ";
//        print_r($cost_center);die('--');

        return view('admin.purchase_requisition.create', compact('requestors', 'cost_center', 'g_l_account', 'country', 'vendor', 'currency', 'material', 'status', 'project_ids', 'task_ids', 'phase_ids', 'purchase_order_number', 'requestor', 'changed_by', 'requestedby', 'created_by'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $purchase_requisition_data = Input::all();


        $elementdata = $purchase_requisition_data['elementdata'];
        $purchase = $purchase_requisition_data['obj'];
        $purchase['requisition_number'] = rand(10000, 9000000);
        $purchase['company_id'] = Auth::user()->company_id;


        $validationmessages = [
            'requisition_number.required' => 'Please enter purchase requisition number',
            'header_note.required' => 'Please enter short description',
            'approver_1.required' => 'Please select 1st approver',
        ];


        $validator = Validator::make($purchase, [
                    'requisition_number' => 'required|unique:purchase_requisition',
                    'header_note' => 'required|max:255',
                    'approver_1' => 'required',
                        ], $validationmessages);

        if ($validator->fails()) {
            $msgs = $validator->messages();
            return response()->json($msgs);
        }

        foreach ($elementdata as $index => $row) {
            $row['requisition_number'] = $purchase['requisition_number'];
            $row['company_id'] = Auth::user()->company_id;

            unset($row['optradio']);

            $validationmsgitem = [
                'status.required' => 'Please enter select Status ' . ($index + 1) . ' record',
                'requisition_number.required' => 'Please enter Requisition number on ' . ($index + 1) . ' record',
                'status.required' => 'Please enter Status on ' . ($index + 1) . ' record',
                'item_no.required' => 'Please enter Item number on ' . ($index + 1) . ' record',
                'item_quantity.required' => 'Please enter Item quantity on ' . ($index + 1) . ' record',
                'item_cost.required' => 'Please enter Item cost on ' . ($index + 1) . ' record',
                'currency.required' => 'Please select Currency type on ' . ($index + 1) . ' record',
                'delivery_date.required' => 'Please select Delevery date on ' . ($index + 1) . ' record',
                'requestor.required' => 'Please select a Requestor on ' . ($index + 1) . ' record',
                'item_category.required' => 'Please select Item category on ' . ($index + 1) . ' record',
                'material.required' => 'Please enter Material on ' . ($index + 1) . ' record',
                'quantity_unit.required' => 'Please select a Quantity unit on ' . ($index + 1) . ' record',
                'material_group.required' => 'Please enter Material group on ' . ($index + 1) . ' record',
                'vendor.required' => 'Please select a Vendor on ' . ($index + 1) . ' record',
                'contract_number.required' => 'Please enter Contract number on ' . ($index + 1) . ' record',
                'contract_item_number.required' => 'Please enter Contract Item number on ' . ($index + 1) . ' record',
                'material_description.required' => 'Please enter Material description on ' . ($index + 1) . ' record',
                'g_l_account.required' => 'Please select a requisition g/l account for item ' . ($index + 1),
                'requestor.required' => 'Please enter requestor detail for item ' . ($index + 1),
                'processing_status.required' => 'Please select a status for item ' . ($index + 1),
                'add1.required' => 'Please enter your street address for item ' . ($index + 1),
                'postal_code.required' => 'Please enter your Postal code / Zip code' . ($index + 1),
            ];

            $validator = Validator::make($row, ['status' => 'required',
                        'item_no' => 'required',
                        'item_quantity' => 'required',
                        'item_cost' => 'required',
                        'currency' => 'required',
                        'delivery_date' => 'required',
                        'requestor' => 'required',
                        'item_category' => 'required',
                        'material' => 'required',
                        'quantity_unit' => 'required',
                        'material_group' => 'required',
                        'vendor' => 'required',
                        'contract_number' => 'required',
                        'contract_item_number' => 'required',
                        'material_description' => 'required|max:255',
                        'processing_status' => 'required',
                        'g_l_account' => 'required',
                        'add1' => 'required',
                        'postal_code' => 'required|numeric|digits_between:5,10',
                            ], $validationmsgitem);



            if ($validator->fails()) {
                $msgs = $validator->messages();
                return response()->json($msgs);
            }

            $matchThese = array('requisition_number' => $purchase['requisition_number'], 'item_no' => $row['item_no']);
            purchase_item::updateOrCreate($matchThese, $row);
        }

        purchase_requisition::create($purchase);
        session()->flash('flash_message', 'Purchase Requisition Created successfully...');
        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $token = null)
    {
        $purchase_requisition = purchase_requisition::find($id);
        if (($token == null) && ($purchase_requisition->approved_indicator == '1' || $purchase_requisition->approved_indicator == '2' || $purchase_requisition->approved_indicator == '3' || $purchase_requisition->approved_indicator == '4' || $purchase_requisition->approved_indicator == '5')) {
            session()->flash('flash_message', 'Approver Mail Cycle has already started, It can`t be updated ...');
            return redirect('admin/purchase_requisition');
        }
        $user = Auth::user();
        $username = 'you are not logged in';
        if (Auth::check()) {
            $username = $user->name;
        }

        $phase_ids = array();
        $project_ids = array();
        $task_ids = array();

        //make associative arrays
        //make associative arrays
        $phase_data = Projectphase::all();
        foreach ($phase_data as $value) {
            $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
        }

        $project_data = Project::where('company_id', Auth::user()->company_id)->get();
        foreach ($project_data as $value) {
            $project_ids[$value['id']] = $value['project_Id'] . ' (' . $value['project_name'] . ') ';
        }

        $tasks = Projecttask::where('company_id', Auth::user()->company_id)->get();
        foreach ($tasks as $value) {
            $task_ids[$value['id']] = $value['task_Id'] . ' (' . $value['task_name'] . ') ';
        }


        $userid = 'you are not logged in';
        if (Auth::check()) {
            $userid = $user->id;
        }

        $requestedby = array();
        $temp = Employee_records::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();

        foreach ($temp as $value) {

            $requestedby[$value->employee_id] = $value->employee_id . ' ( ' . (isset($value->employee_first_name) ? $value->employee_first_name : '') . ' ) ';
        }

        $requestor = User::where('name', $username)->first();
        $requestor_data = User::all();
        foreach ($requestor_data as $value) {
            $requestors[$value->id] = $value->name;
        }

        $requestor = $requestor['id'];
        $changed_by = $requestor;
        $created_by = $requestor;




        $material = array();
        $temp = materialmaster::all();
        foreach ($temp as $value) {
            $material[$value['material_number']] = $value['material_name'];
        }

        $country = array();
        $country_data = country::all();
        foreach ($country_data as $value) {
            $country[$value->id] = $value->country_name;
        }

        $currency = array();
        $temp = null;
        $temp = Currency::all();
        foreach ($temp as $value) {
            $currency[$value['id']] = $value['short_code'];
        }


        $purchase_item_data = purchase_item::where('requisition_number', $purchase_requisition->requisition_number)->get();
        //value are static as module not 
        $purchase_order_data = \App\purchase_order::where('company_id', Auth::user()->company_id)->get();
        $purchase_order_number = array();
        foreach ($purchase_order_data as $value) {

            $purchase_order_number[$value->purchase_order_no] = $value->purchase_order_no;
        }


        $approvers = Employee_records::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        foreach ($approvers as $key => $value) {
            $approver[$value->employee_id] = $value->employee_first_name;
        }

        $vendor = array();
        $vendor_data = \App\vendor::all();
        foreach ($vendor_data as $value) {
            $vendor[$value->id] = $value->name;
        }
        //check for status
        $status = '';

        ## demo purposes only once modules are ready use actual data
        $g_l_account = array('1' => '1231', '2' => '1321', '3' => '3232');

        $cost_center_data = \App\Cost_centres::all();
        foreach ($cost_center_data as $value) {

            $cost_center[$value->cost_id] = $value->cost_centre;
        }


        if ($token == null) {
            return view('admin.purchase_requisition.approval', compact('requestors', 'task_ids', 'project_data', 'phase_ids', 'changed_by', 'created_by', 'purchase_order_number', 'username', 'requestedby', 'cost_center', 'g_l_account', 'vendor', 'currency', 'country', 'material', 'purchase_item_data', 'id', 'approver', 'userid', 'purchase_requisition'));
        } else {

            if (count(purchase_requisition::where('approver_token', $token)->get()) < 1) {

                session()->flash('flash_message', 'Request already been approved by you  Or token has expired ...');
                return redirect('admin/purchase_requisition');
            } else {

                return view('admin.purchase_requisition.approval', compact('requestors', 'task_ids', 'project_data', 'phase_ids', 'changed_by', 'created_by', 'purchase_order_number', 'username', 'requestedby', 'cost_center', 'g_l_account', 'vendor', 'currency', 'country', 'material', 'purchase_item_data', 'id', 'approver', 'userid', 'purchase_requisition', 'token'));
            }
        }
    }

    public function reject($id)
    {
        $purchase_requisition = purchase_requisition::find($id);
        $purchase_requisition->approved_indicator = 'rejected';
        $purchase_requisition->approver_token = '';
        $purchase_requisition->save();

        session()->flash('flash_message', 'Request has been rejected ...');
        return redirect('admin/purchase_requisition');
    }

    public function approval(Request $request, $id, $token = null)
    {

        $approval_data = Input::except('_token');

        foreach ($approval_data['data'] as $data) {
            $approval[$data['name']] = $data['value'];
        }
        unset($approval['id']);
        purchase_requisition::find($id)->update($approval);

        $purchase_requisation = purchase_requisition::find($id);


        if ($token != null) {
            if (count(purchase_requisition::where('approver_token', $token)->get()) < 1) {
                session()->flash('flash_message', 'Request already been approved by you  Or token has expired ...');
                return redirect('admin/purchase_requisition');
            }
        }

        if ($purchase_requisation->approved_indicator == '' || $purchase_requisation->approved_indicator == 'rejected') {
            $purchase_requisation->approved_indicator = 1;
            $purchase_requisation->save();
        }

//     $purchase_requisation->update($approval_data);

        $count = 0;
        if ($purchase_requisation->approver_1 != '') {
            $count++;
            if ($purchase_requisation->approver_2 != '') {
                $count++;
                if ($purchase_requisation->approver_3 != '') {
                    $count++;
                    if ($purchase_requisation->approver_4 != '') {
                        $count++;
                    }
                }
            }
        } else {
            session()->flash('flash_message', 'Approver not set ...please set Approvers');
            return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
        }



        if ($purchase_requisation->approved_indicator !== 'approved' && $purchase_requisation->approver_1 != '') {
            $purchase_requisation = purchase_requisition::find($id);
            switch ($purchase_requisation->approved_indicator) {
                case 1:
                    $user_id = isset($purchase_requisation->approver_1) ? $purchase_requisation->approver_1 : '';
                    $to = Employee_records::where('employee_id', $user_id)->first();

                    if (isset($to)) {

                        $purchase_requisation->approver_token = md5($to->email_id . time()) . '1';

                        Mail::to($to->email_id)->send(new purchase_requisition_approval($purchase_requisation));


                        $purchase_requisation->approved_indicator = 2;
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Approval Request sent successfully to 1st approver...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    } else {
                        session()->flash('flash_message', 'Approver not set ...pls edit approval settings ');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    }

                    break;
                case 2:
                    $user_id = isset($purchase_requisation->approver_2) ? $purchase_requisation->approver_2 : '';
                    $to = Employee_records::where('employee_id', $user_id)->first();
                    ;

                    if ($user_id != '' && $token == $purchase_requisation->approver_token && isset($to)) {
                        //$token = md5($user_mail['Email']) . time();

                        $purchase_requisation->approver_token = md5($to->email_id . time()) . '2';
                        Mail::to($to->email_id)->send(new purchase_requisition_approval($purchase_requisation));

                        $purchase_requisation->approved_indicator = 3;
                        $purchase_requisation->save();
                        // set the approvers email_id address in place of testers


                        session()->flash('flash_message', 'Approval Request sent successfully to 2nd approver...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    } else {
                        $purchase_requisation->approver_token = '';
                        $purchase_requisation->approved_indicator = 'approved';
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Project Requisition Approved ...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    }

                    break;
                case 3:

                    $user_id = isset($purchase_requisation->approver_3) ? $purchase_requisation->approver_3 : '';
                    $to = Employee_records::where('employee_id', $user_id)->first();
                    ;

                    if ($user_id !== '' && $token == $purchase_requisation->approver_token && isset($to)) {
                        //$token = md5($user_mail['Email']) . time();
                        // set the approvers email_id address in place of testers
                        $purchase_requisation->approver_token = md5($to->email_id . time()) . '3';
                        Mail::to($to->email_id)->send(new purchase_requisition_approval($purchase_requisation));

                        $purchase_requisation->approved_indicator = 4;
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Approval Request sent successfully to 3rd approver...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    } else {
                        $purchase_requisation->approver_token = '';
                        $purchase_requisation->approved_indicator = 'approved';
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Project Requisition Approved ...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    }
                    break;
                case 4:

                    $user_id = isset($purchase_requisation->approver_4) ? $purchase_requisation->approver_4 : '';
                    $to = Employee_records::where('employee_id', $user_id)->first();
                    ;

                    if ($user_id !== '' && $token == $purchase_requisation->approver_token && isset($to)) {

                        $purchase_requisation->approver_token = md5($to->email_id . time()) . '4';
                        Mail::to($to->email_id)->send(new purchase_requisition_approval($purchase_requisation));

                        $purchase_requisation->approved_indicator = 5;
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Approval Request sent successfully to 4th approver...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    } else {
                        $purchase_requisation->approver_token = '';
                        $purchase_requisation->approved_indicator = 'approved';
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Project Requisition Approved ...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    }
                    break;
                case 5:
                    if ($token == $purchase_requisation->approver_token) {
                        $purchase_requisation->approver_token = '';
                        $purchase_requisation->approved_indicator = 'approved';
                        $purchase_requisation->save();

                        session()->flash('flash_message', 'Project Requisition Approved ...');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                        break;
                    } else {
                        session()->flash('flash_message', 'Approval Token expired ... ');
                        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
                    }
                default :

                    break;
            }
        } else {
            if ($token != null) {
                session()->flash('flash_message', 'Project Requisition is already approved...');
                return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
            } else {
                $purchase_requisation = purchase_requisition::find($id);
                $purchase_requisation->approver_token = '';
                $purchase_requisation->approved_indicator = '';
                $purchase_requisation->save();
                session()->flash('flash_message', 'Approval Detail updated ...');
                return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
            }
        }


        session()->flash('flash_message', 'Error occurred...');
        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $country = array();
        $country_data = country::all();
        foreach ($country_data as $value) {
            $country[$value->id] = $value->country_name;
        }
        // get the requestor id 
        $user = Auth::user();
        $username = 'you are not logged in';
        if (Auth::check()) {
            $username = $user->name;
        }
        $requestedby = array();
        $temp = Employee_records::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();

        foreach ($temp as $value) {

            $requestedby[$value->employee_id] = $value->employee_id . ' ( ' . (isset($value->employee_first_name) ? $value->employee_first_name : '') . ' ) ';
        }

        $requestor = User::where('name', $username)->first();
        $requestor_data = User::all();
        foreach ($requestor_data as $value) {
            $requestors[$value->id] = $value->name;
        }

        $requestor = $requestor['id'];
        $changed_by = $requestor;
        $created_by = $requestor;
        // get record of the id trying to edit
        $purchase_requisition = purchase_requisition::find($id);


        //get material records
        $material = array();
        $temp = materialmaster::all();
        foreach ($temp as $value) {
            $material[$value['material_number']] = $value['material_name'];
        }

        $phase_ids = array();
        $project_ids = array();
        $task_ids = array();

        //make associative arrays
        $phase_data = Projectphase::all();
        foreach ($phase_data as $value) {
            $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
        }
        $phase_ids[0] = '_blank_';

        $project_data = Project::where('company_id', Auth::user()->company_id)->get();
        foreach ($project_data as $value) {
            $project_ids[$value['id']] = $value['project_Id'] . ' (' . $value['project_name'] . ') ';
        }
        $project_ids[0] = '_blank_';

        $tasks = Projecttask::where('company_id', Auth::user()->company_id)->get();
        foreach ($tasks as $value) {
            $task_ids[$value['id']] = $value['task_Id'] . ' (' . $value['task_name'] . ') ';
        }
        $task_ids[0] = '_blank_';

        //get currency from currency table
        $currency = array();
        $temp = null;
        $temp = Currency::all();
        foreach ($temp as $value) {
            $currency[$value['id']] = $value['short_code'];
        }

        //check for status
        $status = '';
        if ($purchase_requisition['status'] == 'active') {

            $status = 'active';
        } else {

            $status = 'inactive';
        }

        $purchase_item_data = purchase_item::where('requisition_number', $purchase_requisition->requisition_number)
                            ->where('company_id', Auth::user()->company_id)->get();
        //value are static as module not 
        $purchase_order_data = \App\purchase_order::where('company_id', Auth::user()->company_id)->get();
        $purchase_order_number = array();
        foreach ($purchase_order_data as $value) {

            $purchase_order_number[$value->purchase_order_number] = $value->purchase_order_number;
        }

        $vendor = array();
        $vendor_data = \App\vendor::all();
        foreach ($vendor_data as $value) {
            $vendor[$value->id] = $value->name;
        }
        //check for status
        $status = '';

        $gl = GlAccount::where('company_id', Auth::user()->company_id)->get();
        $g_l_account = array();
        foreach ($gl as $value) {
            $g_l_account[$value->number] = $value->number;
        }

        $cost_center_data = \App\Cost_centres::all();
        foreach ($cost_center_data as $value) {

            $cost_center[$value->cost_id] = $value->cost_centre;
        }
        $cost_center[0] = '_blank_';

        return view('admin.purchase_requisition.edit', compact('requestors', 'vendor', 'cost_center', 'purchase_item_data', 'g_l_account', 'country', 'purchase_requisition', 'currency', 'material', 'status', 'project_ids', 'task_ids', 'phase_ids', 'purchase_order_number', 'requestor', 'changed_by', 'requestedby', 'created_by'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $purchase_requisition_data = Input::all();

        $elementdata = $purchase_requisition_data['elementdata'];
        $purchase = $purchase_requisition_data['obj'];


        $validationmessages = [
            'requisition_number.required' => 'Please enter purchase requisition number',
            'header_note.required' => 'Please enter short description',
            'approver_1.required' => 'Please select approver 1',
        ];


        $validator = Validator::make($purchase, [
                    'requisition_number' => 'required',
                    'approver_1' => 'required',
                    'header_note' => 'required|max:255',
                        ], $validationmessages);

        if ($validator->fails()) {
            $msgs = $validator->messages();
            return response()->json($msgs);
        }

        foreach ($elementdata as $index => $row) {
            $row['requisition_number'] = $purchase['requisition_number'];
            $row['company_id'] = Auth::user()->company_id;
            unset($row['optradio']);

            $validationmsgitem = [
                'processing_status.required' => 'Please select a Processing Status on ' . ($index + 1) . ' record',
                'status.required' => 'Please enter select Status ' . ($index + 1) . ' record',
                'requisition_number.required' => 'Please enter Requisition number on ' . ($index + 1) . ' record',
                'item_no.required' => 'Please enter Item number on ' . ($index + 1) . ' record',
                'item_quantity.required' => 'Please enter Item quantity on ' . ($index + 1) . ' record',
                'item_cost.required' => 'Please enter Item cost on ' . ($index + 1) . ' record',
                'currency.required' => 'Please select Currency type on ' . ($index + 1) . ' record',
                'delivery_date.required' => 'Please select Delevery date on ' . ($index + 1) . ' record',
                'requestor.required' => 'Please select a Requestor on ' . ($index + 1) . ' record',
                'item_category.required' => 'Please select Item category on ' . ($index + 1) . ' record',
                'material.required' => 'Please enter Material on ' . ($index + 1) . ' record',
                'quantity_unit.required' => 'Please select a Quantity unit on ' . ($index + 1) . ' record',
                'material_group.required' => 'Please enter Material group on ' . ($index + 1) . ' record',
                'vendor.required' => 'Please select a Vendor on ' . ($index + 1) . ' record',
                'contract_number.required' => 'Please enter Contract number on ' . ($index + 1) . ' record',
                'contract_item_number.required' => 'Please enter Contract Item number on ' . ($index + 1) . ' record',
                'material_description.required' => 'Please enter Material description on ' . ($index + 1) . ' record',
                'g_l_account.required' => 'Please select a requisition g/l account on ' . ($index + 1) . ' record',
                'add1.required' => 'Please enter your street address',
                'postal_code.required' => 'Please enter your street address' . ($index + 1) . ' record',
            ];
            $validator = Validator::make($row, [
                        'processing_status' => 'required',
                        'add1' => 'required',
                        'g_l_account' => 'required',
                        'status' => 'required',
                        'item_no' => 'required',
                        'item_quantity' => 'required',
                        'item_cost' => 'required',
                        'currency' => 'required',
                        'delivery_date' => 'required',
                        'requestor' => 'required',
                        'item_category' => 'required',
                        'material' => 'required',
                        'quantity_unit' => 'required',
                        'material_group' => 'required',
                        'vendor' => 'required',
                        'contract_number' => 'required',
                        'contract_item_number' => 'required',
                        'material_description' => 'required|max:255',
                        'postal_code' => 'required|numeric|digits_between:5,10',
                            ], $validationmsgitem);


            if ($validator->fails()) {
                $msgs = $validator->messages();
                return response()->json($msgs);
            }

            $matchThese = array('requisition_number' => $purchase['requisition_number'], 'item_no' => $row['item_no']);
            purchase_item::updateOrCreate($matchThese, $row);
        }

        purchase_requisition::where('requisition_number', $purchase['requisition_number'])
                ->update($purchase);
        session()->flash('flash_message', 'Purchase Requisition updated successfully...');
        return response()->json(array('redirect_url' => 'admin/purchase_requisition'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $purchase_requisition = purchase_requisition::find($id);
        $data = purchaseorder_item::where('requisition_number', $purchase_requisition->requisition_number)->get();
        if (count($data) > 0) {
            session()->flash('flash_message', 'Purchase Requisition cannot be deleted. There is purchase order created for it...');
            return redirect('admin/purchase_requisition');
        }

        die();

        $purchase_requisition->delete($id);
        session()->flash('flash_message', 'Purchase Requisition deleted successfully...');
        return redirect('admin/purchase_requisition');
    }

    public function getApproverName()
    {
        $id = Input::all()['id'];
        $employees = Employee_records::where('employee_id', $id)->select('employee_first_name')->first();
        $employees = $employees->toArray();

        return response()->json($employees);
    }

}
