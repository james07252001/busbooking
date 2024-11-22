<?php
    session_start();
    include 'dbconfig.php';

    if(isset($_SESSION["userId"])){
        header("location: index.php");
        exit;
    }

    // Redirect from .php URLs to remove the extension
    $request = $_SERVER['REQUEST_URI'];
    if (substr($request, -4) == '.php') {
        $new_url = substr($request, 0, -4);
        header("Location: $new_url", true, 301);
        exit();
    }
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="icon" href="../assets/img/bus.ico" type="image/ico">
    <title>Bantayan Online Bus Reservation</title>
</head>

<body>
    <div style="width: 100vw; height: 100vh" class="bg-light">
        <div class="h-100 d-flex flex-column justify-content-center align-items-center">
            <div class="container">
                <div class="w-100 m-auto" style="max-width: 500px">
                    <h1 class="text-center mb-5">Bantayan Online Bus Reservation</h1>
                    <div class="bg-white rounded shadow p-3">
                        <div class="text-center mb-5">
                            <img class="img-fluid" alt="login" src="../assets/img/forgotPw.png" style="width: 300px" />
                            <h4>Reset Password</h4>
                        </div>

                        <?php
                        if(isset($_GET["reset"])){
                            if($_GET["reset"] == "success"){
                                ?>
                        <div class="alert alert-success" role="alert">
                            Reset password link has been sent. Please check your email.
                        </div>
                        <?php
                            }else if($_GET["reset"] == "emailNotExist"){
                                ?>
                        <div class="alert alert-danger" role="alert">
                            You are not register yet. Please contact system admin.
                        </div>
                        <?php
                            }
                        }
                        ?>

                        <form method="POST" action="backend/reset-request.php">
                            <div class="form mb-3">
                                <input type="email" class="form__input" id="email" name="email" placeholder=" "
                                    required />
                                <label for="email" class="form__label">Email address</label>
                            </div>
                            <button type="submit" class="btn btn-block btn-primary" name="reset-request-submit">Request
                                reset password</button>

                            <div class="text-center">
                                <a href="login.php" class="btn btn-link">Log in instead.</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/jquery.dataTables.min.js"></script>

    <script>
    $("#login_form").submit(function(event) {
        event.preventDefault();
        var data = $("#login_form").serialize();
        console.log(data)

        $.ajax({
            data: data,
            type: "post",
            url: "backend/user.php",
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult.statusCode == 200) {
                    alert("Login successfully!");
                    window.location.replace("index.php")
                } else {
                    alert(dataResult.title);
                }
            },
        });
    });
    </script>
</body>

</html>