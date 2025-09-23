@if(count($errors)>0)

@foreach($errors as $error)

<div class="alert alert-danger alert-dismissible">

    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

    <i class="fa  fa-info" style="margin-right:0.5em;"></i>

    {{$error}}

</div>

@endforeach
@if(!is_array($errors))
@foreach($errors->all() as $error)

<div class="alert alert-danger alert-dismissible">

    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

    <i class="fa  fa-info" style="margin-right:0.5em;"></i>

    {{$error}}

</div>

@endforeach
@endif

@endif



@if(session('success'))

<div class="alert alert-success alert-dismissible">

    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

    <i class="fa  fa-info" style="margin-right:0.5em;"></i>

    {{session('success')}}

</div>

@endif





@if(session('error'))

<div class="alert alert-danger alert-dismissible">

    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

    <i class="fa  fa-info" style="margin-right:0.5em;"></i>

    {{session('error')}}

</div>

@endif