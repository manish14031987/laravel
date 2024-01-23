<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\purchase_order;
use App\GoodsReceipt;
use Illuminate\Support\Facades\DB;
use App\purchaseorder_item;
use App\goodsReceiptsItem;
use App\User;
use App\project_gr_cost;
use App\cost_centre_cost;
use App\gr_ir;
use App\materialmaster;
use App\gl;
use App\gl_records;
use App\GlAccount;

class GoodsReceiptController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $goods_receipt = GoodsReceipt::all();
        $createdby = array();
        foreach ($goods_receipt as $key => $value) {
            $createdby[$key] = ($value['created_by'] != '') ? User::where('id', $value['created_by'])->first()['original']['name'] : '';
        }

        return view('admin.goodsreceipt.index', compact('goods_receipt', 'createdby'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //fetch value for purchase order no.
        $purchase_no = array();
        $purchase_order_data = purchase_order::all();

        foreach ($purchase_order_data as $value) {

            $purchase_no[$value->purchase_order_number] = $value->purchase_order_number;
        }

        return view('admin.goodsreceipt.create', compact('purchase_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @credit decreases the account -> spend money 
     * @debit increases the account -> save money
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (Auth::check()) {
            $userid = $user->id;
        }

        $goods_receipt_data = Input::except('_token');

        $created_by = $userid;
        $created_on = date('Y-m-d');
        $changed_on = date('Y-m-d');
        $changed_by = $userid;
        $purchase_order_number = isset($goods_receipt_data['purchase_order_number']) ? $goods_receipt_data['purchase_order_number'] : '';

        if ($purchase_order_number == '') {
            $msgs = ['Please select Purchase Order No'];
            session()->flash('error_message', 'plz select Purchase order...');
            return redirect('admin/goods_receipt/create')->withErrors($msgs)->withInput($goods_receipt_data);
        }

        $item = array();

        if (isset($goods_receipt_data['purchase_order_item_no']))
            foreach ($goods_receipt_data['purchase_order_item_no'] as $key => $value) {

                $item[$key]['purchase_order_item_no'] = $value;
            }

        if (isset($goods_receipt_data['item_description']))
            foreach ($goods_receipt_data['item_description'] as $key => $value) {

                $item[$key]['item_description'] = $value;
            }

        if (isset($goods_receipt_data['vendor_number']))
            foreach ($goods_receipt_data['vendor_number'] as $key => $value) {

                $item[$key]['vendor_number'] = $value;
            }

        if (isset($goods_receipt_data['vendor_name']))
            foreach ($goods_receipt_data['vendor_name'] as $key => $value) {

                $item[$key]['vendor_name'] = $value;
            }

        if (isset($goods_receipt_data['purchase_order_quantity']))
            foreach ($goods_receipt_data['purchase_order_quantity'] as $key => $value) {

                $item[$key]['purchase_order_quantity'] = $value;
            }

        if (isset($goods_receipt_data['quantity_received']))
            foreach ($goods_receipt_data['quantity_received'] as $key => $value) {

                $item[$key]['quantity_received'] = $value;
            }


        if (isset($goods_receipt_data['quantity_remaining']))
            foreach ($goods_receipt_data['quantity_remaining'] as $key => $value) {

                $item[$key]['quantity_remaining'] = $value;
            }

        if (isset($goods_receipt_data['bill_of_lading']))
            foreach ($goods_receipt_data['bill_of_lading'] as $key => $value) {

                $item[$key]['bill_of_lading'] = $value;
            }

        if (isset($goods_receipt_data['delivery_note']))
            foreach ($goods_receipt_data['delivery_note'] as $key => $value) {

                $item[$key]['delivery_note'] = $value;
            }

        if (isset($goods_receipt_data['status']))
            foreach ($goods_receipt_data['status'] as $key => $value) {
                $item[$key]['status'] = $value;
            }

        if (count($item) == 0) {
            $msgs = ['Please Valid Purchase Order No'];
            session()->flash('error_message', 'Goods Receipt is empty , no item(s) found...');
            session()->flash('purchase_order', $purchase_order_number);
            return redirect('admin/goods_receipt/create')->withErrors($msgs)->withInput($goods_receipt_data);
        }

        foreach ($item as $goods_receipt_item) {


            $goods_receipt_item['purchase_order_number'] = $purchase_order_number;


            $validationmessages = [
                'purchase_order_number.required' => 'Please select Purchase Order Number',
                'purchase_order_item_no.required' => 'Please enter Item No',
                'item_description.required' => 'Please enter Item Description',
                'vendor_number.required' => 'Please enter Vendor Number',
                'vendor_name.unique' => 'please enter Vendor Name',
                'purchase_order_quantity.required' => 'Please enter Purchase Order Quantity',
                'quantity_received.required' => 'Please enter Quantity Received',
                'quantity_remaining.required' => 'Please enter Quantity Remaining',
                'bill_of_lading.required' => 'Please enter Bill of Lading',
                'delivery_note.required' => 'Please enter Delivery Note',
            ];

            $validator = Validator::make($goods_receipt_item, [
                        'purchase_order_number' => 'required|filled',
                        'purchase_order_item_no' => 'required|filled',
                        'item_description' => 'required|filled',
                        'vendor_number' => 'required|filled',
                        'vendor_name' => 'required|filled',
                        'purchase_order_quantity' => 'required|filled',
                        'quantity_received' => 'required|filled',
                        'quantity_remaining' => 'required|filled',
                        'bill_of_lading' => 'required|filled',
                        'delivery_note' => 'required|filled',
                            ], $validationmessages);

            if ($validator->fails()) {
                $msgs = $validator->messages();
                session()->flash('purchase_order', $purchase_order_number);
                return redirect('admin/goods_receipt/create')->withErrors($validator)->withInput($goods_receipt_data);
            }
        }

        // insert in GR table
        $goodsbill = GoodsReceipt::create(['purchase_order_number' => $purchase_order_number, 'posting_date' => $goods_receipt_data['posting_date'], 'document_date' => $goods_receipt_data['document_date'],'company_id' => Auth::user()->company_id,'created_on' => $created_on, 'created_by' => $created_by, 'changed_by' => $changed_by, 'changed_on' => $changed_on]);
        $goodsbill = $goodsbill->toArray();

        // insert in GRI table
        foreach ($item as $goods_receipt_item) {

            $goods_receipt_item['goods_receipt_no'] = $goodsbill['id'];
            $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $goods_receipt_item['purchase_order_item_no']])->first();

            $goods_receipt_item['company_id'] = Auth::user()->company_id;
            if ($po_item != null) {
                $goods_receipt_item['task'] = $po_item->task_id;
                $goods_receipt_item['phase'] = $po_item->phase_id;
                $goods_receipt_item['project'] = $po_item->project_id;
                $goods_receipt_item['item_cost'] = $po_item->item_cost;
                $goods_receipt_item['gl_account'] = $po_item->g_l_account;
                $goods_receipt_item['cost_center'] = $po_item->cost_center;
                $goods_receipt_item['purchase_order_number'] = $purchase_order_number;
            }
          
            goodsReceiptsItem::create($goods_receipt_item);
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

        $item = goodsReceiptsItem::where(['goods_receipt_no' => $goodsbill['id'], 'purchase_order_number' => $purchase_order_number])->get();

        foreach ($item as $goods_receipt_item) {


            $po_item = purchaseorder_item::where(['purchase_order_number' => $purchase_order_number, 'item_no' => $goods_receipt_item['purchase_order_item_no']])->first();

            if (isset($po_item->phase_id) && isset($po_item->task_id) && isset($po_item->project_id))
                if (count($po_item) > 0) {
                    $matchArray = ['project_id' => $po_item->project_id, 'phase' => $po_item->phase_id, 'task_id' => $po_item->task_id, 'item_number' => $po_item->item_no, 'purchase_order_number' => $goods_receipt_data['purchase_order_number']];

                    $data = ['project_id' => $po_item->project_id,
                        'currency' => $po_item->currency,
                        'material_documber_number' => $goodsbill['id'],
                        'posting_date' => $goods_receipt_data['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => $userid
                    ];
                    $data['purchase_order_number'] = $goods_receipt_data['purchase_order_number'];
                    $data['phase'] = $po_item->phase_id;
                    $data['task_id'] = $po_item->task_id;
                    $data['item_number'] = $po_item->item_no;
                    $data['purchase_order_number'] = $goods_receipt_data['purchase_order_number'];
                    $data['created_at'] = date('Y-m-d');
                    $data['amount'] = ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']);
                    $data['dr_cr_indicator'] = 'DR';
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    project_gr_cost::create($data);
                    //add a seprate entry to persist seprate posting dates
                }


            if (isset($po_item->cost_center))
                if (count($po_item) > 0) {
                    $matchArray = ['item_number' => $po_item->item_no, 'purchase_order_number' => $goods_receipt_data['purchase_order_number'], 'cost_centre' => $po_item->cost_center];

                    $data = [
                        'material_documber_number' => $goodsbill['id'],
                        'posting_date' => $goods_receipt_data['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => $userid
                    ];
                    $data['created_at'] = date('Y-m-d');
                    $data['item_number'] = $po_item->item_no;
                    $data['cost_centre'] = $po_item->cost_center;
                    $data['purchase_order_number'] = $goods_receipt_data['purchase_order_number'];
                    $data['currency'] = $po_item->currency;
                    $data['amount'] = ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']);
                    $data['dr_cr_indicator'] = 'DR';
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    $data['description'] = $po_item->material_description;
                    cost_centre_cost::create($data);
                    //add a seprate entry to persist seprate posting dates
                }


            if (count($po_item) > 0) {
                $gl_account_number = GlAccount::where(['type_flag' => 'GRIR','company_id' => Auth::user()->company_id])->first();
                $matchArray = ['item' => $po_item->item_no, 'po_number' => $goods_receipt_data['purchase_order_number']];
                $data = [
                    'po_number' => $goods_receipt_data['purchase_order_number'],
                    'item' => $po_item->item_no,
                    'amount' => $po_item->item_cost * $goods_receipt_item['quantity_received'],
                    'dr_cr_indicator' => 'CR',
                    'vendor_id' => $po_item->vendor,
                    'currency' => $po_item->currency,
                    'material_documber_number' => $goodsbill['id'],
                    'posting_date' => $goods_receipt_data['posting_date'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                    'posted_by' => $userid,
                    'gl_account' => $gl_account_number->number,
                    'transaction_type' => '1'
                    
                ];
                gr_ir::create($data);

                if (count($po_item) > 0) {
                    gl_records::insert(['item_no' => $goods_receipt_item['purchase_order_item_no'], 'remark' => 'GR', 'ref_id' => $goodsbill['id'], 'gl_account_number' =>  $gl_account_number->number, 'dr_cr_indicator' => 'CR', 'amount' => ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']), 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,'purchase_order_no'=>$goods_receipt_item['purchase_order_number'],'vendor'=>$goods_receipt_item['vendor_number'],'created_at'=>date("Y-m-d"),'posting_date' => $goods_receipt_data['posting_date'],'posted_by' => Auth::user()->id]);
                    gl_records::insert(['item_no' => $goods_receipt_item['purchase_order_item_no'], 'remark' => 'GR', 'ref_id' => $goodsbill['id'], 'gl_account_number' => $po_item->g_l_account, 'dr_cr_indicator' => 'DR', 'amount' => ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']), 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,'purchase_order_no'=>$goods_receipt_item['purchase_order_number'],'vendor'=>$goods_receipt_item['vendor_number'],'created_at'=>date("Y-m-d"),'posting_date' => $goods_receipt_data['posting_date'],'posted_by' => Auth::user()->id]);
                    //gl_account update remaining
                    gl::where(['type_flag' => 'GRIR', 'status' => 'active', 'company_id' => Auth::user()->company_id])->increment('credit', ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']));
                    gl::where(['type_flag' => 'GRIR', 'status' => 'active', 'company_id' => Auth::user()->company_id])->increment('balance', -($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']));
                }
                //update the inventory
                materialmaster::where('material_number', $po_item->material)->increment('stock_item', $goods_receipt_item['quantity_received']);
            }

            //update pointer for Gr_quantity received on PO
            purchaseorder_item::where(['purchase_order_number' => $goods_receipt_item['purchase_order_number'], 'item_no' => $goods_receipt_item['purchase_order_item_no']])->increment('item_quantity_gr', -($goods_receipt_item['quantity_received']));
        }

        $gr_indicator = DB::table('purchaseorder_item')
                ->select(DB::raw('sum(IFNULL(item_quantity_gr,0)) as remaining_quantity'))
                ->groupBy(DB::raw('purchase_order_number'))
                ->where('purchase_order_number',$goods_receipt_item['purchase_order_number'])
                ->first();
        if ($gr_indicator->remaining_quantity == 0) {
            purchase_order::where(['purchase_order_number' => $goods_receipt_item['purchase_order_number']])->update(['gr' => '1']);
        }
        session()->flash('flash_message', 'Goods Receipt Added successfully...');
        return redirect('admin/goods_receipt');
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // fetch from GRI table
        $item = goodsReceiptsItem::where('goods_receipt_no', $id)->get()->toArray();
        $goods_receipt = GoodsReceipt::find($id)->first();
        $purchase_order_number = $goods_receipt->purchase_order_number;

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
        foreach ($item as $goods_receipt_item) {

            $po_item = purchaseorder_item::where(['purchase_order_number' => $goods_receipt_item['purchase_order_number'],'item_no' => $goods_receipt_item['purchase_order_item_no']])->first();

            if (isset($po_item->phase_id) && isset($po_item->task_id) && isset($po_item->project_id))
                if (count($po_item) > 0) {

                    $data = ['project_id' => $po_item->project_id,
                        'currency' => $po_item->currency,
                        'material_documber_number' => $id,
                        'posting_date' => $goods_receipt['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => Auth::user()->id
                    ];
                    $data['purchase_order_number'] = $goods_receipt_item['purchase_order_number'];
                    $data['phase'] = $po_item->phase_id;
                    $data['task_id'] = $po_item->task_id;
                    $data['item_number'] = $po_item->item_no;
                    $data['purchase_order_number'] = $goods_receipt_item['purchase_order_number'];
                    $data['created_at'] = date('Y-m-d');
                    $data['dr_cr_indicator'] = 'CR';
                    $data['amount'] = $po_item->item_cost * $goods_receipt_item['quantity_received'];
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    project_gr_cost::create($data);
                }

            if (isset($po_item->cost_center))
                if (count($po_item) > 0) {
                    $matchArray = ['material_documber_number' => $id];
                    $data = [
                        'material_documber_number' => $id,
                        'posting_date' => $goods_receipt['posting_date'],
                        'updated_at' => date('Y-m-d'),
                        'posted_by' => Auth::user()->id
                    ];
                    $data['created_at'] = date('Y-m-d');
                    $data['item_number'] = $po_item->item_no;
                    $data['cost_centre'] = $po_item->cost_center;
                    $data['purchase_order_number'] = $goods_receipt_item['purchase_order_number'];
                    $data['currency'] = $po_item->currency;
                    $data['amount'] = $po_item->item_cost * $goods_receipt_item['quantity_received'];
                    $data['dr_cr_indicator'] = 'CR';
                    $data['vendor'] = $po_item->vendor;
                    $data['gl_account'] = $po_item->g_l_account;
                    $data['description'] = $po_item->material_description;
                    cost_centre_cost::create($data);
                }

            if (count($po_item) > 0) {
                $gl_account_number = GlAccount::where(['type_flag' => 'GRIR','company_id' => Auth::user()->company_id])->first();
                $matchArray = ['item' => $po_item->item_no, 'po_number' => $goods_receipt_item['purchase_order_number']];
                $data = [
                    'po_number' => $goods_receipt_item['purchase_order_number'],
                    'item' => $po_item->item_no,
                    'amount' => $po_item->item_cost * $goods_receipt_item['quantity_received'],
                    'dr_cr_indicator' => 'DR',
                    'vendor_id' => $po_item->vendor,
                    'currency' => $po_item->currency,
                    'material_documber_number' => $id,
                    'posting_date' => $goods_receipt['posting_date'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                    'posted_by' => Auth::user()->id,
                    'gl_account' => $gl_account_number->number,
                    'transaction_type'=>'3'
                ];
                gr_ir::create($data);
            }




            if (count($po_item) > 0) {
                    //gl_account update remaining
                gl::where(['type_flag' => 'GRIR', 'status' => 'active', 'company_id' => Auth::user()->company_id])->increment('debit', ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']));
                gl::where(['type_flag' => 'GRIR', 'status' => 'active', 'company_id' => Auth::user()->company_id])->increment('balance', ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']));
                gl_records::insert(['item_no' => $goods_receipt_item['purchase_order_item_no'],'remark' => 'GR', 'ref_id' => $id, 'gl_account_number' => $gl_account_number->number,'amount' => ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']), 'dr_cr_indicator' =>'DR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,'purchase_order_no'=>$goods_receipt_item['purchase_order_number'],'vendor'=>$goods_receipt_item['vendor_number'],'created_at'=>date("Y-m-d"),'posting_date' => $goods_receipt['posting_date'],'posted_by' => Auth::user()->id]);
                gl_records::insert(['item_no' => $goods_receipt_item['purchase_order_item_no'],'remark' => 'GR', 'ref_id' => $id, 'gl_account_number' => $po_item->g_l_account,'amount' => ($goods_receipt_item['quantity_received'] * $goods_receipt_item['item_cost']), 'dr_cr_indicator' =>'CR', 'created_by' => Auth::user()->id, 'company_id' => Auth::user()->company_id,'purchase_order_no'=>$goods_receipt_item['purchase_order_number'],'vendor'=>$goods_receipt_item['vendor_number'],'created_at'=>date("Y-m-d"),'posting_date' => $goods_receipt['posting_date'],'posted_by' => Auth::user()->id]);
            }



            //update the inventory
            materialmaster::where('material_number', $po_item->material)->decrement('stock_item', $goods_receipt_item['quantity_received']);

            purchaseorder_item::where(['purchase_order_number' => $goods_receipt_item['purchase_order_number'], 'item_no' => $goods_receipt_item['purchase_order_item_no']])->increment('item_quantity_gr', ($goods_receipt_item['quantity_received']));
        }


        GoodsReceipt::find($id)->update(['reversed' => '1']);

        session()->flash('flash_message', 'Goods Receipt Deleted successfully... And Posted data reverted to initial value');
        return redirect('admin/goods_receipt');
    }

    public function getPurchaseitemList($purchase_order_number)
    {
        try {

            $goods_recept_item_qty = purchaseorder_item::groupBy('purchase_order_number')
                            ->selectRaw('sum(item_quantity) as sum')
                            ->where('purchase_order_number', $purchase_order_number)
                            ->pluck('sum')->first();

            if ($goods_recept_item_qty == 0)
                return response()->json(array('status' => 'msg', 'results' => 'All item for this purchase order received ...'));


            if (count($goods_recept_item_qty) > 0) {
                $purchaseItemdata = DB::table("purchaseorder_item")
                        ->where("purchaseorder_item.purchase_order_number", $purchase_order_number)
                        ->where("purchaseorder_item.item_quantity_gr", "<>", 0)
                        ->join('vendor', 'purchaseorder_item.vendor', '=', 'vendor.id')
                        ->select('purchaseorder_item.*', 'vendor.name')
                        ->get();

                $Itemdata = DB::table("purchaseorder_item")
                        ->where("purchase_order_number", $purchase_order_number)
                        ->where('item_quantity_gr', '<>', 0)
                        ->where('company_id', Auth::user()->company_id)
                        ->select('id', 'item_no')
                        ->pluck('item_no', 'item_no')
                        ->prepend('Select All Item', 0);

                //query for received quantity
                $receivedQuantity = DB::table("goods_receipt")
                        ->where("goods_receipt.reversed", "!=", "1")
                        ->select(DB::raw("SUM(goods_receipt_item.quantity_received) as receivedQty"), "goods_receipt_item.purchase_order_item_no", "goods_receipt_item.purchase_order_number", "goods_receipt_item.phase")
                        ->join("goods_receipt_item", "goods_receipt_item.goods_receipt_no", "goods_receipt.id")
                        ->groupBy(DB::raw("goods_receipt_item.purchase_order_item_no"), DB::raw("goods_receipt_item.purchase_order_number"), DB::raw("goods_receipt_item.phase"))
                        ->get();

                //query for get Goods Receipt Amount 
                $goodsReceiptAmount = DB::table("goods_receipt")
                        ->select(DB::raw("SUM(goods_receipt_item.quantity_received) as receivedQty"), "goods_receipt_item.purchase_order_item_no", "goods_receipt_item.purchase_order_number", "goods_receipt_item.item_cost")
                        ->leftjoin("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                        ->where("goods_receipt.reversed", "!=", "1")
                        ->groupBy(DB::raw("goods_receipt_item.purchase_order_item_no"), DB::raw("goods_receipt_item.purchase_order_number"), DB::raw("goods_receipt_item.item_cost"))
                        ->get();

                //Array for received quantity and Goods receipt amount
                $purchase_item_data = $receivedQuantity;

                foreach ($purchase_item_data as $recQtyandGoodsRec) {
                    $recQtyandGoodsRec->phase_id = $recQtyandGoodsRec->phase;
                    $recQtyandGoodsRec->item_no = $recQtyandGoodsRec->purchase_order_item_no;
                    foreach ($goodsReceiptAmount as $goodsRecAmt) {
                        if ($goodsRecAmt->purchase_order_item_no == $recQtyandGoodsRec->purchase_order_item_no && $goodsRecAmt->purchase_order_number == $recQtyandGoodsRec->purchase_order_number && $goodsRecAmt->receivedQty == $recQtyandGoodsRec->receivedQty) {
                            $recQtyandGoodsRec->goodsReceiptAmount = ($goodsRecAmt->receivedQty * $goodsRecAmt->item_cost);
                            $recQtyandGoodsRec->item_cost = $goodsRecAmt->item_cost;
                        }
                    }
                }

                //add $purchaseItemdata into $purchase_item_data array
                foreach ($purchase_item_data as $purchaseItem) {
                    foreach ($purchaseItemdata as $purchage) {
                        if ($purchage->item_no && $purchage->purchase_order_number) {
                            $purchaseItem->id = $purchage->id;
                            $purchaseItem->status = $purchage->status;
                            $purchaseItem->item_category = $purchage->item_category;
                            $purchaseItem->material = $purchage->material;
                            $purchaseItem->material_description = $purchage->material_description;
                            $purchaseItem->item_quantity = $purchage->item_quantity;
                            $purchaseItem->quantity_unit = $purchage->quantity_unit;
                            $purchaseItem->currency = $purchage->currency;
                            $purchaseItem->delivery_date = $purchage->delivery_date;
                            $purchaseItem->material_group = $purchage->material_group;
                            $purchaseItem->vendor = $purchage->vendor;
                            $purchaseItem->requestor = $purchage->requestor;
                            $purchaseItem->contract_number = $purchage->contract_number;
                            $purchaseItem->contract_item_number = $purchage->contract_item_number;
                            $purchaseItem->project_id = $purchage->project_id;
                            $purchaseItem->task_id = $purchage->task_id;
                            $purchaseItem->g_l_account = $purchage->g_l_account;
                            $purchaseItem->cost_center = $purchage->cost_center;
                            $purchaseItem->created_by = $purchage->created_by;
                            $purchaseItem->created_on = $purchage->created_on;
                            $purchaseItem->changed_by = $purchage->changed_by;
                            $purchaseItem->processing_status = $purchage->processing_status;
                            $purchaseItem->title = $purchage->title;
                            $purchaseItem->name = $purchage->name;
                            $purchaseItem->add1 = $purchage->add1;
                            $purchaseItem->add2 = $purchage->add2;
                            $purchaseItem->postal_code = $purchage->postal_code;
                            $purchaseItem->country = $purchage->country;
                            $purchaseItem->requisition_number = $purchage->requisition_number;
                            $purchaseItem->company_id = $purchage->company_id;
                            $purchaseItem->item_quantity_gr = $purchage->item_quantity_gr;
                        }
                    }
                }

                //add received quantity in purchaseItemdata array
                foreach ($purchaseItemdata as $purchaseItem) {
                    foreach ($receivedQuantity as $recQty) {
                        if ($recQty->purchase_order_item_no == $purchaseItem->item_no && $recQty->purchase_order_number == $purchaseItem->purchase_order_number && $recQty->phase == $purchaseItem->phase_id) {
                            $purchaseItem->receivedQty = $recQty->receivedQty;
                            $purchaseItem->phase = $recQty->phase;
                        }
                    }
                }

                if (count($purchaseItemdata) > 0) {
                    return response()->json(array('status' => true, 'results' => $purchaseItemdata->toArray(), 'item' => $Itemdata));
                } else {
                    return response()->json(array('status' => 'msg', 'results' => 'No Purchase order item(s) found...Plz add items to Purchase order'));
                }
            }
        } catch (\Exception $ex) {
            return response()->json(array('status' => false, 'message' => $ex->getMessage()));
        }
    }

    public function getPurchaseitem($purchase_order, $item)
    {

        $goods_recept_item_qty = purchaseorder_item::
                selectRaw('item_quantity')
                ->where('purchase_order_number', $purchase_order)
                ->where('item_no', $item)
                ->where('item_quantity_gr', '<>', 0)
                ->pluck('item_quantity');

        //query for received quantity
        $receivedQuantity = DB::table("goods_receipt")
                ->where("goods_receipt.reversed", "<>", "1")
                ->where('goods_receipt_item.purchase_order_number', $purchase_order)
                ->where('goods_receipt_item.purchase_order_item_no', $item)
                ->select(DB::raw("SUM(goods_receipt_item.quantity_received) as receivedQty"), "goods_receipt_item.purchase_order_item_no", "goods_receipt_item.purchase_order_number", "goods_receipt_item.phase")
                ->join("goods_receipt_item", "goods_receipt_item.goods_receipt_no", "goods_receipt.id")
                ->groupBy(DB::raw("goods_receipt_item.purchase_order_item_no"), DB::raw("goods_receipt_item.purchase_order_number"), DB::raw("goods_receipt_item.phase"))
                ->get();

        //query for get Goods Receipt Amount 
        $goodsReceiptAmount = DB::table("goods_receipt")
                ->where("goods_receipt.reversed", "<>", "1")
                ->where('goods_receipt_item.purchase_order_number', $purchase_order)
                ->where('goods_receipt_item.purchase_order_item_no', $item)
                ->select(DB::raw("SUM(goods_receipt_item.quantity_received) as receivedQty"), "goods_receipt_item.purchase_order_item_no", "goods_receipt_item.purchase_order_number", "goods_receipt_item.item_cost")
                ->join("goods_receipt_item", "goods_receipt.id", "goods_receipt_item.goods_receipt_no")
                ->groupBy(DB::raw("goods_receipt_item.purchase_order_item_no"), DB::raw("goods_receipt_item.purchase_order_number"), DB::raw("goods_receipt_item.item_cost"))
                ->get();
//            print_r($goods_recept_item_qty);
//            print_r($receivedQuantity); 
        //Array for received quantity and Goods receipt amount
        $purchase_item_data = $receivedQuantity;

        // print_r($purchase_item_data);
        foreach ($purchase_item_data as $key => $recQtyandGoodsRec) {
            $purchase_item_data[$key]->phase_id = $recQtyandGoodsRec->phase;
            $purchase_item_data[$key]->item_no = $recQtyandGoodsRec->purchase_order_item_no;
            foreach ($goodsReceiptAmount as $goodsRecAmt) {
                if ($goodsRecAmt->purchase_order_item_no == $recQtyandGoodsRec->purchase_order_item_no && $goodsRecAmt->purchase_order_number == $recQtyandGoodsRec->purchase_order_number && $goodsRecAmt->receivedQty == $recQtyandGoodsRec->receivedQty) {
                    $purchase_item_data[$key]->goodsReceiptAmount = ($goodsRecAmt->receivedQty * $goodsRecAmt->item_cost);
                    $purchase_item_data[$key]->item_cost = $goodsRecAmt->item_cost;
                }
            }
        }

        $purchaseItemdata = DB::table("purchaseorder_item")
                ->where("purchaseorder_item.item_no", $item)
                ->where("purchaseorder_item.purchase_order_number", $purchase_order)
                ->where("purchaseorder_item.item_quantity_gr", "<>", 0)
                ->join('vendor', 'purchaseorder_item.vendor', '=', 'vendor.id')
                ->select('purchaseorder_item.*', 'vendor.name')
                ->get();
        //add $purchaseItemdata into $purchase_item_data array
        foreach ($purchase_item_data as $purchaseItem) {
            foreach ($purchaseItemdata as $purchage) {
                if ($purchage->item_no && $purchage->purchase_order_number) {
                    $purchaseItem->id = $purchage->id;
                    $purchaseItem->status = $purchage->status;
                    $purchaseItem->item_category = $purchage->item_category;
                    $purchaseItem->material = $purchage->material;
                    $purchaseItem->material_description = $purchage->material_description;
                    $purchaseItem->item_quantity = $purchage->item_quantity;
                    $purchaseItem->quantity_unit = $purchage->quantity_unit;
                    $purchaseItem->currency = $purchage->currency;
                    $purchaseItem->delivery_date = $purchage->delivery_date;
                    $purchaseItem->material_group = $purchage->material_group;
                    $purchaseItem->vendor = $purchage->vendor;
                    $purchaseItem->requestor = $purchage->requestor;
                    $purchaseItem->contract_number = $purchage->contract_number;
                    $purchaseItem->contract_item_number = $purchage->contract_item_number;
                    $purchaseItem->project_id = $purchage->project_id;
                    $purchaseItem->task_id = $purchage->task_id;
                    $purchaseItem->g_l_account = $purchage->g_l_account;
                    $purchaseItem->cost_center = $purchage->cost_center;
                    $purchaseItem->created_by = $purchage->created_by;
                    $purchaseItem->created_on = $purchage->created_on;
                    $purchaseItem->changed_by = $purchage->changed_by;
                    $purchaseItem->processing_status = $purchage->processing_status;
                    $purchaseItem->title = $purchage->title;
                    $purchaseItem->name = $purchage->name;
                    $purchaseItem->add1 = $purchage->add1;
                    $purchaseItem->add2 = $purchage->add2;
                    $purchaseItem->postal_code = $purchage->postal_code;
                    $purchaseItem->country = $purchage->country;
                    $purchaseItem->requisition_number = $purchage->requisition_number;
                    $purchaseItem->company_id = $purchage->company_id;
                    $purchaseItem->item_quantity_gr = $purchage->item_quantity_gr;
                }
            }
        }

        //add received quantity in purchaseItemdata array
        foreach ($purchaseItemdata as $purchaseItem) {
            foreach ($receivedQuantity as $recQty) {
                if ($recQty->purchase_order_item_no == $purchaseItem->item_no && $recQty->purchase_order_number == $purchaseItem->purchase_order_number && $recQty->phase == $purchaseItem->phase_id) {
                    $purchaseItem->receivedQty = $recQty->receivedQty;
                    $purchaseItem->phase = $recQty->phase;
                }
            }
        }
        if (count($goods_recept_item_qty) == 0)
            return response()->json(array('status' => 'msg', 'results' => 'All item for this purchase order received ...'));


        if (count($goods_recept_item_qty) > 0) {

            if (count($purchaseItemdata) > 0) {
                return response()->json(array('status' => true, 'results' => $purchaseItemdata->toArray()));
            } else {
                return response()->json(array('status' => 'msg', 'results' => 'No Purchase order item(s) found...Plz add items to Purchase order'));
            }
        }
//        } catch (\Exception $ex) {
//            return response()->json(array('status' => false, 'message' => $ex->getMessage()));
//        }
    }

}
