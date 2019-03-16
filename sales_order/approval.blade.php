@extends('layout.adminlayout')
@section('title','Edit Sales Order')
@section('body')
<!--Sales Order-->
{!! Html::script('/js/jquery.validate.min.js') !!}
{!! Html::script('/js/sales_order.js') !!}
<!--Sales Order-->
<section id="create_form" class="panel">
    <!--- Bootstrap Model --->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>No discount above 100%</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Model -->
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-bottom-50">

                    <ul class="list-unstyled breadcrumb breadcrumb-custom">
                        <li>
                            You are here : <a href="javascript: void(0);" style="margin-left: 10px;">Sales Order</a>
                        </li>
                        <li>
                            <a href="{{url('/admin/sales_order')}}">Sales Order</a>
                        </li>
                        <li>
                            <span>Edit Sales Order</span>
                        </li>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-header card-header-box bg-lightcyan">
                        <h4 class="margin-0">Sales Order</h4>
                    </div>
                    <div class="card-block">
                        <div class="ppm-tabpane">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#inquiry-desc" role="tab">Header Note</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#header_item" role="tab">Header Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#header_pricing" role="tab">Header Pricing Conditions</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <form id="cutomer_inquiry">
                                {!!Form::hidden('salesorder_number',$sales_order->salesorder_number,array('class'=>'form-control border-radius-0'))!!}
                                {{ csrf_field() }}  
                                <div class="tab-content">
                                    <div class="tab-pane active" id="inquiry-desc" role="tabpanel">
                                        <div class="tab-header-title">
                                            Sales Order Detailed Description
                                        </div>
                                        <div class="tab-block">
                                            <div class="form-group row">
                                                {!!Form::textarea('salesorder_description',isset($sales_order->salesorder_description) ? $sales_order->salesorder_description : '',array('class'=>'form-control header_note  border-radius-0 no-resize','placeholder'=>'Please enter sales order detailed description','maxlength'=>255,'disabled'))!!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="header_item" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Number*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('salesorder_number',$sales_order->salesorder_number,array('class'=>'form-control border-radius-0','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Customer*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::select('customer',$customer_id,isset($sales_order->customer) ? $sales_order->customer : '',array('class'=>'form-control border-radius-0 select2 customer','placeholder'=>'Please select customer','id'=>'customer','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales organization*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('sales_organization',$salesorg,isset($sales_order->sales_organization) ? $sales_order->sales_organization : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select sales organization','id'=>'sales_organization','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Inquiry Number*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('inquiry',$inquiry_no,isset($sales_order->inquiry) ? $sales_order->inquiry : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select inquiry number','id'=>'inquiry_no','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Requested By:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('requested_by',$requestedby,isset($sales_order->requested_by) ? $sales_order->requested_by : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select requested by','id'=>'requested_by','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Type*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">                                               
                                                                {!!Form::select('sales_order_type',array('Product Order'=>'Product Order','Service Order'=>'Service Order','Support Order'=>'Support Order','Project Order'=>'Project Order'),isset($sales_order->sales_order_type) ? $sales_order->sales_order_type : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select sales order type','id'=>'salesorder_type','disabled'))!!}                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Customer Name* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('customer_name',isset($sales_order->customer_name) ? $sales_order->customer_name : '',array('class'=>'form-control border-radius-0','placeholder'=>'Customer name','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Region*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('sales_region',$salesregion,isset($sales_order->sales_region) ? $sales_order->sales_region : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select sales region','id'=>'sales_region','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Quotation Number*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('quotation',$quotation_no,isset($sales_order->quotation) ? $sales_order->quotation : '',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select quotation number','id'=>'quotation_no','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="header_pricing" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Gross Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('salesorder_gross_price',isset($sales_order->salesorder_gross_price) ? $sales_order->salesorder_gross_price : '',array('class'=>'form-control border-radius-0','readonly','placeholder'=>'Gross Price','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_profit_amt',isset($sales_order->salesorder_profit_amt) ? $sales_order->salesorder_profit_amt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Profit margin amount','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('salesorder_discount',isset($sales_order->salesorder_discount) ? $sales_order->salesorder_discount : '',array('class'=>'form-control border-radius-0','placeholder'=>'Discount','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount Gross Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_discount_gross_price',isset($sales_order->salesorder_discount_gross_price) ? $sales_order->salesorder_discount_gross_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Discount gross price','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Net Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_net_price',isset($sales_order->salesorder_net_price) ? $sales_order->salesorder_net_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Net price','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Total Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_total_price',isset($sales_order->salesorder_total_price) ? $sales_order->salesorder_total_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Total price','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('salesorder_profit_margin',isset($sales_order->salesorder_profit_margin) ? $sales_order->salesorder_profit_margin : '',array('class'=>'form-control border-radius-0','placeholder'=>'Profit margin','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin Gross Price :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('salesorder_profit_margin_grossprice',isset($sales_order->salesorder_profit_margin_grossprice) ? $sales_order->salesorder_profit_margin_grossprice : '',array('class'=>'form-control border-radius-0','placeholder'=>'Profit margin gross price','readonly'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_discount_amt',isset($sales_order->salesorder_discount_amt) ? $sales_order->salesorder_discount_amt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Discount amount','readonly'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Tax Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_sales_taxamt',isset($sales_order->salesorder_sales_taxamt) ? $sales_order->salesorder_sales_taxamt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Sales tax amount','readonly'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Freight Charges:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('salesorder_freight_charges',isset($sales_order->salesorder_freight_charges) ? $sales_order->salesorder_freight_charges : '',array('class'=>'form-control border-radius-0','placeholder'=>'freight charges','readonly'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-list table-responsive margin-top-15 margin-bottom-40">
                            <thead>
                                <tr> 
                                    <th>Select</th>
                                    <th>Delete</th>
                                    <th>Status</th>
                                    <th>Item No.</th>                                    
                                    <th>Material</th>
                                    <th>Material Description</th>
                                    <th>Customer Material NO.</th>
                                    <th>Order Quantity*</th>
                                    <th>Cost per unit*</th>
                                    <th>Total Amount*</th>
                                    <th>Material Group</th>
                                    <th>Reason For Rejection</th>
                                </tr>
                            </thead>
                            <tbody id='purchase_item_form'>
                                {!! Form::button('Add Item',array('class'=>'btn btn-warning width-100','id'=>'add_row','disabled')) !!}  
                                @if (count($salesorder_item_data)<1)
                                <tr id='purchase_item_0' class = "form">

                                    <td class="text-center line-height-2">
                                        <div class="radio">
                                            <label><input type="radio" name="optradio" id='0' value='#purchase_item_0' class='special-radio' checked=""/></label>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <!--Delete--> </a>

                                    </td> 
                                    <td>                                        
                                        @if(isset($itemData->status))
                                        <input type="image" src="{{asset('vendors/common/img/green.png')}}" alt="" value="active"  onclick="if (this.value == 'active'){this.value = 'inactive'; this.src = '{{asset('vendors/common/img/red.png')}}' } else{this.value = 'active'; this.src = '{{asset('vendors/common/img/green.png')}}' }" name="status" disabled="true">

                                        </input>

                                        @else
                                        <input type="image" src="{{asset('vendors/common/img/red.png')}}" alt="" value="inactive" onclick="if (this.value == 'active'){this.value = 'inactive'; this.src = '{{asset('vendors/common/img/red.png')}}' } else{this.value = 'active'; this.src = '{{asset('vendors/common/img/green.png')}}' }" name="status" disabled="true">

                                        </input>
                                        @endif    
                                    </td>
                                    <td>
                                        {!!Form::text('item_no','10',array('class'=>'form-control padding-input border-radius-0 width-70','min'=>0,'readonly'))!!}
                                    </td>
                                    <td>
                                        {!!Form::select('material',$material,isset($itemData->material) ? $itemData->material : '',array('class'=>'form-control material select2 border-radius-0','placeholder'=>'Please select Material','id'=>'material','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('material_description',isset($itemData->material_description) ? $itemData->material_description : '',array('class'=>'form-control border-radius-0 no-resize','placeholder'=>'Enter Description','maxlength'=>50,'disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('customer_material_no',isset($itemData->customer_material_no) ? $itemData->customer_material_no : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please select customer material no.','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('order_qty',isset($itemData->order_qty) ? $itemData->order_qty : '',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter Quantity','id'=>'order_qty','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('cost_unit',isset($itemData->cost_unit) ? $itemData->cost_unit : '',array('class'=>'form-control border-radius-0 padding-input totalamt','placeholder'=>'Please enter cost per unit','id'=>'cost_unit','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('tota_amt',isset($itemData->tota_amt) ? $itemData->tota_amt : '',array('class'=>'form-control  border-radius-0 padding-input','placeholder'=>'Please enter total amount','disabled'))!!}                                   
                                    </td>
                                    <td>
                                        {!!Form::select('material_group',['Raw Material'=>'Raw Material','Service Material'=>'Service Material'],isset($itemData->material_group) ? $itemData->material_group : '',array('class'=>'form-control select2 border-radius-0','placeholder'=>'Please select Material Group','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::select('reason',$reasonRejection,isset($itemData->reason) ? $itemData->reason : '',array('class'=>'form-control select2  border-radius-0','placeholder'=>'Please select Reason for Rejection','disabled'))!!}
                                    </td>
                                    {!!Form::hidden('project_id')!!}
                                    {!!Form::hidden('phaseid')!!}
                                    {!!Form::hidden('task')!!}
                                    {!!Form::hidden('cost_center')!!}
                                    {!!Form::hidden('processing_status')!!}
                                    {!!Form::hidden('company_name')!!}
                                    {!!Form::hidden('contact_person_name')!!}
                                    {!!Form::hidden('phone_no')!!}
                                    {!!Form::hidden('salesorder_number',$sales_order->salesorder_number)!!}
                                    {!!Form::hidden('short_description')!!}
                                    {!!Form::hidden('requested_by')!!}
                                    {!!Form::hidden('gross_price')!!}
                                    {!!Form::hidden('profit_margin')!!}
                                    {!!Form::hidden('profit_amt')!!}
                                    {!!Form::hidden('profit_gross_price')!!}
                                    {!!Form::hidden('discount')!!}
                                    {!!Form::hidden('discount_amt')!!}
                                    {!!Form::hidden('discount_gross_price')!!}
                                    {!!Form::hidden('sales_tax')!!}
                                    {!!Form::hidden('sales_taxamt')!!}
                                    {!!Form::hidden('net_price')!!}
                                    {!!Form::hidden('freight_charges')!!}
                                    {!!Form::hidden('total_price')!!}
                                    {!!Form::hidden('incoterms')!!}
                                    {!!Form::hidden('invoicing_dates')!!}
                                    {!!Form::hidden('milestone')!!}
                                    {!!Form::hidden('billing_block')!!}
                                    {!!Form::hidden('auto_billing')!!}
                                    {!!Form::hidden('billing_reminder')!!}
                                    {!!Form::hidden('payment_card')!!}
                                    {!!Form::hidden('paypal')!!}
                                    {!!Form::hidden('down_payment')!!}
                                </tr>
                                @endif
                                @if(count($salesorder_item_data)>0)
                                @foreach($salesorder_item_data as $sales_item)   
                                <tr id="purchase_item_{{isset($loop->index)?$loop->index:''}}" class = "form">
                                    <td class="text-center line-height-2">
                                        <div class="radio">
                                            <label><input type="radio" name="optradio" id='{{$loop->index}}' value='#purchase_item_{{$loop->index}}' class='special-radio'  checked=""/></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {!! Form::open(array('url' => array('admin/sales_item/delete_item',$sales_item->id), 'method' => 'DELETE','id'=>'delform'.$sales_item->id)) !!}
                                        <a href="javascript:void(0)"  class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <!--Delete--> </a>
                                        {!! Form::close() !!}
                                    </td> 
                                    <td>                                        
                                        @if(isset($sales_item->status))
                                        @if($sales_item->status == 'active')
                                        <input type="image" disabled="" src="{{asset('vendors/common/img/green.png')}}" alt="" value="active"  onclick="if (this.value == 'active'){this.value = 'inactive'; this.src = '{{asset('vendors/common/img/red.png')}}' } else{this.value = 'active'; this.src = '{{asset('vendors/common/img/green.png')}}' }" name="status">

                                        </input>

                                        @else
                                        <input type="image"  disabled="" src="{{asset('vendors/common/img/red.png')}}" alt="" value="inactive" onclick="if (this.value == 'active'){this.value = 'inactive'; this.src = '{{asset('vendors/common/img/red.png')}}' } else{this.value = 'active'; this.src = '{{asset('vendors/common/img/green.png')}}' }" name="status">

                                        </input>
                                        @endif
                                        @endif    
                                    </td>
                                    <td>
                                        {!!Form::text('item_no',isset($sales_item->item_no) ? $sales_item->item_no : 10,array('class'=>'form-control padding-input border-radius-0 width-70','min'=>0,'disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::select('material',$material,isset($sales_item->material)? $sales_item->material:'',array('class'=>'form-control material select2 border-radius-0','placeholder'=>'Please select Material','id'=>'material','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('material_description',isset($sales_item->material_description)? $sales_item->material_description:'',array('class'=>'form-control border-radius-0 no-resize','placeholder'=>'Enter Description','maxlength'=>50,'disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('customer_material_no',isset($sales_item->customer_material_no)? $sales_item->customer_material_no:'',array('class'=>'form-control border-radius-0','placeholder'=>'Please select customer material no.','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('order_qty',isset($sales_item->order_qty)? $sales_item->order_qty:'',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter Quantity','id'=>'order_qty','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('cost_unit',isset($sales_item->cost_unit)? $sales_item->cost_unit:'',array('class'=>'form-control border-radius-0 padding-input totalamt','placeholder'=>'Please enter cost per unit','id'=>'cost_unit','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::text('tota_amt',isset($sales_item->tota_amt)? $sales_item->tota_amt:'',array('class'=>'form-control  border-radius-0 padding-input','placeholder'=>'Please enter total amount','readonly','disabled'))!!}                                   
                                    </td>
                                    <td>
                                        {!!Form::select('material_group',['Raw Material'=>'Raw Material','Service Material'=>'Service Material'],isset($sales_item->material_group)? $sales_item->material_group:'',array('class'=>'form-control select2 border-radius-0','placeholder'=>'Please select Material Group','disabled'))!!}
                                    </td>
                                    <td>
                                        {!!Form::select('reason',$reasonRejection,isset($sales_item->reason) ? $sales_item->reason : '',array('class'=>'form-control select2  border-radius-0','placeholder'=>'Please select Reason for Rejection','disabled'))!!}
                                    </td>
                                    {!!Form::hidden('project_id',$sales_item->project_id)!!}
                                    {!!Form::hidden('phaseid',$sales_item->phaseid)!!}
                                    {!!Form::hidden('task',$sales_item->task)!!}
                                    {!!Form::hidden('cost_center',$sales_item->cost_center)!!}
                                    {!!Form::hidden('processing_status',$sales_item->processing_status)!!}
                                    {!!Form::hidden('company_name',$sales_item->company_name)!!}
                                    {!!Form::hidden('contact_person_name',$sales_item->contact_person_name)!!}
                                    {!!Form::hidden('phone_no',$sales_item->phone_no)!!}
                                    {!!Form::hidden('salesorder_number',$sales_order->salesorder_number)!!}
                                    {!!Form::hidden('short_description',$sales_item->short_description)!!}
                                    {!!Form::hidden('requested_by',$sales_item->requested_by)!!}
                                    {!!Form::hidden('gross_price',$sales_item->gross_price)!!}
                                    {!!Form::hidden('profit_margin',$sales_item->profit_margin)!!}
                                    {!!Form::hidden('profit_amt',$sales_item->profit_amt)!!}
                                    {!!Form::hidden('profit_gross_price',$sales_item->profit_gross_price)!!}
                                    {!!Form::hidden('discount',$sales_item->discount)!!}
                                    {!!Form::hidden('discount_amt',$sales_item->discount_amt)!!}
                                    {!!Form::hidden('discount_gross_price',$sales_item->discount_gross_price)!!}
                                    {!!Form::hidden('sales_tax',$sales_item->sales_tax)!!}
                                    {!!Form::hidden('sales_taxamt',$sales_item->sales_taxamt)!!}
                                    {!!Form::hidden('net_price',$sales_item->net_price)!!}
                                    {!!Form::hidden('freight_charges',$sales_item->freight_charges)!!}
                                    {!!Form::hidden('total_price',$sales_item->total_price)!!}
                                    {!!Form::hidden('incoterms',$sales_item->incoterms)!!}
                                    {!!Form::hidden('invoicing_dates',$sales_item->invoicing_dates)!!}
                                    {!!Form::hidden('milestone',$sales_item->milestone)!!}
                                    {!!Form::hidden('billing_block',$sales_item->billing_block)!!}
                                    {!!Form::hidden('auto_billing',$sales_item->auto_billing)!!}
                                    {!!Form::hidden('billing_reminder',$sales_item->billing_reminder)!!}
                                    {!!Form::hidden('payment_card',$sales_item->payment_card)!!}
                                    {!!Form::hidden('paypal',$sales_item->paypal)!!}
                                    {!!Form::hidden('down_payment',$sales_item->down_payment)!!}
                                </tr>
                                @endforeach  
                                @endif
                            </tbody>
                        </table>
                        <div id="hidden_row" style="display:none;">
                            <table class="table table-list table-responsive margin-top-15 margin-bottom-40">
                                <thead>
                                    <tr>    
                                        <th>Select</th>
                                        <th>Delete</th>
                                        <th>Status</th>
                                        <th>Item No.</th>                                    
                                        <th>Material</th>
                                        <th>Material Description</th>
                                        <th>Customer Material NO.</th>
                                        <th>Order Quantity*</th>
                                        <th>Cost per unit*</th>
                                        <th>Total Amount*</th>
                                        <th>Material Group</th>
                                        <th>Reason For Rejection</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id='purchase_hidden_row' class = "form">
                                        <td class="text-center line-height-2">
                                            <div class="radio">
                                                <label><input type="radio" name="optradio" id=''/></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" onclick="{var res = confirm('Are you sure you want to delete this customer item'); }" class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash-o"></i> <!--Delete--> </a>
                                        </td> 
                                        <td>                                        
                                            <input type="image" src="{{asset('vendors/common/img/red.png')}}" alt="" value="inactive" onclick=" event.preventDefault(); if (this.value == 'active'){this.value = 'inactive'; this.src = '{{asset('vendors/common/img/red.png')}}' } else{this.value = 'active'; this.src = '{{asset('vendors/common/img/green.png')}}' }" name="status">
                                        </td>
                                        <td>
                                            {!!Form::text('item_no','10',array('class'=>'form-control padding-input border-radius-0 width-70','min'=>0,'readonly'))!!}
                                        </td>
                                        <td>
                                            {!!Form::select('material',$material,'',array('class'=>'form-control  border-radius-0 material','placeholder'=>'Please select Material','id'=>'material'))!!}
                                        </td>
                                        <td>
                                            {!!Form::text('material_description','',array('class'=>'form-control border-radius-0 no-resize','placeholder'=>'Enter Description','maxlength'=>50))!!}
                                        </td>
                                        <td>
                                            {!!Form::text('customer_material_no','',array('class'=>'form-control border-radius-0','placeholder'=>'Please select customer material no.'))!!}
                                        </td>
                                        <td>
                                            {!!Form::text('order_qty','',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter Quantity','id'=>'order_qty'))!!}
                                        </td>
                                        <td>
                                            {!!Form::text('cost_unit','',array('class'=>'form-control border-radius-0 padding-input totalamt','placeholder'=>'Please enter cost per unit','id'=>'cost_unit'))!!}
                                        </td>
                                        <td>
                                            {!!Form::text('tota_amt','',array('class'=>'form-control  border-radius-0 padding-input','placeholder'=>'Please enter total amount','readonly'))!!}                                   
                                        </td>
                                        <td>
                                            {!!Form::select('material_group',['Raw Material'=>'Raw Material','Service Material'=>'Service Material'],'',array('class'=>'form-control  border-radius-0','placeholder'=>'Please select Material Group'))!!}
                                        </td>
                                        <td>
                                            {!!Form::select('reason',$reasonRejection,'',array('class'=>'form-control  border-radius-0','placeholder'=>'Please select Reason for Rejection'))!!}
                                        </td>
                                        {!!Form::hidden('project_id')!!}
                                        {!!Form::hidden('phaseid')!!}
                                        {!!Form::hidden('task')!!}
                                        {!!Form::hidden('cost_center')!!}
                                        {!!Form::hidden('processing_status')!!}
                                        {!!Form::hidden('company_name')!!}
                                        {!!Form::hidden('contact_person_name')!!}
                                        {!!Form::hidden('phone_no')!!}
                                        {!!Form::hidden('salesorder_number',$sales_order->salesorder_number)!!}
                                        {!!Form::hidden('short_description')!!}
                                        {!!Form::hidden('requested_by')!!}
                                        {!!Form::hidden('gross_price')!!}
                                        {!!Form::hidden('profit_margin')!!}
                                        {!!Form::hidden('profit_amt')!!}
                                        {!!Form::hidden('profit_gross_price')!!}
                                        {!!Form::hidden('discount')!!}
                                        {!!Form::hidden('discount_amt')!!}
                                        {!!Form::hidden('discount_gross_price')!!}
                                        {!!Form::hidden('sales_tax')!!}
                                        {!!Form::hidden('sales_taxamt')!!}
                                        {!!Form::hidden('net_price')!!}
                                        {!!Form::hidden('freight_charges')!!}
                                        {!!Form::hidden('total_price')!!}
                                        {!!Form::hidden('incoterms')!!}
                                        {!!Form::hidden('invoicing_dates')!!}
                                        {!!Form::hidden('milestone')!!}
                                        {!!Form::hidden('billing_block')!!}
                                        {!!Form::hidden('auto_billing')!!}
                                        {!!Form::hidden('billing_reminder')!!}
                                        {!!Form::hidden('payment_card')!!}
                                        {!!Form::hidden('paypal')!!}
                                        {!!Form::hidden('down_payment')!!}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="ppm-tabpane">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#Condition" role="tab">Sales Order Item Text</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " data-toggle="tab" href="#inquiry" role="tab">Item Pricing Condition</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#accassign" role="tab">Account Assignment</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#status" role="tab">Status</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#deladdress" role="tab">Delivery Address</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#payment" role="tab">Payment Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#administration" role="tab">Administration</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <form id="Purchase_requisition_two">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="Condition" role="tabpanel">
                                        <div class="tab-header-title">
                                            Sales Order Short Description
                                        </div>
                                        <div class="tab-block">
                                            <div class="form-group row">
                                                {!!Form::textarea('short_description',isset($itemData->short_description) ? $itemData->short_description : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter quotation short description','disabled'))!!}                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="inquiry" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Gross Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('gross_price',isset($itemData->gross_price) ? $itemData->gross_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Gross Price','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('profit_amt',isset($itemData->profit_amt) ? $itemData->profit_amt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Profit margin amount','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('discount',isset($itemData->discount) ? $itemData->discount : '',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter discount','id'=>'discount','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount Gross Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('discount_gross_price',isset($itemData->discount_gross_price) ? $itemData->discount_gross_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Discount gross price','disabled'))!!}  
                                                        </div>
                                                    </div>                                                   
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Tax Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('sales_taxamt',isset($itemData->sales_taxamt) ? $itemData->sales_taxamt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Sales tax amount','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Freight Charges:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('freight_charges',isset($itemData->freight_charges) ? $itemData->freight_charges : '',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter freight charges','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <div class="form-input-icon">
                                                                {!!Form::text('profit_margin',isset($itemData->profit_margin) ? $itemData->profit_margin : '',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter profit margin','id'=>'profit','disabled'))!!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Profit Margin Gross Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('profit_gross_price',isset($itemData->profit_gross_price) ? $itemData->profit_gross_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Profit gross price','readonly','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Discount Amount:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('discount_amt',isset($itemData->discount_amt) ? $itemData->discount_amt : '',array('class'=>'form-control border-radius-0','placeholder'=>'Discount amount','readonly','disabled'))!!}  
                                                        </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Tax*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('sales_tax',isset($itemData->sales_tax) ? $itemData->sales_tax : '',array('class'=>'form-control border-radius-0 totalamt','placeholder'=>'Please enter sales tax','id'=>'sales_tax','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Net Price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('net_price',isset($itemData->net_price) ? $itemData->net_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Net price','readonly','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Total price:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('total_price',isset($itemData->total_price) ? $itemData->total_price : '',array('class'=>'form-control border-radius-0','placeholder'=>'Total price','readonly','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="accassign" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Project Id*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('project_id',$pid,isset($itemData->project_id) ? $itemData->project_id : '',array('class'=>'form-control select2 border-radius-0','placeholder'=>'Please select Project Id','id'=>'project','disabled'))!!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Task Id*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('task',$tid,isset($itemData->task) ? $itemData->task : '',array('class'=>'form-control select2 border-radius-0','placeholder'=>'Please enter task','min'=>0,'id'=>'task','disabled'))!!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Phase ID*:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('phaseid',$phase_ids,isset($itemData->phaseid) ? $itemData->phaseid : '',array('class'=>'form-control select2','placeholder'=>'Please select Phase Id','id'=>'phase','disabled'))!!}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Cost Centre:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::select('cost_center',$cost,isset($itemData->cost_center) ? $itemData->cost_center : '',array('class'=>'form-control select2 border-radius-0','placeholder'=>'Please select cost center','disabled'))!!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="status" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="form-group row padding-bottom-20">
                                                <label class="col-sm-3 control-label">Processing Status*:</label>
                                                <div class="col-sm-5">
                                                    {!!Form::select('processing_status',['Created'=>'Created','In progress'=>'In progress','Closed'=>'Closed'],isset($itemData->processing_status) ? $itemData->processing_status : '',array('class'=>'form-control select2','placeholder'=>'Please select Processing Status','disabled'))!!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="deladdress" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Company Name* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('company_name',isset($itemData->company_name) ? $itemData->company_name : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter company name','disabled'))!!}  
                                                            @if($errors->has('company_name')) 
                                                            <div style='color:red'>
                                                                {{ $errors->first('company_name') }}
                                                            </div> 
                                                            @endif                                
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Contact Phone No:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('phone_no',isset($itemData->phone_no) ? $itemData->phone_no : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter contact person phone no','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Contact Person Name :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('contact_person_name',isset($itemData->contact_person_name) ? $itemData->contact_person_name : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter contact person name','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="payment" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Incoterms:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('incoterms',isset($itemData->incoterms) ? $itemData->incoterms : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter incoterms','id'=>'incoterms','disabled'))!!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Milestone / Month:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('milestone',isset($itemData->milestone) ? $itemData->milestone : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter milestone / month','id'=>'milestone','disabled'))!!}
                                                        </div>
                                                    </div>
                                                    @if(!isset($itemData->auto_billing))
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Auto Billing:</label>
                                                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                                                            <label>Yes {!!Form::checkbox('auto_billing','0',false,array('id'=>'auto_billing','disabled'))!!}</label>
                                                        </div>
                                                    </div>
                                                    @else
                                                    @if($itemData->auto_billing == 1)
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Auto Billing:</label>
                                                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                                                            <label>Yes {!!Form::checkbox('auto_billing','1',true,array('id'=>'auto_billing','disabled'))!!}</label>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Auto Billing:</label>
                                                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                                                            <label>Yes {!!Form::checkbox('auto_billing','0',false,array('id'=>'auto_billing','disabled'))!!}</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Payment Card Details:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('payment_card',isset($itemData->payment_card) ? $itemData->payment_card : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter payment card details','disabled'))!!}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label p-l-3 line-n word-break wspace-n">Down Payment Amount / Item:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('down_payment',isset($itemData->down_payment) ? $itemData->down_payment : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter down payment amount','disabled'))!!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Invoicing Dates:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            <label class="input-group datepicker-only-init">
                                                                {!!Form::text('invoicing_dates',isset($itemData->invoicing_dates) ? $itemData->invoicing_dates : '',array('class'=>'form-control border-radius-0 datepicker-only-init','placeholder'=>'Please select invoicing date','disabled'))!!}
                                                                <span class="input-group-addon"> <i class="icmn-calendar"></i> </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Billing Block:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('billing_block',isset($itemData->billing_block) ? $itemData->billing_block : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter billing block','disabled'))!!}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label p-l-3 line-n word-break wspace-n">Billing Reminder Notification:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('billing_reminder',isset($itemData->billing_reminder) ? $itemData->billing_reminder : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter billing reminder notification','disabled'))!!}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">PayPal account details:</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('paypal',isset($itemData->paypal) ? $itemData->paypal : '',array('class'=>'form-control border-radius-0','placeholder'=>'Please enter paypal account details','disabled'))!!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="administration" role="tabpanel">
                                        <div class="padding-left-30 padding-right-30">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Created On* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('created_on',isset($sales_order->created_on) ? $sales_order->created_on : '',array('class'=>'form-control border-radius-0','disabled'))!!}  
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Changed On* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('changed_on',isset($sales_order->changed_on) ? $sales_order->changed_on : '',array('class'=>'form-control border-radius-0','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Created By* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('created_by',isset($created_by->name) ? $created_by->name : '',array('class'=>'form-control border-radius-0','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Changed By* :</label>
                                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                                            {!!Form::text('changed_by',isset($changed_by->name) ? $changed_by->name : '',array('class'=>'form-control border-radius-0','disabled'))!!}  
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form> 
                            <div class="ppm-tabpane">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#Approval" role="tab">Approval</a>
                                    </li>

                                </ul>

                                <!-- Tab panes -->
                                <form id="Approval">
                                    
                                    {!!Form::hidden('id',$id,array('class'=>'form-control'))!!}
                                    {!!Form::hidden('changed_by',$userid,array('class'=>'form-control'))!!}
                                    
                                    <div class="tab-content">
                                        <div class="tab-pane active"  role="tabpanel">
                                            <div class="padding-left-30 padding-right-30">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 control-label">Approver 1:</label>
                                                            <div class="col-sm-5">
                                                                {!!Form::select('approver_1',$requestedby,isset($sales_order->approver_1) ? $sales_order->approver_1 : '',array('class'=>'form-control select2','placeholder'=>'Please select Approver 1'))!!}
                                                            </div>
                                                            <span class="col-sm-4 padding-top-10"></span>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 control-label">Approver 3:</label>
                                                            <div class="col-sm-5">
                                                                {!!Form::select('approver_3',$requestedby,isset($sales_order->approver_3) ? $sales_order->approver_3 : '',array('class'=>'form-control select2','placeholder'=>'Please select Approver 3'))!!}

                                                            </div>
                                                            <span class="col-sm-4 padding-top-10"></span>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 control-label">Approver 2:</label>
                                                            <div class="col-sm-5">
                                                                {!!Form::select('approver_2',$requestedby,isset($sales_order->approver_2) ? $sales_order->approver_2 : '',array('class'=>'form-control select2','placeholder'=>'Please select Approver 2'))!!}
                                                            </div>
                                                            <span class="col-sm-4 padding-top-10"></span>

                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 control-label">Approver 4:</label>
                                                            <div class="col-sm-5">
                                                                {!!Form::select('approver_4',$requestedby,isset($sales_order->approver_4) ? $sales_order->approver_4 : '',array('class'=>'form-control select2','placeholder'=>'Please select Approver 4'))!!}
                                                            </div>
                                                            <span class="col-sm-4 padding-top-10"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <div class='error-message' style='display:none;'> </div>    
                                        <?php if (isset($token)) { ?>  
                                            {!! Form::button('Approve Request',array('class'=>'btn btn-primary width-250','id'=>'btn_save')) !!} 
                                            <a href="{{url('/admin/sales_order/reject/'.$id)}}" class="btn btn-danger">Reject Request</a>
                                        <?php } else { ?>
                                            {!! Form::button('Save and Send Approval Request',array('class'=>'btn btn-primary width-250','id'=>'btn_save')) !!}
                                            <a href="{{url('/admin/sales_order')}}" class="btn btn-default">Cancel</a>
                                        <?php } ?>
                                    </div>
                                </form>     
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
<!-- End Dashboard -->

<!-- Page Scripts -->
<script type="text/javascript">
            (function () {
            $('#btn_save').click(function () {
            $('#mask').show();
                    var data = $('#Approval').serializeArray();
                    var token = $('input[name^=_token]').val();
                    $.ajax({
                    url: "{{isset($token)?url('/admin/sales_order/approval/'.$id.'/'.$token):url('/admin/sales_order/approval/'.$id)}}",
                            method: "POST",
                            data: {'_token': token, data},
                            dataType: "JSON "
                    }).done(function (msg) {console.log(msg);
            if ('redirect_url' in msg)
            {
            $('#mask').hide();
                    window.location.href = location.origin + '/' + msg.redirect_url;
            }


            });
            });
            })();
            
        $.ajaxSetup({async:false});
        var task_ids = @php echo json_encode($tid); @endphp;
        var phase_ids = @php echo json_encode($phase_ids); @endphp;
        var project_ids = @php echo json_encode($pid); @endphp;
       
        // on radio button select 
        $('.special-radio').on('change', function(evt){
    var _item_number = $(this).val();
        $('select[name=phaseid]').html('');
        $('select[name=task]').html('');
        $('select[name=phaseid]').append('<option selected="selected" disabled="disabled" hidden="hidden" value="">Please select Phase Id</option>');
        $('select[name=task]').append('<option selected="selected" disabled="disabled" hidden="hidden" value="">Please select Task Id</option>');
        for (x in phase_ids){
    $('#phase').append('<option value="' + x + '" > ' + phase_ids[x] + '</option>');
    }
    for (x in task_ids){
    $('#task').append('<option value="' + x + '" > ' + task_ids[x] + '</option>');
    }

    document.querySelector('#Purchase_requisition_two').reset();
        $(_item_number + ' [type^=hidden]').each(
        function(i, ele){
        console.log(ele);
                if (document.querySelector('#Purchase_requisition_two [name=' + ele.name + ']') != null)
        {console.log(ele);
                $('#Purchase_requisition_two [name=' + ele.name + ']').val(ele.value);
                 $('#Purchase_requisition_two [name=' + ele.name + ']').val(ele.value);
                        if(ele.name == 'auto_billing')
                        {
                            if(ele.value=='1')
                            {
                                $('#auto_billing').attr('checked','checked');
                            }
                            else if(ele.value == '0'){

                                $('#auto_billing').removeAttr('checked');
                            }
                        }
        }
        else if (document.querySelector('#Purchase_requisition_three [name=' + ele.name + ']') != null)
        {console.log(ele);
                $('#Purchase_requisition_three [name=' + ele.name + ']').val(ele.value);
        }

        });
        $('#project,#phase,#task').selectpicker('refresh');
        $('#Purchase_requisition_two .select2').trigger('change.select2');
    });
        function extend(obj, src) {

        for (var key in src) {
        if (src.hasOwnProperty(key))
                obj[key] = src[key];
        }
        return obj;
        }
    $('#Purchase_requisition_two [name^=incoterms],#Purchase_requisition_two [name^=milestone],#Purchase_requisition_two [name^=billing_block],#Purchase_requisition_two [name^=auto_billing],#Purchase_requisition_two [name^=billing_reminder],#Purchase_requisition_two [name^=payment_card],#Purchase_requisition_two [name^=paypal],#Purchase_requisition_two [name^=down_payment],#Purchase_requisition_two [name^=project_id],#Purchase_requisition_two [name^=phaseid],#Purchase_requisition_two [name^=task], #Purchase_requisition_two [name^=cost_center],#Purchase_requisition_two [name^=processing_status],#Purchase_requisition_two [name^=customer],#Purchase_requisition_two [name^=company_name],#Purchase_requisition_two [name^=contact_person_name],#Purchase_requisition_two [name^=phone_no],#Purchase_requisition_two [name^=quotation_type],#Purchase_requisition_two [name^=sales_region],#Purchase_requisition_two [name^=short_description], #Purchase_requisition_two [name^=weight],#Purchase_requisition_two [name^=unit],#Purchase_requisition_two [name^=requested_by],#Purchase_requisition_two [name^=invoice_number]')
        .on('change', function(evt){

        var _ele_name = evt.target.name;
                var _item_number = $('[name^=optradio]:checked').val();
                console.log(_item_number);
                $(_item_number + ' [name^=' + _ele_name + ']').val(this.value);
        });
        $('#Purchase_requisition_two [name^=invoicing_dates]').on("dp.change", function(evt){

    var _ele_name = evt.target.name;
        var _item_number = $('[name^=optradio]:checked').val();
        console.log(_item_number);
        $(_item_number + ' [name^=' + _ele_name + ']').val(this.value);
    });
        $('#auto_billing').on('click', function()
    {
    if ($("#auto_billing").is(':checked'))
        $("#auto_billing").val('1');
        else
        $("#auto_billing").val('0');
    });

</script>
@endsection
