@include('layouts.datatables')

<table id="tblAppendGrid"></table>

<script src="https://cdn.jsdelivr.net/npm/jquery.appendgrid@2.0.0/dist/AppendGrid.min.js"></script>

<x-laravelAppendGrid url="/adduser/UserUZMobile/dev" ctrlOptions='@foreach ($users as $key =>  $elem)
{{$key}}: "{{ $elem }}",
@endforeach' name="users" display="Users"></x-laravelAppendGrid>

