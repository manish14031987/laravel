<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use App\Mail\sales_order_customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sales_order;
use Illuminate\Support\Facades\Auth;
use App\customer_master;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\materialmaster;
use Illuminate\Support\Facades\DB;
use App\materialgroup;
use App\Cost_centres;
use App\Employee_records;
use App\salesregion;
use App\Projectphase;
use App\Mail\sales_order_approval;
use App\Project;

class SalesOrderController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $salesorder_data = DB::table('sales_order')
      ->select('sales_order.*', 'users.name', 'billing_type.name as billing_type', 'customer_master.customer_id', 'salesorganization.sales_organization', 'salesregion.sales_region')
      ->leftJoin('users', 'sales_order.created_by', '=', 'users.id')
      ->leftJoin('customer_master', 'sales_order.customer', '=', 'customer_master.id')
      ->leftJoin('salesorganization', 'sales_order.sales_organization', '=', 'salesorganization.id')
      ->leftJoin('salesregion', 'sales_order.sales_region', '=', 'salesregion.id')
      ->leftJoin('billing_type', 'sales_order.billing_type', '=', 'billing_type.id')
      ->get();
    foreach ($salesorder_data as $key => $value) {
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

    return view('admin.sales_order.index', compact('salesorder_data', 'approver'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create() {

    //get inquiry number
    $inquiry_no = array();
    $inquiry_number = \App\customerinquiry::where('company_id', Auth::user()->company_id)->get();
    foreach ($inquiry_number as $value) {
      $inquiry_no[$value->inquiry_number] = $value->inquiry_number;
    }

    //get quotation number
    $quotation_no = array();
    $quotation_number = \App\quotation::where('company_id', Auth::user()->company_id)->get();
    foreach ($quotation_number as $value) {
      $quotation_no[$value->quotation_number] = $value->quotation_number;
    }
    //created salesorder date
    $salesorder_createdDate = date("Y-m-d");

    //get maximum salesorder number
    $max_salesordernumber = DB::table('sales_order')->MAX('salesorder_number');
    $salesorder_number = array();
    $range = \App\salesOrderRange::where('company_id', Auth::user()->company_id)->get();
    foreach ($range as $value) {
      $start_range = $value->start_range;
      $end_range = $value->end_range;
    }
    if ($max_salesordernumber == null || $max_salesordernumber == 0) {
      $salesorder_number = $start_range;
    } else {
      $salesorder_number = $max_salesordernumber + 1;
      if ($salesorder_number > $end_range) {
        session()->flash('flash_message', 'Please change end range of sales order number in settings...');
        $salesorder_number = '';
      }
    }

    //get sales region
    $salesregion = array();
    $temp = null;
    $temp = salesregion::all();
    foreach ($temp as $value) {
      $salesregion [$value['id']] = $value['sales_region'];
    }

    //get sales organization
    $salesorg = array();
    $sales_org = \App\sales_organization::where('company_id', Auth::user()->company_id)->get();
    foreach ($sales_org as $value) {
      $salesorg[$value->id] = $value->sales_organization;
    }

    //get customer
    $customer_data = customer_master::all();
    $customer_id = array();
    foreach ($customer_data as $customer) {
      $customer_id[$customer->id] = isset($customer->customer_id) ? $customer->customer_id : '';
    }

    //get material
    $material = array();
    $temp = materialmaster::all();
    foreach ($temp as $value) {
      $material[$value['material_number']] = $value['material_name'];
    }

    //get requestedby value
    $requestedby = array();
    $temp = Employee_records::where('company_id', Auth::user()->company_id)->get();
    foreach ($temp as $value) {

      $requestedby[$value->employee_id] = isset($value->employee_id) ? $value->employee_id . ' (' . $value->employee_first_name . ') ' : '';
    }

    //get project number
    $project_data = DB::table("project")
      ->select('project.*')
      ->where('company_id', Auth::user()->company_id)
      ->get();
    $pid = array();
    foreach ($project_data as $key => $projectdata) {
      $pid[$projectdata->project_Id] = isset($projectdata->project_Id) ? $projectdata->project_Id . ' (' . $projectdata->project_name . ') ' : '';
    }
    //get task id
    $task_data = DB::table("tasks_subtask")
      ->select('tasks_subtask.*')
      ->where('company_id', Auth::user()->company_id)
      ->get();
    $tid = array();
    if (count($task_data) > 0)
      foreach ($task_data as $key => $taskdata) {
        $tid[$taskdata->task_Id] = isset($taskdata->task_Id) ? $taskdata->task_Id . ' (' . $taskdata->task_name . ') ' : '';
      }

    //get phase id
    $phase_ids = array();
    $phase_data = Projectphase::all();
    foreach ($phase_data as $value) {
      $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
    }

    //get cost_center
    $cost_centre = Cost_centres::where('company_id', Auth::user()->company_id)->get();
    $cost = array();
    foreach ($cost_centre as $costcenter) {
      $cost[$costcenter->cost_centre] = isset($costcenter->cost_centre) ? $costcenter->cost_centre : '';
    }

    //get reason for rejection
    $reasonRejection = array();
    $reason_data = \App\reasonRejection::where('company_id', Auth::user()->company_id)->get();
    foreach ($reason_data as $value) {
      $reasonRejection[$value->id] = $value->reason_rejection;
    }

    //get biiling type
    $billing_type = array();
    $billing_data = \App\billing_type::where('company_id', Auth::user()->company_id)->get();
    foreach ($billing_data as $value) {
      $billing_type[$value->id] = $value->name;
    }
    return view('admin.sales_order.create', compact('billing_type', 'inquiry_no', 'quotation_no', 'reasonRejection', 'salesorg', 'cost', 'phase_ids', 'pid', 'tid', 'requestedby', 'material', 'salesorder_createdDate', 'pid', 'salesregion', 'customer_id', 'salesorder_number', 'created_on', 'username'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) {
    $sales_data = Input::all();
    $elementdata = $sales_data['elementdata'];
    $sales = $sales_data['obj'];
    $sales['company_id'] = Auth::user()->company_id;
    $sales['created_by'] = Auth::User()->id;
    $sales['created_on'] = date("Y-m-d");

    $validationmessages = [
        'salesorder_description.required' => 'Please enter sales order description',
        'salesorder_description.min' => 'Please enter at least 3 characters',
        'salesorder_description.max' => 'Please enter no more than 250 characters',
        'customer.required' => "Please select customer",
        'sales_organization.required' => 'Please select sales organization',
        'sales_region.required' => "Please select sales region",
        'sales_order_type.required' => "Please select sales order type",
    ];

    $validator = Validator::make($sales, [
          'salesorder_description' => "required|min:3|max:250",
          'customer' => "required",
          'sales_organization' => "required",
          'sales_region' => "required",
          'sales_order_type' => "required",
        ], $validationmessages);
    if ($validator->fails()) {
      $msgs = $validator->messages();
      return response()->json($msgs);
    }
    foreach ($elementdata as $index => $row) {

      $row['salesorder_number'] = $sales['salesorder_number'];
      $row['company_id'] = Auth::user()->company_id;
      $row['created_on'] = date("Y-m-d");
      $row['created_by'] = Auth::User()->id;

      if ($row['auto_billing'] == '' || $row['auto_billing'] == null) {
        $row['auto_billing'] = 0;
      }

      unset($row['optradio']);
      $validationmsgitem = [
          'order_qty.required' => 'Please enter order quantity' . ($index + 1) . ' record',
          'order_qty.numeric' => 'Please enter order quantity in number' . ($index + 1) . '  record',
          'cost_unit.required' => 'Please enter cost unit' . ($index + 1) . ' record',
          'cost_unit.numeric' => 'Please enter cost unit in number' . ($index + 1) . '  record',
          'short_description.required' => 'Please enter short description' . ($index + 1) . ' record',
          'short_description.min' => 'Please enter at least 3 characters.' . ($index + 1) . ' record',
          'short_description.max' => 'Please enter no more than 40 characters.' . ($index + 1) . ' record',
          'discount.numeric' => 'Please enter discount in number' . ($index + 1) . ' record',
          'sales_tax.required' => 'Please enter sales tax' . ($index + 1) . ' record',
          'sales_tax.numeric' => 'Please enter sales tax in number' . ($index + 1) . ' record',
          'freight_charges.numeric' => 'Please enter freight charges in number' . ($index + 1) . ' record',
          'project_id.required' => 'Please select project' . ($index + 1) . ' record',
          'phaseid.required' => 'Please select phase' . ($index + 1) . ' record',
          'task.required' => 'Please select task' . ($index + 1) . ' record',
          'processing_status.required' => 'Please select status' . ($index + 1) . ' record',
          'company_name.required' => 'Please enter company name' . ($index + 1) . ' record',
          'contact_person_name.regex' => 'Please enter contact name in character in ' . ($index + 1) . ' record',
      ];

      $validator = Validator::make($row, ['status' => 'required',
            'order_qty' => "required|numeric",
            'cost_unit' => "required|numeric",
            'short_description' => "required|min:3|max:40",
            'discount' => "numeric",
            'sales_tax' => "required|numeric",
            'freight_charges' => "numeric",
            'project_id' => "required",
            'phaseid' => "required",
            'task' => "required",
            'processing_status' => "required",
            'company_name' => "required",
            'contact_person_name' => "regex:/^[a-zA-Z]+$/u"
          ], $validationmsgitem);

      if ($validator->fails()) {
        $msgs = $validator->messages();
        return response()->json($msgs);
      }
      $matchThese = array('salesorder_number' => $sales['salesorder_number'], 'item_no' => $row['item_no']);
      \App\sales_order_item::updateOrCreate($matchThese, $row);
    }

    Sales_order::create($sales);
    session()->flash('flash_message', 'Sales Order Created Successfully...');
    return response()->json(array('redirect_url' => 'admin/sales_order'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id, $token = null) {
    $sales_order = Sales_order::find($id);
    if (($token == null) && ($sales_order->approved_indicator == '1' || $sales_order->approved_indicator == '2' || $sales_order->approved_indicator == '3' || $sales_order->approved_indicator == '4' || $sales_order->approved_indicator == '5')) {
      session()->flash('flash_message', 'Approver Mail Cycle has already started, It can`t be updated ...');
      return redirect('admin/sales_order');
    }
    //get inquiry number
    $inquiry_no = array();
    $inquiry_number = \App\customerinquiry::where('company_id', Auth::user()->company_id)->get();
    foreach ($inquiry_number as $value) {
      $inquiry_no[$value->inquiry_number] = $value->inquiry_number;
    }

    //get quotation number
    $quotation_no = array();
    $quotation_number = \App\quotation::where('company_id', Auth::user()->company_id)->get();
    foreach ($quotation_number as $value) {
      $quotation_no[$value->quotation_number] = $value->quotation_number;
    }
    //get sales order item data
    $item = DB::table('sales_item')
      ->select('sales_item.*')
      ->where('salesorder_number', $sales_order->salesorder_number)
      ->where('company_id', Auth::user()->company_id)
      ->get();
    foreach ($item as $key => $value) {
      $itemData = $value;
    }
    $created_by = DB::table('users')
      ->select('users.name')
      ->where('id', $sales_order->created_by)
      ->where('company_id', Auth::user()->company_id)
      ->first();

    $changed_by = DB::table('users')
      ->select('users.name')
      ->where('id', $sales_order->changed_by)
      ->where('company_id', Auth::user()->company_id)
      ->first();
    //get customer
    $customer_data = customer_master::all();
    foreach ($customer_data as $customer) {
      $customer_id[$customer->id] = isset($customer->customer_id) ? $customer->customer_id : '';
    }

    $salesregion = array();
    $temp = null;
    $temp = salesregion::all();
    foreach ($temp as $value) {
      $salesregion [$value['id']] = $value['sales_region'];
    }
    //get sales organization
    $salesorg = array();
    $sales_org = \App\sales_organization::where('company_id', Auth::user()->company_id)->get();
    foreach ($sales_org as $value) {
      $salesorg[$value->id] = $value->sales_organization;
    }

    //get reason for rejection
    $reasonRejection = array();
    $reason_data = \App\reasonRejection::where('company_id', Auth::user()->company_id)->get();
    foreach ($reason_data as $value) {
      $reasonRejection[$value->id] = $value->reason_rejection;
    }
    $material = array();
    $temp = materialmaster::all();
    foreach ($temp as $value) {
      $material[$value['material_number']] = $value['material_name'];
    }

    $salesorder_item_data = \App\sales_order_item::where('salesorder_number', $sales_order->salesorder_number)->get();
    //get project number
    $project_data = DB::table("project")
      ->select('project.*')
      ->where('company_id', Auth::user()->company_id)
      ->get();
    $pid = '';
    foreach ($project_data as $key => $projectdata) {
      $pid[$projectdata->project_Id] = isset($projectdata->project_Id) ? $projectdata->project_Id . ' (' . $projectdata->project_name . ') ' : '';
    }
    //get task id
    $task_data = DB::table("tasks_subtask")
      ->select('tasks_subtask.*')
      ->where('company_id', Auth::user()->company_id)
      ->get();
    $tid = '';
    foreach ($task_data as $key => $taskdata) {
      $tid[$taskdata->task_Id] = isset($taskdata->task_Id) ? $taskdata->task_Id . ' (' . $taskdata->task_name . ') ' : '';
    }

    $phase_ids = array();
    $phase_data = Projectphase::all();
    foreach ($phase_data as $value) {
      $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
    }
    //get material group
    $material_group = materialgroup::all();
    foreach ($material_group as $group) {
      $materialgrp[$group->materialgroup] = isset($group->materialgroup) ? $group->materialgroup : '';
    }
    //get cost_center
    $cost_centre = Cost_centres::all();
    foreach ($cost_centre as $costcenter) {
      $cost[$costcenter->cost_centre] = isset($costcenter->cost_centre) ? $costcenter->cost_centre : '';
    }
    //get requestedby value
    $requestedby = array();
    $temp = Employee_records::where('company_id', Auth::user()->company_id)->get();
    foreach ($temp as $value) {

      $requestedby[$value->employee_id] = isset($value->employee_id) ? $value->employee_id . ' (' . $value->employee_first_name . ') ' : '';
    }

    $createdDate = date('Y-m-d', strtotime($sales_order->created_on));

    $user = Auth::user();
    $username = 'you are not logged in';
    if (Auth::check()) {
      $username = $user->name;
    }
    $userid = 'you are not logged in';
    if (Auth::check()) {
      $userid = $user->id;
    }

    if ($token == null) {
      return view('admin.sales_order.approval', compact('id', 'userid', 'quotation_no', 'inquiry_no', 'reasonRejection', 'changed_by', 'salesorg', 'createdDate', 'customerName', 'itemData', 'created_by', 'phase_ids', 'salesorder_item_data', 'id', 'material', 'salesregion', 'sales_order', 'customer_id', 'material_no', 'pid', 'tid', 'materialgrp', 'cost', 'requestedby', 'quotation_id'));
    } else {

      if (count(Sales_order::where('approver_token', $token)->get()) < 1) {

        session()->flash('flash_message', 'Request already been approved by you  Or token has expired ...');
        return redirect('admin/sales_order');
      } else {

        return view('admin.sales_order.approval', compact('id', 'userid', 'quotation_no', 'inquiry_no', 'reasonRejection', 'changed_by', 'salesorg', 'createdDate', 'customerName', 'itemData', 'created_by', 'phase_ids', 'salesorder_item_data', 'id', 'material', 'salesregion', 'sales_order', 'customer_id', 'material_no', 'pid', 'tid', 'materialgrp', 'cost', 'requestedby', 'quotation_id', 'token'));
      }
    }
  }

  public function reject($id) {
    $sales_order = Sales_order::find($id);
    $sales_order->approved_indicator = 'rejected';
    $sales_order->approver_token = '';
    $sales_order->save();

    session()->flash('flash_message', 'Request has been rejected ...');
    return redirect('admin/sales_order');
  }

  public function approval(Request $request, $id, $token = null) {
    $approval_data = Input::except('_token');
    foreach ($approval_data['data'] as $data) {
      $approval[$data['name']] = $data['value'];
    }
    unset($approval['id']);
    Sales_order::find($id)->update($approval);

    $sales_order = Sales_order::find($id);


    if ($token != null) {
      if (count(Sales_order::where('approver_token', $token)->get()) < 1) {
        session()->flash('flash_message', 'Request already been approved by you  Or token has expired ...');
        return redirect('admin/sales_order');
      }
    }

    if ($sales_order->approved_indicator == '' || $sales_order->approved_indicator == 'rejected') {
      $sales_order->approved_indicator = 1;
      $sales_order->save();
    }

//     $purchase_requisation->update($approval_data);

    $count = 0;
    if ($sales_order->approver_1 != '') {
      $count++;
      if ($sales_order->approver_2 != '') {
        $count++;
        if ($sales_order->approver_3 != '') {
          $count++;
          if ($sales_order->approver_4 != '') {
            $count++;
          }
        }
      }
    } else {
      session()->flash('flash_message', 'Approver not set ...please set Approvers');
      return response()->json(array('redirect_url' => 'admin/sales_order'));
    }



    if ($sales_order->approved_indicator !== 'approved' && $sales_order->approver_1 != '') {
      $sales_order = Sales_order::find($id);
      switch ($sales_order->approved_indicator) {
        case 1:
          $user_id = isset($sales_order->approver_1) ? $sales_order->approver_1 : '';
          $to = Employee_records::where('employee_id', $user_id)->first();

          if (isset($to)) {

            $sales_order->approver_token = md5($to->email_id . time()) . '1';

            Mail::to($to->email_id)->send(new sales_order_approval($sales_order));


            $sales_order->approved_indicator = 2;
            $sales_order->save();

            session()->flash('flash_message', 'Approval Request sent successfully to 1st approver...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          } else {
            session()->flash('flash_message', 'Approver not set ...pls edit approval settings ');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          }

          break;
        case 2:
          $user_id = isset($sales_order->approver_2) ? $sales_order->approver_2 : '';
          $to = Employee_records::where('employee_id', $user_id)->first();
          ;

          if ($user_id != '' && $token == $sales_order->approver_token && isset($to)) {
            //$token = md5($user_mail['Email']) . time();

            $sales_order->approver_token = md5($to->email_id . time()) . '2';
            Mail::to($to->email_id)->send(new sales_order_approval($sales_order));

            $sales_order->approved_indicator = 3;
            $sales_order->save();
            // set the approvers email_id address in place of testers


            session()->flash('flash_message', 'Approval Request sent successfully to 2nd approver...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          } else {
            $sales_order->approver_token = '';
            $sales_order->approved_indicator = 'approved';
            $sales_order->save();

            session()->flash('flash_message', 'Sales Order Approved ...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          }

          break;
        case 3:

          $user_id = isset($sales_order->approver_3) ? $sales_order->approver_3 : '';
          $to = Employee_records::where('employee_id', $user_id)->first();


          if ($user_id !== '' && $token == $sales_order->approver_token && isset($to)) {
            //$token = md5($user_mail['Email']) . time();
            // set the approvers email_id address in place of testers
            $sales_order->approver_token = md5($to->email_id . time()) . '3';
            Mail::to($to->email_id)->send(new sales_order_approval($sales_order));

            $sales_order->approved_indicator = 4;
            $sales_order->save();

            session()->flash('flash_message', 'Approval Request sent successfully to 3rd approver...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          } else {
            $sales_order->approver_token = '';
            $sales_order->approved_indicator = 'approved';
            $sales_order->save();

            session()->flash('flash_message', 'Sales Order Approved ...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          }
          break;
        case 4:

          $user_id = isset($sales_order->approver_4) ? $sales_order->approver_4 : '';
          $to = Employee_records::where('employee_id', $user_id)->first();


          if ($user_id !== '' && $token == $sales_order->approver_token && isset($to)) {

            $sales_order->approver_token = md5($to->email_id . time()) . '4';
            Mail::to($to->email_id)->send(new sales_order_approval($sales_order));

            $sales_order->approved_indicator = 5;
            $sales_order->save();

            session()->flash('flash_message', 'Approval Request sent successfully to 4th approver...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          } else {
            $sales_order->approver_token = '';
            $sales_order->approved_indicator = 'approved';
            $sales_order->save();

            session()->flash('flash_message', 'Sales Order Approved ...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          }
          break;
        case 5:
          if ($token == $sales_order->approver_token) {
            $sales_order->approver_token = '';
            $sales_order->approved_indicator = 'approved';
            $sales_order->save();

            session()->flash('flash_message', 'Sales Order Approved ...');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
            break;
          } else {
            session()->flash('flash_message', 'Approval Token expired ... ');
            return response()->json(array('redirect_url' => 'admin/sales_order'));
          }
        default :

          break;
      }
    } else {
      if ($token != null) {
        session()->flash('flash_message', 'Sales Order is already approved...');
        return response()->json(array('redirect_url' => 'admin/sales_order'));
      } else {
        $sales_order = Sales_order::find($id);
        $sales_order->approver_token = '';
        $sales_order->approved_indicator = '';
        $sales_order->save();
        session()->flash('flash_message', 'Approval Detail Updated ...');
        return response()->json(array('redirect_url' => 'admin/sales_order'));
      }
    }


    session()->flash('flash_message', 'Error occurred...');
    return response()->json(array('redirect_url' => 'admin/sales_order'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id) {

    //get inquiry number
    $inquiry_no = array();
    $inquiry_number = \App\customerinquiry::where('company_id', Auth::user()->company_id)->get();
    foreach ($inquiry_number as $value) {
      $inquiry_no[$value->inquiry_number] = $value->inquiry_number;
    }

    //get quotation number
    $quotation_no = array();
    $quotation_number = \App\quotation::where('company_id', Auth::user()->company_id)->get();
    foreach ($quotation_number as $value) {
      $quotation_no[$value->quotation_number] = $value->quotation_number;
    }

    //get sales order data
    $sales_order = Sales_order::find($id);
    //get sales order item data
    $item = DB::table('sales_item')
      ->select('sales_item.*')
      ->where('salesorder_number', $sales_order->salesorder_number)
      ->where('company_id', Auth::user()->company_id)
      ->get();
    foreach ($item as $key => $value) {
      $itemData = $value;
    }
    $created_by = DB::table('users')
      ->select('users.name')
      ->where('id', $sales_order->created_by)
      ->where('company_id', Auth::user()->company_id)
      ->first();

    $changed_by = DB::table('users')
      ->select('users.name')
      ->where('id', $sales_order->changed_by)
      ->where('company_id', Auth::user()->company_id)
      ->first();
    //get customer
    $customer_data = customer_master::all();
    foreach ($customer_data as $customer) {
      $customer_id[$customer->id] = isset($customer->customer_id) ? $customer->customer_id : '';
    }

    //get sales region
    $salesregion = array();
    $temp = null;
    $temp = salesregion::all();
    foreach ($temp as $value) {
      $salesregion [$value['id']] = $value['sales_region'];
    }
    //get sales organization
    $salesorg = array();
    $sales_org = \App\sales_organization::where('company_id', Auth::user()->company_id)->get();
    foreach ($sales_org as $value) {
      $salesorg[$value->id] = $value->sales_organization;
    }

    //get reason for rejection
    $reasonRejection = array();
    $reason_data = \App\reasonRejection::where('company_id', Auth::user()->company_id)->get();
    foreach ($reason_data as $value) {
      $reasonRejection[$value->id] = $value->reason_rejection;
    }
    //get material
    $material = array();
    $temp = materialmaster::all();
    foreach ($temp as $value) {
      $material[$value['material_number']] = $value['material_name'];
    }
    $salesorder_item_data = \App\sales_order_item::where('salesorder_number', $sales_order->salesorder_number)->get();

    //get project number
    $project = DB::table('project')->where('company_id', Auth::user()->company_id)
      ->get();
    $pid = array();
    foreach ($project as $projectid) {
      $pid[$projectid->project_Id] = $projectid->project_Id . ' ( ' . $projectid->project_name . ' )';
    }
    //get task id
    $task = DB::table('tasks_subtask')
      ->select('tasks_subtask.*')
      ->where('company_id', Auth::user()->company_id)
      ->get();

    $tid = array();
    foreach ($task as $taskid) {
      $tid[$taskid->task_Id] = $taskid->task_Id . ' ( ' . $taskid->task_name . ' )';
    }

    //get phase
    $phase_ids = array();
    $phase_data = Projectphase::all();
    foreach ($phase_data as $value) {
      $phase_ids[$value['id']] = $value['phase_Id'] . ' (' . $value['phase_name'] . ') ';
    }
    //get material group
    $material_group = materialgroup::all();
    foreach ($material_group as $group) {
      $materialgrp[$group->materialgroup] = isset($group->materialgroup) ? $group->materialgroup : '';
    }
    //get cost_center
    $cost_centre = Cost_centres::all();
    foreach ($cost_centre as $costcenter) {
      $cost[$costcenter->cost_centre] = isset($costcenter->cost_centre) ? $costcenter->cost_centre : '';
    }
    //get requestedby value
    $requestedby = array();
    $temp = Employee_records::where('company_id', Auth::user()->company_id)->get();
    foreach ($temp as $value) {

      $requestedby[$value->employee_id] = isset($value->employee_id) ? $value->employee_id . ' (' . $value->employee_first_name . ') ' : '';
    }

    //get biiling type
    $billing_type = array();
    $billing_data = \App\billing_type::where('company_id', Auth::user()->company_id)->get();
    foreach ($billing_data as $value) {
      $billing_type[$value->id] = $value->name;
    }

    $createdDate = date('Y-m-d', strtotime($sales_order->created_on));
    return view('admin.sales_order.edit', compact('billing_type', 'quotation_no', 'inquiry_no', 'reasonRejection', 'changed_by', 'salesorg', 'createdDate', 'customerName', 'itemData', 'created_by', 'phase_ids', 'salesorder_item_data', 'id', 'material', 'salesregion', 'sales_order', 'customer_id', 'material_no', 'pid', 'tid', 'materialgrp', 'cost', 'requestedby', 'quotation_id'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request) {
    $sales_data = Input::all();

    $elementdata = $sales_data['elementdata'];

    $sales = $sales_data['obj'];
    unset($sales['optradio']);
    $validationmessages = [
        'salesorder_description.required' => 'Please enter sales order description',
        'salesorder_description.min' => 'Please enter at least 3 characters',
        'salesorder_description.max' => 'Please enter no more than 250 characters',
        'customer.required' => "Please select customer",
        'sales_organization.required' => 'Please select sales organization',
        'sales_region.required' => "Please select sales region",
        'sales_order_type.required' => "Please select sales order type",
    ];

    $validator = Validator::make($sales, [
          'salesorder_description' => "required|min:3|max:250",
          'customer' => "required",
          'sales_organization' => "required",
          'sales_region' => "required",
          'sales_order_type' => "required",
        ], $validationmessages);
    if ($validator->fails()) {
      $msgs = $validator->messages();
      return response()->json($msgs);
    }
    if (isset($elementdata)) {
      foreach ($elementdata as $index => $row) {
        $row['salesorder_number'] = $sales['salesorder_number'];
        if ($row['auto_billing'] == '' || $row['auto_billing'] == null) {
          $row['auto_billing'] = 0;
        }
        unset($row['optradio']);
        $validationmsgitem = [
            'order_qty.required' => 'Please enter order quantity' . ($index + 1) . ' record',
            'order_qty.numeric' => 'Please enter order quantity in number' . ($index + 1) . '  record',
            'short_description.min' => 'Please enter at least 3 characters.' . ($index + 1) . ' record',
            'short_description.max' => 'Please enter no more than 40 characters.' . ($index + 1) . ' record',
            'cost_unit.required' => 'Please enter cost unit' . ($index + 1) . ' record',
            'cost_unit.numeric' => 'Please enter cost unit in number' . ($index + 1) . '  record',
            'short_description.required' => 'Please enter short description' . ($index + 1) . ' record',
            'discount.numeric' => 'Please enter discount in number' . ($index + 1) . ' record',
            'sales_tax.required' => 'Please enter sales tax' . ($index + 1) . ' record',
            'sales_tax.numeric' => 'Please enter sales tax in number' . ($index + 1) . ' record',
            'freight_charges.numeric' => 'Please enter freight charges in number' . ($index + 1) . ' record',
            'project_id.required' => 'Please select project' . ($index + 1) . ' record',
            'phaseid.required' => 'Please select phase' . ($index + 1) . ' record',
            'task.required' => 'Please select task' . ($index + 1) . ' record',
            'processing_status.required' => 'Please select status' . ($index + 1) . ' record',
            'company_name.required' => 'Please enter company name' . ($index + 1) . ' record',
            'contact_person_name.regex' => 'Please enter contact name in character in ' . ($index + 1) . ' record',
        ];
        $validator = Validator::make($row, ['status' => 'required',
              'order_qty' => "required|numeric",
              'cost_unit' => "required|numeric",
              'short_description' => "required|min:3|max:40",
              'discount' => "numeric",
              'sales_tax' => "required|numeric",
              'freight_charges' => "numeric",
              'project_id' => "required",
              'phaseid' => "required",
              'task' => "required",
              'processing_status' => "required",
              'company_name' => "required",
              'contact_person_name' => "regex:/^[a-zA-Z]+$/u"
            ], $validationmsgitem);
        if ($validator->fails()) {
          $msgs = $validator->messages();
          return response()->json($msgs);
        }
        $row['company_id'] = Auth::user()->company_id;
        $row['changed_by'] = Auth::User()->id;
        $row['changed_on'] = date("Y-m-d");
        $matchThese = array('salesorder_number' => $sales['salesorder_number'], 'item_no' => $row['item_no']);
        \App\sales_order_item::updateOrCreate($matchThese, $row);
      }
    }
    unset($sales['_token']);
    unset($sales['_method']);
    $sales['changed_by'] = Auth::User()->id;
    $sales['changed_on'] = date("Y-m-d");
    Sales_order::where('salesorder_number', $sales['salesorder_number'])
      ->update($sales);
    session()->flash('flash_message', 'Sales Order Updated Successfully...');
    return response()->json(array('redirect_url' => 'admin/sales_order'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) {
    $sales_id = Sales_order::find($id);
    $sales_no = $sales_id->salesorder_number;
    $item_inquiryno = '';
    $item_data = DB::table('sales_item')
      ->select('sales_item.salesorder_number')
      ->where('salesorder_number', $sales_no)
      ->where('company_id', Auth::user()->company_id)
      ->get();
    foreach ($item_data as $value) {
      $item_inquiryno = $value->salesorder_number;
    }
    if ($sales_no == $item_inquiryno) {
      session()->flash('flash_message', 'Sales order cannot be deleted , there is a items in this sales order...');
      return redirect('admin/sales_order');
    } else {
      $sales_id->delete();
      session()->flash('flash_message', 'Sales order deleted successfully...');
      return redirect('admin/sales_order');
    }
  }

  public function deleteItem($id) {
    $sales_id = \App\sales_order_item::find($id);
    $sales_id->delete($id);
    session()->flash('flash_message', 'Sales order item deleted successfully...');
    return redirect('admin/sales_order');
  }

  public function create_ref_quotation() {

    //get quotation
    $quotation_data = DB::table('quotation')
      ->select('quotation.quotation_number')
      ->where('sales_order', null)
      ->get();

    foreach ($quotation_data as $quotationid) {

      $quotation[$quotationid->quotation_number] = $quotationid->quotation_number;
    }

    return view('admin.sales_order.create_ref_form', compact('quotation'));
  }

  public function insert_quotation_to_salesorder(Request $request) {
    $quotation_id = $request['quotation'];
    $status = $request['status'];

    if ($status == 'yes') {
      $quotation_data = DB::table('quotation')
        ->select('quotation.*')
        ->where('quotation_number', $quotation_id)
        ->first();

      $created_on = date('Y-m-d');

      //get login details
      $user = Auth::user();

      if (Auth::check()) {
        $username = $user->name;
      } else {
        $username = 'you are not logged in';
      }

      DB::table('sales_order')
        ->insert(array('sales_order_type' => $request['sales_order_type'], 'inquiry' => $quotation_data->inquiry, 'quotation' => $quotation_id, 'customer' => $quotation_data->customer, 'sales_region' => $quotation_data->sales_region, 'purchase_order_number' => $quotation_data->purchase_order_number, 'purchase_order_date' => $quotation_data->purchase_order_date, 'req_delivery_date' => $quotation_data->req_delivery_date, 'invoice_number' => $quotation_data->invoice_number, 'weight' => $quotation_data->weight, 'unit' => $quotation_data->unit, 'valid_from' => $quotation_data->valid_from, 'valid_to' => $quotation_data->valid_to, 'total_value' => $quotation_data->total_value, 'net_amount' => $quotation_data->net_amount, 'material_number' => $quotation_data->material_number, 'order_qty' => $quotation_data->order_qty, 'customer_material_number' => $quotation_data->customer_material_number, 'cost_per_unit' => $quotation_data->cost_per_unit, 'total_amount' => $quotation_data->total_amount, 'po_item' => $quotation_data->po_item, 'project_number' => $quotation_data->project_number, 'task' => $quotation_data->task, 'cost_center' => $quotation_data->cost_center, 'material_group' => $quotation_data->material_group, 'reason_for_rejection' => $quotation_data->reason_for_rejection, 'requested_by' => $quotation_data->requested_by, 'status' => 'active', 'created_on' => $created_on, 'created_by' => $username));

      $sales_orderno = DB::table('sales_order')
        ->select('sales_order.sales_orderno')
        ->where('quotation', $quotation_id)
        ->first();

      DB::table('quotation')
        ->where('quotation_number', $quotation_id)
        ->update(array('sales_order' => $sales_orderno->sales_orderno));

      DB::table('customer_inquiry')
        ->where('quotation', $quotation_id)
        ->update(array('sales_order' => $sales_orderno->sales_orderno));

      session()->flash('flash_message', 'Sales Order created with ref successfully...');
      return redirect('admin/sales_order');
    } else {
      return redirect('admin/sales_order/create');
    }
  }

  public function send($sales_order_id) {
    $sales_item = DB::table('sales_item')
      ->where('sales_item.sales_orderno', '=', $sales_order_id)
      ->join('sales_pricing', 'sales_item.item', '=', 'sales_pricing.item_no')
      ->get();
    $sales_item = $sales_item->toArray();

    $sales_order = \App\Sales_order::where('sales_orderno', $sales_order_id)->first();
    $sales_order = $sales_order->toArray();

    if (count($sales_order) > 0 && count($sales_item) > 0) {
      $to = \App\customer_master::find($sales_order['customer']);
      if (isset($to) == true) {
        Mail::to($to->email)->send(new sales_order_customer($sales_order, $sales_item));

        session()->flash('flash_message', 'Mail Sent Succesfuly to customer ...');
        return redirect('admin/sales_order');
      } else {
        session()->flash('flash_message', 'Unable to send mail, No such customer found ...');
        return redirect('admin/sales_order');
      }
    } else {
      session()->flash('flash_message', 'Unable to send mail, No such sales order found ...');
      return redirect('admin/sales_order');
    }
  }

  //get Material Description based on material no
  public function getMaterialDescription($materialNo) {
    $materialData = materialmaster::where('material_number', $materialNo)->first();

    return response()->json(['status' => true, 'data' => $materialData]);
  }

}
