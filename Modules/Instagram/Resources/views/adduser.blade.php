<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module Instagram</title>
</head>
<body>
<style>
    .error {
        color: red;
    }


</style>
<h1 class="page-title">
    <i class="voyager-person"></i> Add User
</h1>
<div class="page-content container-fluid">
    <form class="form-edit-add" role="form" action="{{route('add-users')}}" method="GET">
        <!-- PUT Method if we are editing -->
        <input type="hidden" name="_token" value="{{@csrf_token()}}">

        <div class="row">
            <div class="">
                <div class="panel panel-bordered">


                    <div class="panel-body">
                        <div class="form-group">
                            <label for="name">Username</label>
                            @error('username')
                            <div class="error">{{ $message }}</div>
                            @enderror
                            <input type="text" class="form-control" id="name" name="username" placeholder="Username" value="">
                        </div>

                        {{--<div class="form-group">
                            <input type="checkbox" class="form-check-input" id="login" name="login">
                            <label class="form-check-label" for="login">Login</label>
                            @error('password')
                            <div class="error">{{ $message }}</div>
                            @enderror
                        </div>--}}

                        @error('login_error')
                        <div class="error">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary pull-right save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
