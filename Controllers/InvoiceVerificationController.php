<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\purchase_order;
use App\GoodsReceipt;
use App\purchaseorder_item;
use App\goodsReceiptsItem;
use App\User;
use App\project_gr_cost;
use App\cost_centre_cost;
use App\gr_ir;
use App\materialmaster;
use App\gl;
use App\gl_records;
use App\invoice_verification;
use App\invoice_verification_item;
use App\vendor;
use App\Projecttask;
use App\Project;
use App\Projectphase;
use App\Cost_centres;
use App\accounts_payable;
use App\GlAccount;
use App\procurement_GST;
use Yajra\DataTables\Facades\DataTables;

class InvoiceVerificationController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.invoice_verification.index');
    }
    
    public function data_table(){
        $invoice_verification = invoice_verification::all();
        $createdby = array();
        foreach ($invoice_verification as $key => $value) {
            $invoice_verification[$key]['created_by'] = ($value['created_by'] != '') ? User::where('id', $value['created_by'])->first()['original']['name'] : '';
        }
        
        return DataTables::of($invoice_verification)
                ->editColumn('active_state', function ($invoice_verification) {
                            if($invoice_verification->reversed == ''){
                                return '<label class="label label-success" style="text-align:center;width:80px;" title="Low" >Active</label>';
                            }
                            else{
                                 return '<label class="label label-danger"  style="text-align:center;width:80px;" title="High" >Reversed</label>';
                            }
                        })
                 ->editColumn('action', function ($invoice_verification) {
                     if($invoice_verification->reversed == ''){
                      return '<a class="btn btn-info btn-xs margin-right-1" id="modal_popup" data-toggle="modal" data-target="#table-pro-view-popup" data-id="'.$invoice_verification->id.'"><i class="fa fa-eye" aria-hidden="true"></i> </a>'
                              . '<a  href="javascript:void(0)" class="btn btn-danger btn-xs margin-right-1" title="Delete" onclick="var res = confirm(`Proceeding further will reverse the posting of data and  delete this invoice recipt.`);if (res) { $(`#delform`).attr(`action`,`' . route('invoice_verification.reversal',$invoice_verification->id) . '`);document.getElementById(`delform`).submit()}'
                                    . '"><i class="fa fa-undo"></i> </a>';
                     }else{
                         return '<a class="btn btn-info btn-xs margin-right-1" id="modal_popup" data-toggle="modal" data-target="#table-pro-view-popup" data-id="'.$invoice_verification->id.'"><i class="fa fa-eye" aria-hidden="true"></i> </a>'
                             . '<a href="javascript:void(0)" onclick="{var res = confirm(`This Bill Contract is in reversed state, cannot be reversed further.`); }
                                           " class="btn btn-danger btn-xs"><i class="fa fa-undo"></i> <!--Delete--> </a>';
                     }
                    })
                 ->editColumn('created_at', function ($invoice_verification) {
                     return date_format($invoice_verification->created_at, 'd-m-Y');
                    })
                ->rawColumns(['active_state','action'])
                ->make();
    }
    
    public function pop_upData() {
        $id = Input::get('id');
        $invoice_verification = invoice_verification::query()
                ->select('invoice_number','po_order_number',DB::raw('DATE_FORMAT(invoice_date, "%d-%m-%Y") as invoice_ddate'),DB::raw('DATE_FORMAT(posting_date, "%d-%m-%Y") as posting_pdate'),'posting_date','reversed',
                        'created_by',DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as created_on'))
                ->where(['id'=>$id])
                ->get()->first();
        $invoice_verification->created_by = $invoice_verification->created_by ? User::where('id', $invoice_verification->created_by)->first()['original']['name'] : '';
        return response()->json(array('data' => $invoice_verification));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
// fetch value for purchase order no.
// If there is no goods receipt exists ,
// the purchase order should not appear in dropdown for invoice verification.

        $purchase_no = DB::table('goods_receipt')
                ->join('purchase_order', 'goods_receipt.purchase_order_number', '=', 'purchase_order.purchase_order_number')
                ->where('goods_receipt.reversed', '=', null)
                ->orwhere('goods_receipt.reversed', '=', '')
                ->select('purchase_order.purchase_order_number')
                ->groupby('purchase_order.purchase_order_number')
                ->pluck('purchase_order.purchase_order_number', 'purchase_order.purchase_order_number');

        $inv_no = invoice_verification::where(['transaction' => 'Invoice', 'company_id' => Auth::user()->company_id, 'reversed' => NULL])->get()->pluck('invoice_number', 'id');
        $vendor = vendor::all()->pluck('name', 'id');

        $task_ids = Projecttask::where('company_id', Auth::user()->company_id)->get()->pluck('task_Id', 'id');
        $project_ids = Project::where('company_id', Auth::user()->company_id)->get()->pluck('project_Id', 'id');
        $phase_ids = Projectphase::get()->pluck('phase_Id', 'id');
        $gl_accounts = GlAccount::get()->pluck('number', 'number');
        $cost_centre = Cost_centres::where('company_id', Auth::user()->company_id)->pluck('cost_centre', 'cost_id');

        return view('admin.invoice_verification.create', compact('purchase_no', 'vendor', 'task_ids', 'project_ids', 'phase_ids', 'gl_accounts', 'cost_centre', 'inv_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @credit decreases the account -> spend money 
     * @debit increases the account -> save money
     */
    public function store(Request $request) {
        $user = Auth::user();

        if (Auth::check()) {
            $userid = $user->id;
        }

        $invoice_verification_data = Input::except('_token');

        $created_by = $userid;
        $created_on = date('Y-m-d');
        $changed_on = date('Y-m-d');
        $changed_by = $userid;

        if ($invoice_verification_data['transaction'] == "Credit memo" || $invoice_verification_data['transaction'] == "Debit memo") {
            $purchase_order_number = isset($invoice_verification_data['purchase_order_number_hidden']) ? $invoice_verification_data['purchase_order_number_hidden'] : '';
        } else {
            $purchase_order_number = isset($invoice_verification_data['purchase_order_number']) ? $invoice_verification_data['purchase_order_number'] : '';
        }


        if ($purchase_order_number == '') {
            $msgs = ['Please select Purchase Order No'];
            session()->flash('error_message', 'plz select Purchase order...');
            return redirect('admin/invoice_verification/create')->withErrors($msgs)->withInput($invoice_verification_data);
        }

        $item = array();

        if (isset($invoice_verification_data['purchase_order_item_no']))
            foreach ($invoice_verification_data['purchase_order_item_no'] as $key => $value) {

                $item[$key]['purchase_order_item_no'] = $value;
                $item[$key]['po_order_number'] = $purchase_order_number;
            }

        if (isset($invoice_verification_data['item_description']))
            foreach ($invoice_verification_data['item_description'] as $key => $value) {
                $item[$key]['item_description'] = $value;
            }

        if (isset($invoice_verification_data['goods_receipt_indicator']))
            foreach ($invoice_verification_data['goods_receipt_indicator'] as $key => $value) {
                $item[$key]['goods_receipt_indicator'] = $value;
            }


        if (isset($invoice_verification_data['purchase_order_value']))
            foreach ($invoice_verification_data['purchase_order_value'] as $key => $value) {

                $item[$key]['po_order_value'] = $value;
            }

        if (isset($invoice_verification_data['g_r_amount']))
            foreach ($invoice_verification_data['g_r_amount'] as $key => $value) {

                $item[$key]['g_r_amount'] = $value;
            }

        if (isset($invoice_verification_data['invoice_value']))
            foreach ($invoice_verification_data['invoice_value'] as $key => $value) {

                $item[$key]['invoice_value'] = $value;
            }

        if (isset($invoice_verification_data['purchase_order_quantity']))
            foreach ($invoice_verification_data['purchase_order_quantity'] as $key => $value) {

                $item[$key]['po_order_qty'] = $value;
            }

        if (isset($invoice_verification_data['qty_recevied']))
            foreach ($invoice_verification_data['qty_recevied'] as $key => $value) {
                if ($invoice_verification_data['transaction'] == 'Invoice') {
                    $item[$key]['qty_recevied'] = $value;
                }
                if ($invoice_verification_data['transaction'] == 'Credit memo') {
                    $item[$key]['quantity_returned'] = $value;
                }
                if ($invoice_verification_data['transaction'] == 'Debit memo') {
                    $item[$key]['additional_quantity'] = $value;
                }
            }

        if (isset($invoice_verification_data['quantity_remaining']))
            foreach ($invoice_verification_data['quantity_remaining'] as $key => $value) {

                $item[$key]['quantity_remaining'] = $value;
            }


        if (isset($invoice_verification_data['difference']))
            foreach ($invoice_verification_data['difference'] as $key => $value) {

                $item[$key]['difference'] = $value;
            }

        if (isset($invoice_verification_data['tax_code']))
            foreach ($invoice_verification_data['tax_code'] as $key => $value) {

                $item[$key]['tax_code'] = $value;
            }

        if (isset($invoice_verification_data['tax_amount']))
            foreach ($invoice_verification_data['tax_amount'] as $key => $value) {

                $item[$key]['tax_amount'] = $value;
            }

        if (isset($invoice_verification_data['g_l_account']))
            foreach ($invoice_verification_data['g_l_account'] as $key => $value) {
                $item[$key]['g_l_account'] = $value;
            }

        if (isset($invoice_verification_data['cost_center']))
            foreach ($invoice_verification_data['cost_center'] as $key => $value) {
                $item[$key]['cost_center'] = $value;
            }

        if (isset($invoice_verification_data['project_id']))
            foreach ($invoice_verification_data['project_id'] as $key => $value) {
                $item[$key]['project_id'] = $value;
            }

        if (isset($invoice_verification_data['phase_id']))
            foreach ($invoice_verification_data['phase_id'] as $key => $value) {
                $item[$key]['phase_id'] = $value;
            }

        if (isset($invoice_verification_data['task_id']))
            foreach ($invoice_verification_data['task_id'] as $key => $value) {
                $item[$key]['task_id'] = $value;
            }

        if (count($item) == 0) {
            $msgs = ['Please Valid Purchase Order No'];
            session()->flash('error_message', 'Invoice Verification is empty , no item(s) found...');
            session()->flash('purchase_order', $purchase_order_number);
            return redirect('admin/invoice_verification/create')->withErrors($msgs)->withInput($invoice_verification_data);
        }

        foreach ($item as $invoice_verification_item) {

            $validationmessages = [
                'po_order_number.required' => 'Please select Purchase Order Number',
                'purchase_order_item_no.required' => 'Please enter Item No',
                'item_description.required' => 'Please enter Item Description',
                'g_r_amount.required' => 'Goods Receipt Amount is missing',
                'invoice_value.required' => 'Please Enter Invoice Value',
                'difference.required' => 'Please Enter Difference Value',
                'purchase_order_quantity.required' => 'Please Enter Purchase Order Quantity',
                'tax_code.required' => 'Please Enter Tax Code',
                'tax_amount.required' => 'Please Enter Tax Amount',
                'g_l_account.required' => 'Please Enter G/L Account',
            ];

            $validator = Validator::make($invoice_verification_item, [
                        'po_order_number' => 'required|filled',
                        'purchase_order_item_no' => 'required|filled',
                        'item_description' => 'required|filled',
                        'tax_code' => 'required|filled',
                        'tax_amount' => 'required|filled',
                        'g_l_account' => 'required|filled',
                            ], $validationmessages);

            if ($validator->fails()) {
                $msgs = $validator->messages();
                session()->flash('purchase_order', $purchase_order_number);
                return redirect('admin/invoice_verification/create')->withErrors($validator)->withInput($invoice_verification_data);
            }
        }

        // insert in GR table
        $goodsbill = invoice_verification::create(['transaction' => $invoice_verification_data['transaction'], 'vendor' => $invoice_verification_data['vendor'], 'po_order_number' => $purchase_order_number, 'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'invoice_date' => $invoice_verification_data['invoice_date'], 'created_on' => $created_on, 'created_by' => $created_by, 'changed_by' => $changed_by, 'changed_on' => $changed_on, 'company_id' => Auth::user()->company_id]);
        $goodsbill = $goodsbill->toArray();

        // insert in GRI table
        foreach ($item as $key => $invoice_verification_item) {

            $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])->first();

            $invoice_verification_item['company_id'] = Auth::user()->company_id;
            if ($po_item != null) {

                $invoice_verification_item['purchase_order_item_no'] = $po_item->item_no;
                $invoice_verification_item['item_description'] = $item[$key]['item_description'];
                //$invoice_verification_item['goods_receipt_indicator'] = $item[$key]['goods_receipt_indicator'];
                $invoice_verification_item['invoice_id'] = $goodsbill['id'];
                $invoice_verification_item['po_order_value'] = $item[$key]['po_order_value'];
                $invoice_verification_item['po_order_qty'] = $item[$key]['po_order_qty'];

                if ($invoice_verification_data['transaction'] == 'Invoice') {
                    $invoice_verification_item['qty_recevied'] = $item[$key]['qty_recevied'];
                }
                if ($invoice_verification_data['transaction'] == 'Credit memo') {
                    $invoice_verification_item['quantity_returned'] = $item[$key]['quantity_returned'];
                }
                if ($invoice_verification_data['transaction'] == 'Debit memo') {
                    $invoice_verification_item['additional_quantity'] = $item[$key]['additional_quantity'];
                }

                $invoice_verification_item['quantity_remaining'] = $item[$key]['quantity_remaining'];

                $invoice_verification_item['g_r_amount'] = $item[$key]['g_r_amount'];
                $invoice_verification_item['g_l_account'] = $item[$key]['g_l_account'];
                $invoice_verification_item['invoice_value'] = $item[$key]['invoice_value'];
                $invoice_verification_item['difference'] = $item[$key]['difference'];
                $invoice_verification_item['tax_code'] = $item[$key]['tax_code'];
                if ($invoice_verification_data['transaction'] == 'Invoice') {
                    $invoice_verification_item['tax_amount'] = $item[$key]['tax_amount'] - $item[$key]['invoice_value'];
                } else {
                    $invoice_verification_item['tax_amount'] = $item[$key]['tax_amount'];
                }
                $invoice_verification_item['currency'] = $po_item->currency;
                $invoice_verification_item['company_id'] = Auth::user()->company_id;
                $invoice_verification_item['cost_center'] = $po_item->cost_center;
                $invoice_verification_item['task_id'] = $po_item->task_id;
                $invoice_verification_item['phase_id'] = $po_item->phase_id;
                $invoice_verification_item['project_id'] = $po_item->project_id;
                $invoice_verification_data['created_by'] = $created_by;
                $invoice_verification_data['changed_by'] = $changed_by;
            }
            invoice_verification_item::create($invoice_verification_item);
        }


        /*  Start of Posting code to other tables   
         *         
         * list of tables to be posted to 
         * 
         * use App\project_gr_cost;
         * use App\cost_centre_cost;
         * use App\gr_ir;
         * use App\materialmaster;
         * use App\gl;
         *  
         */

        $item = invoice_verification_item::where(['invoice_id' => $goodsbill['id'], 'po_order_number' => $purchase_order_number])->get();

        if ($invoice_verification_data['transaction'] == 'Invoice') {
            foreach ($item as $invoice_verification_item) {
                $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])->first();
                //account_payable  
                $account_payable = GlAccount::where(['type_flag' => 'L0002', 'company_id' => Auth::user()->company_id])->first();
                $adata = ['account_id' => $invoice_verification_data['vendor'],
                    'account_name' => 'Vendor Payment',
                    'Reference' => $goodsbill['id'],
                    'type' => 'IR',
                    'amount' => $invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount,
                    'dr_cr_indicator' => "CR",
                    'value' => 0,
                    'company_id' => Auth::user()->company_id];
                accounts_payable::create($adata);
                //DEBIT GST
                if (count($po_item) > 0) {
                    $gst_account = GlAccount::where(['type_flag' => 'GSTP', 'company_id' => Auth::user()->company_id])->first();
                    $gdata = [
                        'gl_account_no' => $gst_account->number,
                        'gl_account_description' => $gst_account->description,
                        'cost_element_type' => $gst_account->gl_account_element_type,
                        'amount' => $invoice_verification_item->tax_amount,
                        'dr_cr_indicator' => 'DR',
                        'balance' => 0,
                        'cleared' => "No",
                        'company_id' => Auth::user()->company_id,
                    ];
                    procurement_GST::create($gdata);
                    //DEBIT GRIR
                    $gl_account_number = GlAccount::where(['type_flag' => 'GRIR', 'company_id' => Auth::user()->company_id])->first();
                    $grdata = [
                        'po_number' => $po_item->purchase_order_number,
                        'item' => $po_item->item_no,
                        'amount' => $invoice_verification_item->invoice_value,
                        'dr_cr_indicator' => 'DR',
                        'vendor_id' => $po_item->vendor,
                        'currency' => $po_item->currency,
                        'material_documber_number' => $goodsbill['id'],
                        'posting_date' => $goodsbill['posting_date'],
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => $userid,
                        'gl_account' => $gl_account_number->number,
                        'ir_value' => $invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount,
                        'transaction_type' => '2'
                    ];
                    gr_ir::create($grdata);

                    $gl_account_number = GlAccount::where(['type_flag' => 'GRIR', 'company_id' => Auth::user()->company_id])->first();
                    gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                        'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $gl_account_number->number,
                        'amount' => ($invoice_verification_item->invoice_value),
                        'dr_cr_indicator' => 'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                        'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                        'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                    gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                        'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $gst_account->number,
                        'amount' => ($invoice_verification_item->tax_amount),
                        'dr_cr_indicator' => 'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                        'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                        'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                    gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                        'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $account_payable->number,
                        'amount' => ($invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount),
                        'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                        'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                        'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                }
            }
            session()->flash('flash_message', 'Invoice Verification completed successfully...');
        } elseif ($invoice_verification_data['transaction'] == 'Credit memo') {
            foreach ($item as $invoice_verification_item) {

                //Reverse the quantity received in purchase order table
                purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])
                        ->increment('item_quantity_gr', $invoice_verification_item['quantity_returned']);

                $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])->first();
                //debit account_payable 
                $account_payable = GlAccount::where(['type_flag' => 'L0002', 'company_id' => Auth::user()->company_id])->first();
                $adata = ['account_id' => $invoice_verification_data['vendor'],
                    'account_name' => 'Vendor Payment',
                    'Reference' => $goodsbill['id'],
                    'type' => 'IR',
                    'amount' => $invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount,
                    'dr_cr_indicator' => "DR",
                    'value' => 0,
                    'company_id' => Auth::user()->company_id];
                accounts_payable::create($adata);

                if (count($po_item) > 0) {
                    $data = ['project_id' => $po_item->project_id,
                        'currency' => $po_item->currency,
                        'material_documber_number' => $goodsbill['id'],
                        'posting_date' => $goodsbill['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => $userid
                    ];
                    $data['purchase_order_number'] = $purchase_order_number;
                    $data['phase'] = $po_item->phase_id;
                    $data['task_id'] = $po_item->task_id;
                    $data['item_number'] = $po_item->item_no;
                    $data['created_at'] = date('Y-m-d');
                    $data['amount'] = $invoice_verification_item->invoice_value;
                    $data['dr_cr_indicator'] = 'CR';
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    $data['transaction_type'] = '2';
                    project_gr_cost::create($data);
                    //add a seprate entry to persist seprate posting dates
                }
                $gst_account = GlAccount::where(['type_flag' => 'GSTP', 'company_id' => Auth::user()->company_id])->first();
                $gdata = [
                    'gl_account_no' => $gst_account->number,
                    'gl_account_description' => $gst_account->description,
                    'cost_element_type' => $gst_account->gl_account_element_type,
                    'amount' => $invoice_verification_item->tax_amount,
                    'dr_cr_indicator' => 'CR',
                    'balance' => 0,
                    'cleared' => "No",
                    'company_id' => Auth::user()->company_id,
                ];
                procurement_GST::create($gdata);

                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $account_payable->number,
                    'amount' => ($invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount),
                    'dr_cr_indicator' => 'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $po_item->g_l_account,
                    'amount' => ($invoice_verification_item->invoice_value),
                    'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $gst_account->number,
                    'amount' => ($invoice_verification_item->tax_amount),
                    'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
            }
            session()->flash('flash_message', 'Invoice Verification Credit Memo completed successfully...');
        } elseif ($invoice_verification_data['transaction'] == 'Debit memo') {
            foreach ($item as $invoice_verification_item) {

                //Increase the quantity received in purchase order table
                purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])
                        ->increment('item_quantity', $invoice_verification_item['additional_quantity']);

                $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $invoice_verification_item['purchase_order_item_no']])->first();
                //credit account_payable 
                $account_payable = GlAccount::where(['type_flag' => 'L0002', 'company_id' => Auth::user()->company_id])->first();
                $adata = ['account_id' => $invoice_verification_data['vendor'],
                    'account_name' => 'Vendor Payment',
                    'Reference' => $goodsbill['id'],
                    'type' => 'IR',
                    'amount' => $invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount,
                    'dr_cr_indicator' => "CR",
                    'value' => 0,
                    'company_id' => Auth::user()->company_id];
                accounts_payable::create($adata);

                if (count($po_item) > 0) {
                    $data = ['project_id' => $po_item->project_id,
                        'currency' => $po_item->currency,
                        'material_documber_number' => $goodsbill['id'],
                        'posting_date' => $goodsbill['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => $userid
                    ];
                    $data['purchase_order_number'] = $purchase_order_number;
                    $data['phase'] = $po_item->phase_id;
                    $data['task_id'] = $po_item->task_id;
                    $data['item_number'] = $po_item->item_no;
                    $data['created_at'] = date('Y-m-d');
                    $data['amount'] = $invoice_verification_item->invoice_value;
                    $data['dr_cr_indicator'] = 'DR';
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    $data['transaction_type'] = '2';
                    project_gr_cost::create($data);
                    //add a seprate entry to persist seprate posting dates
                }
                $gst_account = GlAccount::where(['type_flag' => 'GSTP', 'company_id' => Auth::user()->company_id])->first();
                $gdata = [
                    'gl_account_no' => $gst_account->number,
                    'gl_account_description' => $gst_account->description,
                    'cost_element_type' => $gst_account->gl_account_element_type,
                    'amount' => $invoice_verification_item->tax_amount,
                    'dr_cr_indicator' => 'DR',
                    'balance' => 0,
                    'cleared' => "No",
                    'company_id' => Auth::user()->company_id,
                ];
                procurement_GST::create($gdata);

                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $account_payable->number,
                    'amount' => ($invoice_verification_item->invoice_value + $invoice_verification_item->tax_amount),
                    'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $po_item->g_l_account,
                    'amount' => ($invoice_verification_item->invoice_value),
                    'dr_cr_indicator' => 'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
                gl_records::insert(['purchase_order_no' => $invoice_verification_item->po_order_number, 'item_no' => $invoice_verification_item->purchase_order_item_no,
                    'remark' => 'IR', 'ref_id' => $invoice_verification_item->invoice_id, 'gl_account_number' => $gst_account->number,
                    'amount' => ($invoice_verification_item->tax_amount),
                    'dr_cr_indicator' => 'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,
                    'invoice_number' => $invoice_verification_data['invoice_number'], 'posting_date' => $invoice_verification_data['posting_date'], 'posted_by' => Auth::user()->id,
                    'vendor' => $invoice_verification_data['vendor'], 'created_at' => date('Y-m-d')]);
            }
            session()->flash('flash_message', 'Invoice Verification Debit Memo completed successfully...');
        }

        return redirect('admin/invoice_verification');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = Auth::user();
        if (Auth::check()) {
            $userid = $user->id;
        }
        $item = invoice_verification_item::where('invoice_id', $id)->get()->toArray();
        $goods_receipt = invoice_verification::find($id)->first();
        $purchase_order_number = $goods_receipt->purchase_order_number;

        foreach ($item as $goods_receipt_item) {

            $po_item = invoice_verification_item::where(['po_order_number' => $goods_receipt_item['po_order_number'], 'purchase_order_item_no' => $goods_receipt_item['purchase_order_item_no']])->first();

            $data = ['account_id' => $goods_receipt->vendor,
                'account_name' => 'Vendor Payment',
                'Reference' => $goods_receipt->id,
                'type' => 'IR',
                'dr_cr_indicator' => 'DR',
                'amount' => $goods_receipt_item['invoice_value'] + $goods_receipt_item['tax_amount'],
                'value' => 0,
                'company_id' => Auth::user()->company_id];
            accounts_payable::create($data);

            if (count($po_item) > 0) {
                $gst_account = GlAccount::where(['type_flag' => 'GSTP', 'company_id' => Auth::user()->company_id])->first();
                $gdata = [
                    'gl_account_no' => $gst_account->number,
                    'gl_account_description' => $gst_account->description,
                    'cost_element_type' => $gst_account->gl_account_element_type,
                    'amount' => $goods_receipt_item['tax_amount'],
                    'dr_cr_indicator' => 'CR',
                    'balance' => 0,
                    'cleared' => "Yes",
                    'company_id' => Auth::user()->company_id,
                ];
                procurement_GST::create($gdata);

                $gl_account_number = GlAccount::where(['type_flag' => 'GRIR', 'company_id' => Auth::user()->company_id])->first();
                $data = [
                    'po_number' => $po_item->po_order_number,
                    'item' => $po_item->purchase_order_item_no,
                    'amount' => $goods_receipt_item['invoice_value'],
                    'dr_cr_indicator' => 'CR',
                    'vendor_id' => $goods_receipt->vendor,
                    'currency' => $po_item->currency,
                    'material_documber_number' => $po_item->invoice_id,
                    'posting_date' => $goods_receipt->posting_date,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                    'posted_by' => $userid,
                    'gl_account' => $gl_account_number->number,
                    'ir_value' => $goods_receipt_item['invoice_value'] + $goods_receipt_item['tax_amount'],
                    'transaction_type' => '4'
                ];
                gr_ir::create($data);
                $gl_account_number = GlAccount::where(['type_flag' => 'GRIR', 'company_id' => Auth::user()->company_id])->first();
                gl_records::insert(['remark' => 'IR-Reversed', 'ref_id' => $po_item->invoice_id, 'gl_account_number' => $gl_account_number->number, 'amount' => ($goods_receipt_item['invoice_value']),
                    'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'purchase_order_no' => $goods_receipt_item['po_order_number'],
                    'item_no' => $goods_receipt_item['purchase_order_item_no'], 'company_id' => Auth::user()->company_id,
                    'vendor' => $goods_receipt->vendor, 'invoice_number' => $goods_receipt->invoice_number,
                    'posting_date' => $goods_receipt->posting_date, 'posted_by' => Auth::user()->id, 'created_at' => date('Y-m-d')]);
                gl_records::insert(['remark' => 'IR-Reversed', 'ref_id' => $po_item->invoice_id, 'gl_account_number' => $gst_account->number, 'amount' => ($goods_receipt_item['tax_amount']),
                    'dr_cr_indicator' => 'CR', 'created_by' => Auth::user()->id, 'purchase_order_no' => $goods_receipt_item['po_order_number'],
                    'item_no' => $goods_receipt_item['purchase_order_item_no'], 'company_id' => Auth::user()->company_id,
                    'vendor' => $goods_receipt->vendor, 'invoice_number' => $goods_receipt->invoice_number,
                    'posting_date' => $goods_receipt->posting_date, 'posted_by' => Auth::user()->id, 'created_at' => date('Y-m-d')]);
            }
        }
        invoice_verification::find($id)->update(['reversed' => '1']);
        session()->flash('flash_message', 'Invoice verification Deleted Reversed... And Posted data reverted to initial value');
        return redirect('admin/invoice_verification');
    }

    public function getPurchaseitemList($purchase_order_number) {
        $goods_recept_item_qty = DB::table("goods_receipt")
                ->select(DB::raw('sum(purchase_order_quantity - quantity_received ) as diff'))
                ->where(["goods_receipt.reversed" => null, "goods_receipt_item.purchase_order_number" => $purchase_order_number])
                ->join("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                ->leftjoin('purchaseorder_item', 'goods_receipt_item.purchase_order_number', '=', 'purchaseorder_item.purchase_order_number')
                ->first();
        if ($goods_recept_item_qty->diff < 0)
            return response()->json(array('status' => 'msg', 'results' => 'All item for this purchase order received ...'));
        /**
         * from goods recipt which are not reveresed 
         * get such item no which are not already present in invoice verifiation( which is not not reversed) for a given purchase order
         * then join it with PO table to get the respective POotem id and make a assc array for the drop down
         */
        $Itemdata = DB::table("purchaseorder_item")
                ->where('purchaseorder_item.purchase_order_number', '=', $purchase_order_number)
                ->where('purchaseorder_item.company_id', Auth::user()->company_id)
                ->join('goods_receipt_item', 'goods_receipt_item.purchase_order_number', '=', 'purchaseorder_item.purchase_order_number')
                ->join('goods_receipt', 'goods_receipt.id', '=', 'goods_receipt_item.goods_receipt_no')
                ->where('goods_receipt.reversed', '=', null)
                ->whereNotIn('goods_receipt_item.purchase_order_item_no', function($query) {
                    $query->select('invoice_verification_item.purchase_order_item_no')
                    ->from('invoice_verification_item')
                    ->join('invoice_verification', 'invoice_verification_item.invoice_id', '=', 'invoice_verification.id')
                    ->groupby('invoice_verification_item.purchase_order_item_no')
                    ->where('invoice_verification.reversed', '=', null)
                    ->havingRaw('sum(invoice_verification_item.po_order_qty) - sum(invoice_verification_item.qty_recevied) = 0');
                })->pluck('purchaseorder_item.item_no', 'purchaseorder_item.id')
                ->prepend('Select All Item', 0);
        if (count($goods_recept_item_qty) > 0) {
            $purchaseItemdata = DB::table("purchaseorder_item")
                    ->select('item_no', 'item_quantity_gr')
                    ->where('purchase_order_number', $purchase_order_number)
                    ->where('item_quantity_gr', '>=', 0)
                    ->get()
                    ->toArray();
            if (count($purchaseItemdata) > 0) {
                foreach ($purchaseItemdata as $item) {
                    $invoice = DB::table("invoice_verification_item")
                            ->select('invoice_verification_item.purchase_order_item_no')
                            ->join('invoice_verification', 'invoice_verification_item.invoice_id', '=', 'invoice_verification.id')
                            ->join("goods_receipt_item", "invoice_verification_item.po_order_number", '=', "goods_receipt_item.purchase_order_number")
                            ->where('invoice_verification.reversed', '=', null)
                            ->where('invoice_verification_item.po_order_number', '=', $purchase_order_number)
                            ->where('invoice_verification_item.purchase_order_item_no', '=', $item->item_no)
                            ->orderBy('invoice_verification_item.id', 'DESC')
                            ->first();
                    $invoNo = (array) $invoice;
                    $purchaseItemdata = DB::table("goods_receipt")
                                    ->where("goods_receipt.reversed", "=", null)
                                    ->where('goods_receipt_item.purchase_order_number', $purchase_order_number)
                                    ->where('goods_receipt_item.purchase_order_item_no', $item->item_no)
                                    ->where("purchaseorder_item.item_no", $item->item_no)
                                    ->where("purchaseorder_item.item_quantity_gr", $item->item_quantity_gr)
                                    ->join("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                                    ->join('purchaseorder_item', 'goods_receipt_item.purchase_order_number', '=', 'purchaseorder_item.purchase_order_number')
                                    ->orderBy('goods_receipt_item.id', 'DESC')->first();
                    $result[] = $purchaseItemdata;
                }
            }
            if (count(array_filter($result)) > 0) {
                return response()->json(array('status' => true, 'results' => $result, 'item' => $Itemdata));
            } else {
                return response()->json(array('status' => 'msg', 'results' => 'No Goods Receipt item(s) found...Plz add items to Goods Receipt'));
            }
        }
    }

    public function getPurchaseitem($purchase_order_number, $item) {
        $goods_recept_item_qty = DB::table("goods_receipt")
                ->select(DB::raw('sum(purchase_order_quantity - quantity_received ) as diff'))
                ->where(["goods_receipt.reversed" => null, "goods_receipt_item.purchase_order_number" => $purchase_order_number])
                ->where('purchaseorder_item.id', $item)
                ->join("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                ->join('purchaseorder_item', 'goods_receipt_item.purchase_order_number', '=', 'purchaseorder_item.purchase_order_number')
                ->first();

        if (count($goods_recept_item_qty) == 0)
            return response()->json(array('status' => 'msg', 'results' => 'All item for this purchase order received for this item ...'));

        $purchaseItemdata = DB::table("goods_receipt")
                        ->select('goods_receipt_item.item_description as material_description', 'purchaseorder_item.item_quantity', 'purchaseorder_item.item_cost', 'purchaseorder_item.item_no', 'purchaseorder_item.project_id', 'purchaseorder_item.task_id', 'purchaseorder_item.cost_center', 'purchaseorder_item.phase_id', 'purchaseorder_item.g_l_account', 'purchaseorder_item.item_quantity_gr', 'goods_receipt_item.quantity_remaining', 'goods_receipt_item.quantity_received', 'goods_receipt_item.purchase_order_item_no', 'goods_receipt_item.purchase_order_quantity')
                        ->join("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                        ->join("purchase_order", "purchase_order.purchase_order_number", "goods_receipt_item.purchase_order_number")
                        ->join('purchaseorder_item', 'goods_receipt_item.purchase_order_item_no', '=', 'purchaseorder_item.item_no')
                        ->where("goods_receipt.reversed", "=", null)
                        ->where('goods_receipt_item.purchase_order_number', $purchase_order_number)
                        ->where("purchaseorder_item.id", $item)
                        ->orderBy('goods_receipt_item.id', 'DESC')
                        ->get()->first();
        if (count($purchaseItemdata) > 0) {
            return response()->json(array('status' => true, 'results' => $purchaseItemdata));
        } else {
            return response()->json(array('status' => 'msg', 'results' => 'No Goods Receipt item(s) found...Plz add items to Goods Receipt'));
        }
    }

    public function getData($type, $inv_id) {
        if ($type == 'Credit memo' || $type == 'Debit memo') {
            $invoice_data = invoice_verification::where(['id' => $inv_id, 'reversed' => NULL])->first();
            $invoice_item_data = invoice_verification_item::where('invoice_id', $inv_id)->get();

            foreach ($invoice_item_data as $invoice_item) {
                $po_item = purchaseorder_item::where(['purchase_order_number' => $invoice_item->po_order_number, 'item_no' => $invoice_item->purchase_order_item_no])->first();
                $invoice_item['item_cost'] = $po_item->item_cost;
                $invoice_item['item_quantity_gr'] = $po_item->item_quantity_gr;
                $invoice_item['item_quantity'] = $po_item->item_quantity;
            }

            if (count($invoice_data) == 0) {
                return response()->json(array('status' => true, 'results' => $invoice_item));
            }

            $Itemdata = invoice_verification_item::where('invoice_id', $inv_id)
                    ->pluck('purchase_order_item_no', 'id')
                    ->prepend('Select All Item', 0);
            $purchase_order_no = $invoice_data->po_order_number;
            return response()->json(array('status' => true, 'results' => $invoice_item_data, 'purchase_order_no' => $purchase_order_no, 'invoice_number' => $invoice_data->invoice_number, 'item' => $Itemdata));
        }
    }

    public function getItemData($type, $inv_id, $item_id) {
        if ($type == 'Credit memo' || $type == 'Debit memo') {
            $invoice_data = invoice_verification::where(['id' => $inv_id, 'reversed' => NULL])->first();
            if ($item_id == 0)
                $invoice_item_data = invoice_verification_item::where('invoice_id', $inv_id)->get();
            else
                $invoice_item_data = invoice_verification_item::where(['id' => $item_id])->get();

            foreach ($invoice_item_data as $invoice_item) {
                $po_item = purchaseorder_item::where(['purchase_order_number' => $invoice_item->po_order_number, 'item_no' => $invoice_item->purchase_order_item_no])->first();
                $invoice_item['item_cost'] = $po_item->item_cost;
                $invoice_item['item_quantity_gr'] = $po_item->item_quantity_gr;
                $invoice_item['item_quantity'] = $po_item->item_quantity;
            }
            if (count($invoice_data) == 0) {
                return response()->json(array('status' => true, 'results' => $invoice_item));
            }
            $purchase_order_no = $invoice_data->po_order_number;
            return response()->json(array('status' => true, 'results' => $invoice_item_data, 'purchase_order_no' => $purchase_order_no, 'invoice_number' => $invoice_data->invoice_number));
        }
    }

}
