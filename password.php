<?php
require_once('header.php');


$report = '';


$current_error = $new1_error = $new2_error = "";
//update user information 
if (isset($_POST['update'])) {
    $current = $_POST['current'];

    $new1 = $_POST['new1'];
    $new2 = $_POST['new2'];

    $sql = "SELECT password FROM master WHERE email='$useremail'";
    $count = countRows($sql);
    $row = getRow($sql);
    $old = $row['password'];

    if ( !password_verify($current, $old) )
        $report = 'Error: Incorrect current password!';

    elseif ( password_verify($new1, $old) )
        $report = 'Error: You cannot use your old password!';

    elseif (strlen($new1) < 5)
        $report = 'Error: New password length should is less than 5!';

    elseif ($new1 != $new2) 
        $report = 'Error: New password does not match each other!';

    else {
        $password = password_hash($new1, PASSWORD_BCRYPT, ['cost' => 10]);
        $sql = "UPDATE master
                SET password='$password'
                WHERE email='$useremail'";
        if ( query($sql) ) 
            $report = 'Password updated';
        else
            $report = 'Error: Not updated';
    }
}


?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Change password</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-body">
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12">
                                                    <div class="form-group"><label>Current password:</label>
                                                        <input class="form-control" type="password" style="max-width: 350px;" name="current">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group mt-4"><label>New password:</label>
                                                        <input class="form-control" type="password" style="max-width: 350px;" name="new1">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group"><label>Confirm password:</label>
                                                        <input class="form-control" type="password" style="max-width: 350px;" name="new2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-success" type="submit" name="update">Reset password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<?php require_once('footer.php'); ?>