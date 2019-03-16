@extends('layout.adminlayout')
@section('title','Sales Order')

@section('body')
<!-- Sales Order-->
{!! Html::script('/js/jquery.validate.min.js') !!}
{!! Html::script('/js/sales_order.js') !!}
<!-- Sales Order-->

<section id="create_form" class="panel">

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
                {!! Form::open(array('url' => 'admin/insertRefquotation','method'=>'post', 'id' => 'SalesOrderform')) !!} 
                <div class="margin-bottom-50">

                    <div class="margin-bottom-50">
                        <ul class="list-unstyled breadcrumb breadcrumb-custom">
                            <li>
                                You are here : <a href="{{url('/admin/sales_order')}}">Sales Order</a>
                            </li>
<!--                            <li>
                                <a href="{{url('/admin/sales_order')}}">Sales Order</a>
                            </li>-->
                            <li>
                                <span>Create Sales Order with Ref</span>
                            </li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-header card-header-box bg-lightcyan">
                            <h4 class="margin-0">Create Sales Order with Ref</h4>
                        </div>
                        <div class="card-block">
                            <div class="row">
                                <div class="col-sm-offset-2 col-sm-8 col-sm-offset-2">

                                    <div class="form-group row">
                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Sales Order Type* :</label>
                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                            <div class="form-input-icon">
                                                {!!Form::select('sales_order_type',array('Product Order'=>'Product Order','Service Order'=>'Service Order','Support Order'=>'Support Order','Project Order'=>'Project Order'),'',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select Sales Order Type','id'=>'salesorder_type'))!!}
                                                @if($errors->has('sales_order_type')) 
                                                <div style='color:red'>
                                                    {{ $errors->first('sales_order_type') }}
                                                </div> 
                                                @endif                                        
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Quotation* :</label>
                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                            <div class="form-input-icon">
                                                {!!Form::select('quotation',isset($quotation) ? $quotation : array('No quotation'),'',array('class'=>'form-control border-radius-0 select2','placeholder'=>'Please select Quotation','id'=>'quotation'))!!}
                                                @if($errors->has('quotation')) 
                                                <div style='color:red'>
                                                    {{ $errors->first('quotation') }}
                                                </div> 
                                                @endif                                    
                                            </div>
                                        </div>
                                    </div>

                                  <div class="form-group row">
                                        <label class="col-xs-12 col-sm-6 col-md-4 col-form-label">Copy :</label>
                                        <div class="col-xs-12 col-sm-6 col-md-8 col-lg-7">
                                            <div class="btn-group" data-toggle="buttons">
                                                
                                                <a class="active-bttn btn btn-primary active">
                                                    <!--Active-->
                                                    {!! Form::radio('status','yes','yes') !!}Yes

                                                </a>
                                                <a class="inactive-btn btn btn-default">
                                                    <!--Inactive-->
                                                    {!! Form::radio('status','no') !!}No

                                                </a>
                                             </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-footer card-footer-box text-right">
                            {!!Form::submit('Submit',array('class'=>'btn btn-primary card-btn'))!!}
                            <a href="{{url('/admin/sales_order')}}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    <!--End Vertical Form--> 
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section><!--
<!-- End Dashboard -->
<script type="text/javascript">

    $(document).ready(function () {


        $('#salesorder_type').select2({
        }).on('change', function () {
            $(this).valid();
        });
       
        $('#quotation').select2({
        }).on('change', function () {
            $(this).valid();
        });

    });


</script>
<!-- Page Scripts -->
@endsection

