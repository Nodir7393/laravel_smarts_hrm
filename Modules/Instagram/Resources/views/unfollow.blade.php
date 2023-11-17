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

<form method="post" action={{ route('unfollowPost') }}>
    {{ Aire::open() }}
    <x-select :options=json_encode($bots) name="bot" label="Bot" m='' w="75"></x-select>
    Number of users to unfollow <input type="number" name="number" min="1"><br><br>
    <button id="get" type="submit" class="btn btn-success">Send</button>
    {{ Aire::close() }}
</form>

</body>
</html>
