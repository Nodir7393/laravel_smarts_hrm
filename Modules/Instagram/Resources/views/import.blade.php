@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        <i class="voyager-download"></i> Import user names
    </h1>
    <div class="page-content container-fluid">
        <div class="page-content container-fluid">
            <form class="form-edit-add" role="form" action="{{route('instagram.import')}}" method="POST"
                  enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="_token" value="{{@csrf_token()}}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel panel-bordered panel-warning">
                            <div class="panel-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @endif
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <input type="file" data-name="avatar" name="file">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right save">
                            Import
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection
