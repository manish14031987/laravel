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
            <div class="col-lg-10">

                <h4>Sales Order</h4>

                <br/>
                <div class="col-md-10">

                    <div class="margin-bottom-50">
                        <table class="table table-inverse" id="example3" width="100%">
                            @foreach($salesorder_data as $sales)
                            <tr>
                                <td>
                                    <div id="table-view_{{$sales->sales_orderno }}"  aria-labelledby="" aria-hidden="true">
                                        <div  role="document" style="text-align:left;">
                                            <div class="modal-content">
                                                <div >                                                                                                
                                                  
                                                    <!--view--> 
                                                    <div >
                                                        <ul class="list-unstyled breadcrumb">

                                                            <li>
                                                                <a href="{{url('/admin/sales_order')}}">Sales Order</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript: void(0);">Display Sales Order</a>
                                                            </li>
                                                            <li>
                                                                <span>{{$sales->sales_orderno}}</span>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </div>                                           

                                                    <form class="static-form">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">

                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Order Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_orderno }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Customer </p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->customer }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Region</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_region }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Purchase Order Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->purchase_order_number }}</p>
                                                                </div>
                                                            </div>
                                                        </div>   
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Purchase Order Date</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->purchase_order_date }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Requested delivery date</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->req_delivery_date }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Weight</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->weight }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Unit</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->unit }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Valid From</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->valid_from }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Inquiry Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->inquiry}}</p>
                                                                </div>
                                                            </div>   
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Quotation Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->quotation}}</p>
                                                                </div>
                                                            </div>   
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Sales Description</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->sales_description}}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Total value</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->total_value }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Net Amount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->net_amount }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Material Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->material_number }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Order Quantity</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->order_qty }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Customer Material Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->customer_material_number }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Cost Per Unit</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->cost_per_unit }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Total Amount</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->total_amount }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Purchase Order Item </p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->po_item }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Project Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->project_number }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Task</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->task }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Cost Center</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->cost_center }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Material Group</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->material_group }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Reason For Rejection</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->reason_for_rejection }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Requested by </p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->requested_by }}</p>
                                                                </div>
                                                            </div>   
                                                        </div>


                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Invoice number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->invoice_number }}</p>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Ex Works</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->ex_works }}</p>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Payment terms</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->payment_terms}}</p>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Billing Block</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->billing_block }}</p>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Created On</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->created_on }}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group ">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Created By</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$sales->created_by}}</p>
                                                                </div>
                                                            </div>     
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Status</p>
                                                                </div>

                                                                <div class="col-sm-5">
                                                                    @if($sales->status=='active')
                                                                    <img src="{{asset('vendors/common/img/green.png')}}" alt="">
                                                                    @else
                                                                    <img src="{{asset('vendors/common/img/red.png')}}" alt="">
                                                                    @endif    
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </form>

                                               

                                                <div class="modal-footer ">
                                                    <span class="model-footer-btn">                                                         
                                                        <a href="{{url('admin/customer_inquiry/')}}" class="btn btn-danger">Back</a>
                                                    </span>
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
@endsection
