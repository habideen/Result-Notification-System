<?php
header_remove("X-Powered-By"); 
header('Server: bravytech');

require_once('core/core.inc.php');
require_once('core/func.php');


if (isset($_SESSION['control'])) {
    //header('Location: home');
}




$report = '';
$email = $password = '';


//register user
if (isset($_POST['signin']) && is_post_request()) {
    $email = $_POST['email'];
    $email = $conn->real_escape_string($email);
    $email = noSpace($email);
    $email = strtolower($email);

    $password = $_POST['password'];

    if ($email == '' || $password == '')
        $report = 'Username/password in incorrect!';
    else {
        $sql = "SELECT password, status
                FROM master 
                WHERE email='$email'";
        
        

        $count = countRows($sql);

        if ($count != 1)
            $report = 'Username/password incorrect!' ;
        else {
            $fetch = getRow($sql);
            $password_hash = $fetch['password'];
            $status = $fetch['status'];


            if ( !password_verify($password, $password_hash) ) 
                $report = 'Username/password incorrect!';

            elseif ($status == '0')
                $report = 'Account on suspension!';

            else {
                $_SESSION['control'] = $email;
                header('Location: result');
            }//redirect user
        }//user count is 1
    }//user supplied values
}




?>





<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - Federal Polytechnic, Ilaro</title>
    <meta name="description" content="Our result notification system is designed to give students a quick notification of their result.">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="icon" href="assets/img/logo.png?h=h=582a279a0820df9f6e6a8cfa09dc36qqw" type="image/x-icon">
</head>

<body class="bg-gradient-success">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-12 col-xl-10">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-flex">
                                <div class="flex-grow-1 bg-login-image" style="background-image: url(&quot;assets/img/exam_image.png&quot;);"></div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h3 class="text-success mb-4"><strong>Federal Poly, Ire</strong></h3>
                                        <h4 class="text-dark mb-4">Login</h4>
                                    </div>
                                    <div class="text-danger" style="font-size:0.9em;">
                                        <?php echo $report; ?>
                                    </div>
                                    <form method="post" action="" class="user">
                                        <div class="form-group">
                                            <input class="form-control form-control-user" type="email" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter Email Address..." value="admin@gmail.com" name="email">
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control form-control-user" type="password" id="exampleInputPassword" placeholder="Password" value="11111" name="password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <div class="form-check">
                                                    <input class="form-check-input custom-control-input" type="checkbox" id="formCheck-1">
                                                    <label class="form-check-label custom-control-label" for="formCheck-1">Remember Me</label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-success btn-block text-white btn-user" type="submit" name="signin">Login</button>
                                    </form>
                                    <div class="text-center"></div>
                                    <div class="text-center"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="assets/js/script.min.js"></script>
</body>

</html>