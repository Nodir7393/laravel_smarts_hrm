<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module HHParse</title>
</head>
<body>
<style>
    body {
        background-color: #f2f2f2;
        font-family: Arial, sans-serif;
    }

    .container {
        margin: auto;
        width: 400px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0px 0px 5px #ccc;
        border-radius: 5px;
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
    }

    input[type=text], input[type=password], input[type=email] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        background-color: #4CAF50;
        color: #fff;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background-color: #45a049;
    }

</style>
<div>
    <h2>Add hh user</h2>
    <form method="POST" action="{{ route('hh-profile') }}">
        @csrf

        <label for="name">Name</label>
        <input type="text" name="name">

        <label for="email">Email</label>
        <input type="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>

