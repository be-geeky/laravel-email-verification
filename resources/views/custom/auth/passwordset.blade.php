@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Password Set Confirmation</div>

                <div class="panel-body">

                    You have successfully set your password. Click here to <a href="{{ url('/login') }}">login</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection