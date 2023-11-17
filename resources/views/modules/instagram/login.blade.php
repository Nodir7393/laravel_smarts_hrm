@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        <i class="voyager-lock"></i> Login
    </h1>
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form" action="{{route('instagram.login')}}" method="POST"
              enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            <input type="hidden" name="_token" value="{{@csrf_token()}}">

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">


                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Username</label>
                                @error('username')
                                <div class="error">{{ $message }}</div>
                                @enderror
                                <input type="text" class="form-control" id="name" name="username" placeholder="Username"
                                       value="">
                            </div>

                            <div class="form-group">
                                <label for="email">Password</label>
                                @error('password')
                                <div class="error">{{ $message }}</div>
                                @enderror
                                <input type="text" class="form-control" id="email" name="password"
                                       placeholder="Password" value="">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right save">
                        Login
                    </button>
                </div>
            </div>
        </form>
    </div>
    <style>
        .error {
            color: red;
        }
    </style>
@endsection
