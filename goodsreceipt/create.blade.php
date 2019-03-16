@extends('layout.adminlayout')
@section('title','Create Goods Receipt')

@section('body')

<!-- Goods Receipt -->
{!! Html::script('/js/jquery.validate.min.js') !!}
{!! Html::script('/js/goodsreceipt_validation.js') !!}

<!-- Goods Receipt -->
<section id="create_form" class="panel">


    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="togle-btn pull-right">
                    <!--                    <div class="dropdown inner-drpdwn">
                                            <a href="javascript: void(0);" class="dropdown-toggle dropdown-inline-button" data-toggle="dropdown" aria-expanded="false">
                                                <span class="hidden-lg-down">Portfolio Management</span>
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="" role="menu">
                                                <a class="dropdown-item" href="{{url('admin/portfolio')}}">Portfolio</a>
                                                <a class="dropdown-item" href="{{url('admin/buckets')}}">Buckets</a>
                                                <a class="dropdown-item" href="javascript:void(0)">Portfolio Structure</a>
                                                <a class="dropdown-item" href="{{url('admin/bucketfp')}}">Portfolio Financial Plaining</a>
                                                <a class="dropdown-item" href="javascript:void(0)">Portfolio Resource Plaining</a>
                                            </ul>
                                        </div> -->
                </div>

                @if(!isset($goods_receipt->id))
                {!! Form::open(array('route' => 'goods_receipt.create','method'=>'post', 'id' => 'GoodsReceiptform')) !!} 
                @else
                {!! Form::open(array('route'=>array('goods_receipt.update',$goods_receipt->id),'method' => 'put','id' => 'GoodsReceiptform')) !!}
                @endif
                {{ csrf_field() }}
                <div class="margin-bottom-50">

                    <div class="margin-bottom-50">
                        <ul class="list-unstyled breadcrumb breadcrumb-custom">
                            <li>
                                You are here :    <a href="javascript: void(0);">Procurement</a>
                            </li>
                            <li>
                                <a href="{{url('/admin/goods_receipt')}}">Goods Receipt</a>
                            </li>
                            <li>
                                @if(isset($goods_receipt))
                                <span>Edit Goods Receipt</span>
                                @else
                                <span>Create Goods Receipt</span>
                                @endif    
                            </li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-header card-header-box bg-lightcyan">
                            <h4 class="margin-0">
                                @isset($goods_receipt_item)
                                Edit
                                @else
                                Create
                                @endisset
                                Goods Receipt</h4>
                        </div>
                        <div class="card-block">
                            <div class="row">
                                <div class="col-sm-12">
                                    @if(isset($goods_receipt)!=true)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Purchase Order Number*:</label>
                                        <div class="col-sm-3">
                                            <div class="form-input-icon">
                                                {!!Form::select('purchase_order_number',$purchase_no,isset($goods_receipt->purchase_order_number) ? $goods_receipt->purchase_order_number : '',array('class'=>'form-control border-radius-0 select2','id'=>'purchaseno','placeholder'=>'Please select purchase order number'))!!}
                                                @if($errors->has('purchase_order_number')) 
                                                <div style='color:red'>
                                                    {{ $errors->first('purchase_order_number') }}
                                                </div> 
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Item No:</label>
                                        <div class="col-sm-3">
                                            <div class="form-input-icon">
                                                {!!Form::select('item_no',[],isset($goods_receipt->isset_item_no) ? $goods_receipt->item_no : '',array('class'=>'form-control border-radius-0 select2','id'=>'itemno','placeholder'=>'Please select item no (optional)'))!!}
                                                @if($errors->has('item_no')) 
                                                <div style='color:red'>
                                                    {{ $errors->first('item_no') }}
                                                </div> 
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <table class="table table-list table-responsive margin-top-15" >
                                        <thead>
                                            <tr>

                                                <th>Item No.</th>
                                                <th>Item Description</th>                            
                                                <th>Vendor Number</th>
                                                <th>Vendor Name</th>
                                                <th>Purchase order quantity</th>
                                                <th>Quantity received</th>
                                                <th>Quantity remaining</th>
                                                <th>Delivery note</th>
                                                <th>Bill of lading</th>
                                                <th>Ok</th>
                                            </tr>
                                        </thead>
                                        <tbody id='purchase_item_form'>

                                            @isset($goods_receipt_item)
                                            @foreach($goods_receipt_item as $key=>$item)
                                            <tr id='purchase_item_{{$key}}' class = "form">

                                                <td>
                                                    {!!Form::text('purchase_order_item_no[]',$item->purchase_order_item_no,array('class'=>'form-control no-resize padding-input','placeholder'=>'Item No','readonly'))!!}
                                                </td>
                                                <td>
                                                    {!!Form::textarea('item_description[]',$item->item_description,array('class'=>'form-control no-resize resize-textarea border-radius-0','placeholder'=>'Item Description','readonly'))!!}
                                                </td>
                                                <td>     
                                                    {!!Form::text('vendor_number[]',$item->vendor_number,array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Please select Vendor','readonly'))!!}
                                                </td>
                                                <td>     
                                                    {!!Form::text('vendor_name[]',$item->vendor_name,array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Please select Vendor Name','readonly'))!!}
                                                </td>
                                                <td>
                                                    {!!Form::number('purchase_order_quantity[]',$item->purchase_order_quantity,array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Purchase Order Quantity','min'=>0,'readonly'))!!}
                                                </td>
                                                <td>
                                                    {!!Form::number('quantity_received[]',$item->quantity_received,array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Quantity Received','min'=>0,'onchange'=>"calculate(".$key.",event)"))!!}
                                                </td>
                                                <td>
                                                    {!!Form::text('quantity_remaining[] ',$item->quantity_remaining,array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Quantity Remaining','min'=>0,'readonly'))!!}
                                                </td>
                                                <td>
                                                    {!!Form::text('delivery_note[] ',$item->delivery_note,array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Enter Delivery note','min'=>0))!!}
                                                </td>
                                                <td>
                                                    {!!Form::text('bill_of_lading[] ',$item->bill_of_lading,array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Enter Bill of lading','min'=>0))!!}
                                                </td>
                                                <td>
                                                    {!!Form::checkbox('status[]', 1,($item->status==1)?true:null,array('class'=>'form-control border-radius-0 padding-input'))!!}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endisset

                                        </tbody>

                                    </table>



                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Document date:</label>
                                        <div class="col-sm-3">
                                            {!!Form::text('document_date',isset($goods_receipt->document_date)?$goods_receipt->document_date:date('Y-m-d'),array('class'=>'form-control border-radius-0 datepicker-only-init','placeholder'=>'Please select document date'))!!}
                                            @if($errors->has('document_date')) 
                                            <span class="text-danger">
                                                {{ $errors->first('document_date') }}
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Posting date:</label>
                                        <div class="col-sm-3">
                                            {!!Form::text('posting_date',isset($goods_receipt->posting_date)?$goods_receipt->posting_date:date('Y-m-d'),array('class'=>'form-control border-radius-0 datepicker-only-init','placeholder'=>'Please select posting date'))!!}
                                            @if($errors->has('posting_date')) 
                                            <span class="text-danger">
                                                {{ $errors->first('posting_date') }}
                                            </span> 
                                            @endif
                                        </div>
                                    </div>

                                </div>                                    
                            </div>
                        </div>
                    </div>
                    <div class="card-footer card-footer-box text-right">
                        {!!Form::submit('Submit',array('class'=>'btn btn-primary card-btn'))!!}
                        <a href="{{url('/admin/goods_receipt')}}" class="btn btn-danger">Cancel</a>
                    </div>
                </div>
                <!--End Vertical Form--> 
            </div>
            {!! Form::close() !!}
            <!-- dummy table -->
            <table class="table table-list table-responsive margin-top-15" style="display:none;">
                <thead>
                    <tr>
                        <th>Item No.</th>
                        <th>Item Description</th>                            
                        <th>Vendor Number</th>
                        <th>Vendor Name</th>
                        <th>Purchase order quantity</th>
                        <th>Quantity received</th>
                        <th>Quantity remaining</th>
                        <th>Delivery note</th>
                        <th>Bill of lading</th>
                        <th>Ok</th>

                    </tr>
                </thead>
                <tbody id='hidden_table'>

                    <tr id='purchase_hidden_row' class = "form">


                        <td>
                            {!!Form::text('purchase_order_item_no[]','',array('class'=>'form-control no-resize padding-input','placeholder'=>'Item No','readonly'))!!}
                        </td>
                        <td>
                            {!!Form::textarea('item_description[]','',array('class'=>'form-control no-resize resize-textarea border-radius-0','placeholder'=>'Item Description','readonly'))!!}
                        </td>
                        <td>     
                            {!!Form::text('vendor_number[]','',array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Please select Vendor','readonly'))!!}
                        </td>
                        <td>     
                            {!!Form::text('vendor_name[]','',array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Please select Vendor Name','readonly'))!!}
                        </td>
                        <td>
                            {!!Form::number('purchase_order_quantity[]','',array('class'=>'form-control border-radius-0 padding-input','placeholder'=>'Purchase Order Quantity','min'=>0,'readonly'))!!}
                        </td>
                        <td>
                            {!!Form::number('quantity_received[]','',array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Quantity Received','min'=>0))!!}  
                        </td>
                        <td>
                            {!!Form::text('quantity_remaining[] ','',array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Quantity Remaining','min'=>0,'readonly'))!!}
                        </td>
                        <td>
                            {!!Form::text('delivery_note[] ','',array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Enter Delivery note','min'=>0))!!}
                        </td>
                        <td>
                            {!!Form::text('bill_of_lading[] ','',array('class'=>'form-control border-radius-0 padding-input ','placeholder'=>'Enter Bill of lading','min'=>0))!!}
                        </td>
                        <td>
                            {!!Form::checkbox('status[]', 1, null,array('class'=>'form-control border-radius-0 padding-input'))!!}
                        </td>
                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>
</section><!--
<!-- End Dashboard -->

<!-- Page Scripts -->
<script type="text/javascript">

    $('#purchaseno').change(function () {
        var purchaseID = $(this).val();
        if (purchaseID) {
            getPurchaseitemList(purchaseID);
        }
    });

    $('#itemno').change(function () {
        var itemID = $(this).val();

        if (itemID) {
            getPurchaseitem(itemID);
        }
    });

    $('#purchaseno').select2({
    }).on('change', function () {
        $(this).valid();
    });
    $('#itemno').select2({
    }).on('change', function () {
        $(this).valid();
    });
    $('#vendorno').select2({
    }).on('change', function () {
        $(this).valid();
    });



    function getPurchaseitem(itemId) {
        if (itemId == 0)
        {
            itemId = $('#purchaseno').val();
            getPurchaseitemList(itemId);
            return;
        }
        var purchase_order = $('#purchaseno').val();
        var token = $('[name=_token]').val();
        $.ajax({
            type: "POST",
            url: "/admin/api/purchaseitem/" + purchase_order + "/" + itemId,
            data: {'_token': token},
            success: function (response) {

                $('#purchase_item_form').html('');

                if (response.status == true)
                {
                    $(response.results).each(function (i, data) {

                        var count = $('#purchase_item_form tr').length;
                        var row = $('#purchase_hidden_row').html();
                        $('#purchase_item_form').append('<tr id="purchase_item_' + count + '" class = "form">' + row + '</tr>');


                        $('#purchase_item_' + count + ' [name^=purchase_order_item_no]').val(data.item_no);
                        $('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val(data.item_quantity);
                        $('#purchase_item_' + count + ' [name^=item_description]').val(data.material_description);
                        $('#purchase_item_' + count + ' [name^=vendor_number]').val(data.vendor);
                        $('#purchase_item_' + count + ' [name^=vendor_name]').val(data.name);
                        $('#purchase_item_' + count + ' [name^=quantity_received]').val('');
                        $('#purchase_item_' + count + ' [name^=quantity_remaining]').val('');
                        $('#purchase_item_' + count + ' [name^=quantity_received]').on('change', function () {
                            if (parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) < parseInt(this.value))
                            {
                                this.value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val());
                                var value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) - parseInt(this.value);
                                $('#purchase_item_' + count + ' [name^=quantity_remaining]').val(value);

                            }
                            else
                            {
                                var value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) - parseInt(this.value);
                                $('#purchase_item_' + count + ' [name^=quantity_remaining]').val(value);
                            }
                        });
                    });

                }
                else if (response.status == 'msg')
                {
                    alert(response.results);
                }
            }
        });
    }
    function getPurchaseitemList(purchaseID) {

        $.ajax({
            type: "GET",
            url: "/admin/api/purchaseitems/" + purchaseID,
            success: function (response) {

                $('#purchase_item_form').html('');

                if (response.status == true)
                {
                    $(response.results).each(function (i, data) {

                        var count = $('#purchase_item_form tr').length;
                        var row = $('#purchase_hidden_row').html();
                        $('#purchase_item_form').append('<tr id="purchase_item_' + count + '" class = "form">' + row + '</tr>');


                        $('#purchase_item_' + count + ' [name^=purchase_order_item_no]').val(data.item_no);
                        $('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val(data.item_quantity_gr);
                        $('#purchase_item_' + count + ' [name^=item_description]').val(data.material_description);
                        $('#purchase_item_' + count + ' [name^=vendor_number]').val(data.vendor);
                        $('#purchase_item_' + count + ' [name^=vendor_name]').val(data.name);
                        $('#purchase_item_' + count + ' [name^=quantity_received]').val('');
                        $('#purchase_item_' + count + ' [name^=quantity_remaining]').val('');
                        $('#purchase_item_' + count + ' [name^=quantity_received]').on('change', function () {
                            if (parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) < parseInt(this.value))
                            {
                                this.value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val());
                                var value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) - parseInt(this.value);
                                $('#purchase_item_' + count + ' [name^=quantity_remaining]').val(value);

                            }
                            else
                            {
                                var value = parseInt($('#purchase_item_' + count + ' [name^=purchase_order_quantity]').val()) - parseInt(this.value);
                                $('#purchase_item_' + count + ' [name^=quantity_remaining]').val(value);
                            }
                        });
                    });
                    $('#itemno').html('');
                    $('#itemno').append('<option value selected="selected" disabled="disabled" "placeholder"="Please select item no (optional)" >Please select item no (optional)</option>');

                    $(response.item).each(function (i, data) {
                        for (x in data) {
                            $('#itemno').append('<option value="' + x + '"> ' + data[x] + '</option>');
                        }

                    });
                    $('#itemno').trigger('change');
                }
                else if (response.status == 'msg')
                {
                    alert(response.results);
                }
            }
        });
    }

    function calculate(index, evt)
    {
        if (parseInt(evt.target.value) > parseInt($('#purchase_item_' + index + ' [name^=purchase_order_quantity]').val()))
        {
            evt.target.value = parseInt($('#purchase_item_' + index + ' [name^=purchase_order_quantity]').val());
            var value = parseInt($('#purchase_item_' + index + ' [name^=purchase_order_quantity]').val()) - parseInt(evt.target.value);
            $('#purchase_item_' + index + ' [name^=quantity_remaining]').val(value);
        }
        else
        {
            var value = parseInt($('#purchase_item_' + index + ' [name^=purchase_order_quantity]').val()) - parseInt(evt.target.value);
            $('#purchase_item_' + index + ' [name^=quantity_remaining]').val(value);
        }
    }


    @if (Session::has('purchase_order'))
            (function () {

                $('#purchaseno').trigger('change');

            })();
            @endif


</script>

@endsection
