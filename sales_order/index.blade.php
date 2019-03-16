@extends('layout.adminlayout')
@section('title','Sales Order')
@section('body')
@if(Session::has('flash_message'))
<div class="alert alert-success">
    <span class="glyphicon glyphicon-ok"></span>
    <em> {!! session('flash_message') !!}</em>
</div>
@endif
<section class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="togle-btn pull-right">
                    <div class="dropdown inner-drpdwn">
                        <a href="javascript: void(0);" class="dropdown-toggle dropdown-inline-button" data-toggle="dropdown" aria-expanded="false">
                            <span class="hidden-lg-down"> Sales Order</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="" role="menu">
                            <a class="dropdown-item" href="{{url('admin/customer_master')}}">Customer Master</a>
                            <a class="dropdown-item" href="{{url('admin/customer_inquiry')}}">Customer Inquiry</a>
                            <a class="dropdown-item" href="{{url('admin/quotation')}}">Quotation</a>
                            <a class="dropdown-item" href="{{url('admin/sales_order')}}">Sales Order</a>                           
                            <a class="dropdown-item" href="{{url('admin/sales_billing')}}">Billing</a>                           
                        </ul>
                    </div> 
                </div>
                <div class="margin-bottom-50">
                    <ul class="list-unstyled breadcrumb breadcrumb-custom">
                        <li>
                            You are here :   <a href="javascript: void(0);">Sales Order</a>
                        </li>
                        <li>
                            <span>Sales Order Dashboard</span>
                        </li>
                    </ul>
                </div>
                <h4>Sales Order</h4>
                <div class="dashboard-buttons">
                    <a href="{{url('admin/sales_order/create')}}"  class="btn btn-primary margin-left-10">
                        <i class="fa fa-send margin-right-5"></i>
                        Create Sales Order
                    </a>
                    <a href="{{url('admin/refquotation')}}"  class="btn btn-primary">
                        <i class="fa fa-send margin-right-5"></i>
                        Create Sales Order with Ref
                    </a>
                </div>
                <br/>
                <div class="col-md-12">
                    <div class="margin-bottom-50">
                        <table class="table table-inverse" id="example3" width="100%">
                            <thead>
                                <tr>
                                    <th>Sales Order Number</th>
                                    <th>Sales Order Short Description</th>
                                    <th>Quotation Number</th>
                                    <th>Inquiry Number</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sales Order Number</th>
                                    <th>Sales Order Short Description</th>
                                    <th>Quotation Number</th>
                                    <th>Inquiry Number</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($salesorder_data as $sales)
                                <tr>
                                    <td><a data-toggle="modal" data-target="#table-view-popup_{{$sales->id}}">{{$sales->salesorder_number}}</a></td>
                                    <td>{{$sales->salesorder_description}}</td>
                                    <td>{{$sales->quotation}}</td>
                                    <td>{{$sales->inquiry}}</td>
                                    <td>{{$sales->name}}</td>
                                    <td>
                                        @if($sales->approved_indicator=='approved')
                                        <img src="{{asset('vendors/common/img/green.png')}}" alt="">
                                        @elseif($sales->approved_indicator=='rejected'||$sales->approved_indicator=='') 
                                        <img src="{{asset('vendors/common/img/red.png')}}" alt="">
                                        @else 
                                        <img src="{{asset('vendors/common/img/yellow.png')}}" alt="">
                                        @endif

                                    </td>
                                    <td class="action-btn">
                                        <a href="#" class="btn btn-info btn-xs margin-right-1" data-toggle="modal" data-target="#table-view-popup_{{$sales->id }}"><i class="fa fa-eye" aria-hidden="true"></i> <!--view--> </a>
                                        <a href="{{url('admin/sales_order/'.$sales->id.'/edit')}}" class="btn btn-info btn-xs margin-right-1"><i class="fa fa-pencil"></i> <!--Edit--> </a>

                                        {!! Form::open(array('route' => array('salesorder.delete',$sales->id), 'method' => 'DELETE','id'=>'delform'.$sales->id)) !!}
                                        <a href="javascript:void(0)" onclick="var res = confirm('Are you sure you want to delete this sales order?');
                                                  if (res) {
                                          document.getElementById('delform{{$sales->id}}').submit()
                                                    }" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <!--Delete--> </a>
                                        {!! Form::close() !!}
                                        <div class="modal fade table-view-popup" id="table-view-popup_{{$sales->id }}" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                                            <div class="modal-dialog" role="document" style="text-align:left;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <!--view--> 
                                                        <div class="margin-bottom-10">
                                                            <ul class="list-unstyled breadcrumb">
                                                                <li>
                                                                    <a href="javascript: void(0);">Sales Order</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{url('/admin/sales_order')}}">Sales Order</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript: void(0);">Display Sales Order</a>
                                                                </li>
                                                                <li>
                                                                    <span>{{$sales->salesorder_number}}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="static-form">
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Order Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_number}}</p>
                                                                </div>
                                                            </div>   
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Description</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_description}}</p>
                                                                </div>
                                                            </div>   
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Gross price</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_gross_price}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Profit Margin</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_profit_margin}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Profit Margin Amount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_profit_amt}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Profit Margin Gross Price</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_profit_margin_grossprice}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Discount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_discount}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Discount Amount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_discount_amt}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Discount Gross Price</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_discount_gross_price}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Tax Amount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_sales_taxamt}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Net Price</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_net_price}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Freight Charges</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_freight_charges}}</p>
                                                                </div>
                                                            </div> 
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Total price</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->salesorder_total_price}}</p>
                                                                </div>
                                                            </div> 
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Customer</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->customer_id}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Customer Name</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->customer_name}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Organization</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_organization}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Region</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_region}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Order Type</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_order_type}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Billing Type</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->billing_type}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Approver 1</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">
                                                                        {{ isset($approver[$loop->index][0])?$approver[$loop->index][0]:'' }}
                                                                    </p>    
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Approver 2</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">
                                                                        {{ isset($approver[$loop->index][1])?$approver[$loop->index][1]:'' }}
                                                                    </p>    
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Approver 3</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">
                                                                        {{ isset($approver[$loop->index][2])?$approver[$loop->index][2]:'' }}
                                                                    </p>    
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Approver 4</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">
                                                                        {{ isset($approver[$loop->index][3])?$approver[$loop->index][3]:'' }}
                                                                    </p>    
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Approved Indicator</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    @if($sales->approved_indicator=='approved')
                                                                    <img src="{{asset('vendors/common/img/green.png')}}" alt="">
                                                                    @elseif($sales->approved_indicator=='rejected'||$sales->approved_indicator=='') 
                                                                    <img src="{{asset('vendors/common/img/red.png')}}" alt="">
                                                                    @else 
                                                                    <img src="{{asset('vendors/common/img/yellow.png')}}" alt="">
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <span class="model-button display-inline-block vertical-align-middle" id="ab"><a href="{{url('admin/sales_order/'.$sales->id.'/edit')}}" class="btn btn-primary" style="margin-left: 0;">Edit</a></span>
                                                        <span class="model-button vertical-align-middle" id="back" style="display: none;"><a href="{{url('admin/customer_inquiry')}}"  class="btn btn-danger">Back</a></span>
                                                        <span class=" purchase-btn display-inline-block vertical-align-middle" ><a href="{{url('admin/sales_order/'.$sales->id.'/show')}}" class="btn btn-warning">Edit Approval Details</a></span>
                                                        <button type="button" id="aa" class="btn btn-danger display-inline-block vertical-align-middle margin-left-50" data-dismiss="modal">Close</button>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End  -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
          @isset($id)
          $(document).ready(function() {
  $('#table-view-popup_{{$id}}').modal('show');
          $('#back').show();
          $('#ab').hide();
          $('#ba').hide();
          $('#aa').hide();
//    $('#table-view-popup_{{$id}}').append('<a href="' + document.referrer + '"></a>');
  });
          @endisset
</script>  


@endsection
