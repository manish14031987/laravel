@extends('layout.adminlayout')
@section('title','Settings | SalesOrder Number Range')
@section('body')
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        {{ $error }}
        @endforeach
    </ul>
</div>
@endif
<div class="alert alert-danger message" style="display: none">
    <span class="glyphicon glyphicon-ok"></span>
    <em id="msg"></em>
</div>
<div class="alert alert-danger message1" style="display: none">
    <span class="glyphicon glyphicon-ok"></span>
    <em id="msg1"></em>
</div>
@if(Session::has('flash_message'))
<div class="alert alert-success">
    <span class="glyphicon glyphicon-ok"></span>
    <em> {!! session('flash_message') !!}</em>
</div>
@endif
{!! Html::script('/js/jquery.validate.min.js') !!}
<section class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                @include('include.admin_sidebar')
                <div class="margin-bottom-50">
                    <span style="margin-right: 10px;position: relative;top: -20px;">You are here :</span>
                    <ul class="list-unstyled breadcrumb breadcrumb-custom">
                        <li>
                            <a href="{{url('admin/dashboard')}}">Settings</a>
                        </li>
                        <li>
                            <span>SalesOrder Number Range</span>
                        </li>
                        <li>
                            <span>Edit SalesOrder Number Range</span>
                        </li>
                    </ul>
                </div>
                <h4>Edit SalesOrder Number Range</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin-bottom-50 margin-top-25">
                            <table class="tableizer-table table table-bordered text-center" border="1" style="width: 60%;">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">Start Range</th>
                                        <th rowspan="2" class="text-center vertical-top">End Range</th>
                                        <th rowspan="2" class="text-center vertical-top">Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    {!! Form::open(array('route'=>array('salesorderNumber.update',$salesorderNumber_range->id),'method' => 'put','id'=>'salesorderNumber')) !!}
                                    <tr id="editquotation_{{$salesorderNumber_range->id}}" class="range">
                                        <td class="startrange">
                                            {!!Form::text('start_range',$salesorderNumber_range->start_range,array('class'=>'form-control border-radius-0 startrange','id'=>'startrange'))!!}
                                        </td>
                                        <td class="endrange">
                                            {!!Form::text('end_range',$salesorderNumber_range->end_range,array('class'=>'form-control border-radius-0 endrange','id'=>'endrange'))!!}
                                        </td>
                                        <td> 
                                            {!! Form::submit('Save Changes',array('class'=>'btn btn-primary card-btn save')) !!}  
                                        </td>
                                    </tr>
                                    {!!Form::close()!!}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var id;
    $(document).ready(function () {
        $('[name=start_range],[name=end_range]').on('change', function () {
            if (!isNaN($(this).val()))
            {
                if (parseInt($(this).val()) < 0)
                {
                    $(this).val(-parseInt($(this).val()));
                }
            }
        });
        $('.save').click(function (e) {
            var start_range = $(this).parents('.range').find('input.startrange').val();
            var end_range = $(this).parents('.range').find('input.endrange').val();

            if (start_range == '')
            {
                $('.message').show();
                $('#msg').html("Please enter start range");
                $('#quotatioNnumber').valid();
                e.preventDefault();
            }
            if (end_range == '')
            {
                $('.message1').show();
                $('#msg1').html("Please enter end range");
                $('#quotatioNnumber').valid();
                e.preventDefault();
            }
        });
    });
</script>
@endsection