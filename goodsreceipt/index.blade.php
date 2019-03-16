@extends('layout.adminlayout')
@section('title','Goods Receipt')

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
                <div class="margin-bottom-50">
                    <ul class="list-unstyled breadcrumb breadcrumb-custom">
                        <li>
                            You are here :   <a href="javascript: void(0);">Procurement</a>
                        </li>
                        <li>
                            <span>Goods Receipt Dashboard</span>
                        </li>
                    </ul>
                </div>
                <h4>Goods Receipt</h4>
                <div class="dashboard-buttons">
                    <a href="{{url('admin/goods_receipt/create')}}" class="btn btn-primary">
                        <i class="fa fa-send margin-right-5"></i>
                        Create Goods Receipt
                    </a>

                </div>


                <br />
                <div class="col-md-12">
                    <div class="margin-bottom-50 display-block padding-top-10">
                        <table class="table table-inverse" id="example3" width="100%">
                            <thead>
                                <tr>

                                    <th>Material Document No</th>
                                    <th>Purchase Order No</th>                                                                    
                                    <th>Created On</th>                                    
                                    <th>Created By</th>
                                    <th>Active State</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($goods_receipt as $bill)
                                <tr>
                                    <td><a data-toggle="modal" data-target="#table-view-popup_{{$bill->id }}">{{$bill->id}}</a></td>
                                    <td>{{$bill->purchase_order_number}}</td>
                                    <td>{{$bill->created_on}}</td>
                                    <td>{{$createdby[$loop->index]}}</td>
                                    <td>
                                        @if($bill->reversed=='')
                                        <img src="{{asset('vendors/common/img/green.png')}}" alt="">
                                        @else
                                        <img src="{{asset('vendors/common/img/red.png')}}" alt="">
                                        @endif
                                    </td>

                                    <td class="action-btn">
                                        <a href="#" class="btn btn-info btn-xs margin-right-1" data-toggle="modal" data-target="#table-view-popup_{{$bill->id}}"><i class="fa fa-eye" aria-hidden="true"></i> <!--view--> </a>
                                        {!! Form::open(array('route' => array('goods_receipt.reversal',$bill->id), 'method' => 'DELETE','id'=>'delform'.$bill->id)) !!}
                                        @if($bill->reversed=='')
                                        <a href="javascript:void(0)" onclick="{var res = confirm('Proceeding further will reverse the posting of data and  delete this Receipt.');
                                                    if (res == true)document.getElementById('delform{{$bill->id}}').submit()
                                                                }" class="btn btn-danger btn-xs"><i class="fa fa-undo"></i> <!--Delete--> </a>
                                        @else
                                        <a href="javascript:void(0)" onclick="{var res = confirm('This Bill Contract is in reversed state, cannot be reversed further .');}
                                           " class="btn btn-danger btn-xs"><i class="fa fa-undo"></i> <!--Delete--> </a>
                                        @endif                        
                                        {!! Form::close() !!}
                                        <div class="modal fade table-view-popup" id="table-view-popup_{{$bill->id}}" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
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
                                                                    <a href="javascript: void(0);">Procurement</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{url('/admin/goods_receipt')}}">Goods Receipt</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript: void(0);">Receipt Number</a>
                                                                </li>
                                                                <li>
                                                                    <span>{{$bill->id}}</span>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="static-form">
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Goods Receipt Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$bill->id}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Purchase Order Number</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$bill->purchase_order_number}}</p>
                                                                </div>
                                                            </div>


                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Document Date</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$bill->document_date}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Posting Date</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$bill->posting_date}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Created By</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$createdby[$loop->index]}}</p>
                                                                </div>
                                                            </div>

                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Created On</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">{{$bill->created_on}}</p>
                                                                </div>
                                                            </div> 
                                                            <div class="form-group popup-brd-btm">
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">Active State</p>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <p class="form-control-static">
                                                                        @if($bill->reversed!='')
                                                                        Reversed
                                                                        @else
                                                                        Active
                                                                        @endif
                                                                        
                                                                    </p>
                                                                </div>
                                                            </div> 

                                                        </form>
                                                    </div>
                                                    <!--view-->
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                        </div>
                                        <!-- End  -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>

                                    <th>Material Document No</th>
                                    <th>Purchase Order No</th>                                                                    
                                    <th>Created On</th>                                    
                                    <th>Created By</th>
                                    <th>Active State</th>
                                    <th>Action</th>

                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection