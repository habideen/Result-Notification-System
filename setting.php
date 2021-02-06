<?php
require_once('header.php');


$report = $report_session = '';
$error = 'style="background-color:#ffeeee"';

$session = '';
$department_error = $session_error = '';


$department = getRow("SELECT department 
                        FROM $dbname.department WHERE sn='1'")['department'];


//register user
if (isset($_POST['departmentBtn']) && is_post_request()) {
    $department = $_POST['department'];
    $department = $conn->real_escape_string($department);
    $department = space($department);
    $department = ucwords($department);

    $department = str_replace(' #', '#', $department);
    $department = str_replace('# ', '#', $department);


    $len = strlen($department);
    if ($len < 10)
        $report = 'Error: Department name is too short!';

    else {
        for ($x=0; $x<$len;) {
            if (strpos($department, '###')<1)
                break;

            $x = strpos($department, '###')-1;
            $department = str_replace('###', '##', $department);
        }

        if (substr($department, strlen($department)-1)=='##')
            $department = substr($department, 0, strlen($department)-1);
        if (substr($department, 0, 1)=='##')
            $department = substr($department, 1);


        if(!preg_match('/^[a-zA-Z][a-zA-Z0-9 #\/\-]{10,1500}[a-zA-Z]$/', $department))
            $report = 'Error: Invalid character entered! Make sure you don\'t # at both ends.';

        else {
            $sql = "UPDATE $dbname.department
                    SET 
                        department='$department' 
                    WHERE sn='1'";
            if (query($sql)){
                $report = 'Record edited successfully';
                $code = $title = '';
            }
            else
                $report = 'Error: Unable to connect to database!';
        }//reg matched
    } //length is greater than 10
}




//register user
if (isset($_POST['sessionBtn']) && is_post_request()) {
    $session = $_POST['session'];
    $session = $conn->real_escape_string($session);
    $session = space($session);
    $session = ucwords($session);

    if ($session=='')
        $report_session = 'Error: Please select a session';

    else {
        $sql = "UPDATE $dbname.department
                SET 
                    session='$session' 
                WHERE sn='1'";
        if (query($sql)){
            $report_session = 'Record edited successfully';
            $session = '';
        }
        else
            $report_session = 'Error: Unable to connect to database!';
    }

}

?>


        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Settings</h3>
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
                                                    <div class="form-group">
                                                        <label for="title"><strong>Department</strong></label>
                                                        <textarea class="form-control" name="department" rows="10" maxlength="1470"><?php echo $department; ?></textarea>
                                                        <label for="title"><br><br>Separate department by ##<br></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-success mr-4" type="submit" name="departmentBtn">Edit department</button>

                                                <button class="btn btn-dark ml-2" type="submit" name="departmentBtn" onclick="displayDepartment();">Show department</button>
                                            </div>
                                        </form>

                                        <div id="department" class="mt-4" style="display:none;">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="border-top-style: none;">Department list</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
<?php
$department_ = getRow("SELECT department 
                   FROM $dbname.department WHERE sn='1'")['department'];
$department_ = explode('##', $department_);
foreach ($department_ as $value)
    echo '<tr>
            <td>'.$value.'</td>
        </tr>';
?>                                            
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-body">
                                        <p class="mb-4">
<?php
$session_ = getRow("SELECT session 
    FROM $dbname.department WHERE sn='1'")['session'];
echo "<b>Present session</b>:&nbsp;&nbsp;&nbsp;{$session_}";
?>
                                        </p>
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report_session; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12 col-sm-5 col-md-4">
                                                    <div class="form-group">
                                                        <label for="department">Session</label>
                                                        <select class="form-control" id="session" name="session" <?php echo $session_error; ?> required>
                                                            <option value=''></option>
<?php
    $date = (int)date('Y')-3;
    $end = $date+3;
    while ($date++ <= $end) {
        $session_ = $date++ .'/'.$date--;
        echo "<option value='{$session_}'>{$session_}</option>";
    }
?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-success mr-4" type="submit" name="sessionBtn">Edit department</button>
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



<script type="text/javascript">
const department_div = document.getElementById('department');
department_visible = false;

function displayDepartment() {
    event.preventDefault();
    if (department_visible===false) {
        $(department_div).fadeIn();
        department_visible = true;
    }
    else {
        $(department_div).fadeOut();
        department_visible = false;
    }
}





const session_control = document.getElementById('session');


//auto activate select input option
function InputIndex(_val, _control) {
    var _val = _val;
    var _sel = _control;
    var opts = _sel.options;
    for (var opt, j = 0; opt = opts[j]; j++) {
        if (opt.value == _val) {
            _sel.selectedIndex = j;
            break;
        }
    }
}

InputIndex(<?php echo "'$session'" ?>,  session_control);
</script>