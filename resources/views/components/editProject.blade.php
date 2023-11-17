<!doctype html>
<html lang="en">
<head>
    <!--Required meta tags for Bootstrap-->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <!--Bootstrap CSS for styling-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"/>
</head>
<body class="container">
<br>
<br>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<!-- Script -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!--Script for initialize appendGrid-->

<form action="" method="post">
    {{ Aire::open() }}

    <div>
        {{ Aire::input('name', '')->value($project_name) }}
    </div>
    <div class="d-flex justify-content-between m-4">
        <div>
            <x-select :options=json_encode($channels) name="tasklist_channel" label="Tasklist channel" m='' w="60"></x-select>
            <x-select :options=json_encode($channels) name="internal_channel" label="Internal channel" m='' w="60"></x-select>
            <x-select :options=json_encode($channels) name="partners_channel" label="partners channel" m='' w="60"></x-select>
        </div>
        <div>
            <x-select :options=json_encode($groups) name="tasklist_group" label="Tasklist group" m='' w="60"></x-select>
            <x-select :options=json_encode($groups) name="internal_group" label="Internal group" m='' w="60"></x-select>
            <x-select :options=json_encode($groups) name="partners_group" label="partners group" m='' w="60"></x-select>
        </div>
    </div>
    <br>
    <br>
    <div>
        <x-select :options=json_encode($users) name="dev[]" label="Developers" m='multiple="multiple"' w="75"></x-select>
        <br>
        <x-select :options=json_encode($users) name="pm[]" label="PMS" m='multiple="multiple"' w="75"></x-select>
        <br>
        <x-select :options=json_encode($users) name="qa[]" label="QAS" m='multiple="multiple"' w="75"></x-select>
    </div>
    <button id="get" type="submit" class="btn btn-success">Send</button>
    {{ Aire::close() }}
</form>
<br>
<br>
<br>
</body>
</html>
