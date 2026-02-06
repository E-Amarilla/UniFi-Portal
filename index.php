<?php
session_start();

//Get the MAC addresses of AP and user
$_SESSION["id"] = $_GET["id"];
$_SESSION["ap"] = $_GET["ap"];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WiFi Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos embebidos -->
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .card svg {
            width: 150px;
            height: auto;
            margin-bottom: 30px;
        }

        .card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: #0078d4;
            box-shadow: 0 0 5px rgba(0, 120, 212, 0.3);
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #0078d4;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #005ea3;
        }

        p.welcome-text {
            margin-bottom: 30px;
            color: #333;
            font-size: 18px;
        }

    </style>
</head>
<body>
    <div class="card">
        <!-- Logo SVG centrado -->
        <?php include 'Creminox.svg'; ?>

        <p class="welcome-text">Welcome!<br>Please login to our Wifi service</p>

        <form method="post" action="connecting.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Insert Name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Insert Email">
            </div>
            <input type="submit" value="Sign up">
        </form>
    </div>
</body>
</html>
