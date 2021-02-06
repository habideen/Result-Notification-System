<?php
require_once('header.php');


$report = '';
$error = 'style="background-color:#ffeeee"';

$matric = $sname = $fname = $mname = $gender = $department = $phone = "";
$matric_error = $sname_error = $fname_error = $mname_error = $gender_error = $department_error = $phone_error = "";




//register user
if (isset($_POST['edit_single']) && is_post_request()) {
    $matric = $_POST['matric'];
    if(!preg_match('/^[a-zA-Z][\/][a-zA-Z]{2}[\/][0-9]{2}[\/][0-9]{4}$/', $matric))
        $matric_error = $error;


    $sname = $_POST['sname'];
    $sname = $conn->real_escape_string($sname);
    $sname = noSpace($sname);
    $sname = ucwords($sname);
    if(!preg_match('/^[a-zA-Z]{2,30}$/', $sname))
        $sname_error = $error;


    $fname = $_POST['fname'];
    $fname = $conn->real_escape_string($fname);
    $fname = noSpace($fname);
    $fname = ucwords($fname);
    if(!preg_match('/^[a-zA-Z]{2,30}$/', $fname))
        $fname_error = $error;


    $mname = $_POST['mname'];
    $mname = $conn->real_escape_string($mname);
    $mname = noSpace($mname);
    $mname = ucwords($mname);
    if ($mname!='') {
        if(!preg_match('/^[a-zA-Z]{2,30}$/', $mname))
            $mname_error = $error;
    }


    $gender = $_POST['gender'];
    $gender = $conn->real_escape_string($gender);
    $gender = noSpace($gender);
    if($gender!='M' && $gender!='F')
        $gender_error = $error;


    $department = $_POST['department'];
    $department = $conn->real_escape_string($department);    
    $department = space($department);
    if($department=='')
        $department_error = $error;


    $phone = $_POST['phone'];
    $phone = $conn->real_escape_string($phone);
    $phone = noSpace($phone);
    if ($phone!='') {
        if(!preg_match('/^[0][1-9][0-9]{9}$/', $phone))
            $phone_error = $error;
    }




    if ($matric_error!='' || $sname_error!='' || $fname_error!='' || $mname_error!='' || $gender_error!='' || $department_error!='' || $phone_error!='')
        $report = 'Error: Please check your input(s)';

    else {
        $sql = "UPDATE $dbname.student 
                SET
                    sname='$sname', 
                    fname='$fname', 
                    mname='$mname', 
                    gender='$gender', 
                    department='$department', 
                    phone='$phone'
                WHERE matric='$matric'";
        if (query($sql)){
            $report = 'Record edited';
            $matric = $sname = $fname = $mname = $gender = $department = $phone = "";
        }
        else
            $report = 'Error: Unable to connect to database!';
    }
}



else {
    $matric = $_GET['matric'];
    $matric = $conn->real_escape_string($matric);
    $sql = "SELECT matric, sname, fname, mname, 
                    gender, department, phone
            FROM $dbname.student WHERE matric='$matric'";
    $count = countRows($sql);

    if ($count < 1)
        header('Location: student');

    $fetch = getRow($sql);

    $matric = $fetch['matric'];
    $sname = $fetch['sname'];
    $fname = $fetch['fname'];
    $mname = $fetch['mname'];
    $gender = $fetch['gender'];
    $department = $fetch['department'];
    $phone = $fetch['phone'];

}

?>



        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Edit Student</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-body pt-4">
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="matric"><strong>Matric no.</strong></label>
                                                        <input class="form-control" type="text" name="matric" value="<?php echo $matric; ?>" <?php echo $matric_error; ?> required readonly pattern="^[a-zA-Z][/][a-zA-Z]{2}[/][0-9]{2}[/][0-9]{4}$">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="sname"><strong>Surname</strong></label>
                                                        <input class="form-control" type="text" name="sname" value="<?php echo $sname; ?>" <?php echo $sname_error; ?> required pattern="^[a-zA-Z]{3,}$">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="fname"><strong>Firstname</strong></label>
                                                        <input class="form-control" type="text" name="fname" value="<?php echo $fname; ?>" <?php echo $fname_error; ?> pattern="^[a-zA-Z]{3,}$" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="mname"><strong>Middle name</strong></label>
                                                        <input class="form-control" type="text" name="mname" value="<?php echo $mname; ?>" <?php echo $mname_error; ?> pattern="^[a-zA-Z]{3,}$">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="gender"><strong>Gender</strong></label>
                                                        <select class="form-control" id="gender" name="gender" <?php echo $gender_error; ?> required>
                                                            <option value=""></option>
                                                            <option value="M">Male</option>
                                                            <option value="F">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row mt-4">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="department"><strong>Department</strong></label>
                                                        <select class="form-control" id="department" name="department" <?php echo $department_error; ?> required>
                                                            <option value=''></option>
<?php
$department_ = getRow("SELECT department 
                   FROM $dbname.department WHERE sn='1'")['department'];
$department_ = explode('##', $department_);
foreach ($department_ as $value)
    echo "<option value='$value'>$value</option>";
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group"><label for="phone"><strong>Phone no.</strong></label><input class="form-control" type="text" name="phone" value="<?php echo $phone; ?>" <?php echo $phone_error; ?> pattern="^[0][1-9][0-9]{9}$" required></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-success" type="submit" name="edit_single">Edit student</button>
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
const gender_control = document.getElementById('gender');
const department_control = document.getElementById('department');


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

InputIndex(<?php echo "'$gender'" ?>,  gender_control);
InputIndex(<?php echo "'$department'" ?>,  department_control);

</script>