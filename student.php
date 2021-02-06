<?php
$student_active = '1';
require_once('header.php');
require_once 'php-excel-reader/excel_reader2.php';


$report = $report_file = '';
$error = 'style="background-color:#ffeeee"';

$matric = $sname = $fname = $mname = $gender = $department = $phone = "";
$matric_error = $sname_error = $fname_error = $mname_error = $gender_error = $department_error = $phone_error = "";


//register user
if (isset($_POST['register_single']) && is_post_request()) {
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



    $matric_ = getRow("SELECT COUNT(matric) AS num 
                   FROM $dbname.student WHERE matric='$matric'")['num'];


    if ($matric_error!='' || $sname_error!='' || $fname_error!='' || $mname_error!='' || $gender_error!='' || $department_error!='' || $phone_error!='')
        $report = 'Error: Please check your input(s)';

    elseif ((int)$matric_ > 0)
        $report = 'Error: Matric number already exist!';

    else {
        $sql = "INSERT INTO $dbname.student 
                VALUES('$matric', '$sname', '$fname', '$mname', '$gender', '$department', '$phone', CURRENT_TIMESTAMP)";
        if (query($sql)){
            $report = 'Registration successful';
            $matric = $sname = $fname = $mname = $gender = $department = $phone = "";
        }
        else
            $report = 'Error: Unable to connect to database!';
    }
}



//excel_file
//excel_btn
elseif (isset($_POST['excel_btn']) && is_post_request()) {
    $student_file = 'upload/students.xls';
    if (file_exists($student_file))
        unlink($student_file);

    $imageFileType = $_FILES['excel_file']['name'];
    $imageFileType = strtolower(pathinfo($imageFileType, PATHINFO_EXTENSION));
    if ($_FILES["excel_file"]["size"] > 5000000) //1MB max
        $report_file = "File too large";
    elseif( $imageFileType != 'xls' ) 
        $report_file = "Invalid file format";

    else {
        $student_file = $student_file;
        move_uploaded_file($_FILES["excel_file"]["tmp_name"], $student_file);




        /////////////////////////////////////////////////////////////////
        $data = new Spreadsheet_Excel_Reader($student_file);
        if(count($data->sheets[0]['cells'])>0) // checking sheet not empty
        {
            $matricError = 0;

            $matricError = $snameError = $fnameError = $mnameError = $genderError = $phoneError = $departmentError = '';

            $matric_list = '';

            $sql_build = array();//build sql statement
            $matric_array = array();//build sql statement
            $Duplicate_matric = ''; //array to confirm duplicate REG NO.
            
            $phone_array = array();//build sql statement
            $Duplicate_phone = ''; //array to confirm duplicate REG NO.





            $department_ = getRow("SELECT department 
                               FROM $dbname.department WHERE sn='1'")['department'];
            $department_temp = strtolower($department_);

            $department_temp = explode('##', $department_temp);
            $department = $data->sheets[0]['cells'][4][2];
            $department = $conn->real_escape_string($department);    
            $department = space($department);
            if(!in_array(strtolower($department), $department_temp))
                $departmentError = 'Invalid department<br/>';



            for($j=8; $j<=count($data->sheets[0]['cells'])+3; $j++) // loop used to get each row of the sheet
            { 
                $matric = $data->sheets[0]['cells'][$j][1];
                $matric = strtoupper(noSpace($matric));
                if(!preg_match('/^[a-zA-Z][\/][a-zA-Z]{2}[\/][0-9]{2}[\/][0-9]{4}$/', $matric)) {
                    $matricError .= "Row $j -> $matric <br/>";
                }

                $sname = $data->sheets[0]['cells'][$j][2];
                $sname = $conn->real_escape_string($sname);
                $sname = noSpace($sname);
                $sname = ucwords($sname);
                if(!preg_match('/^[a-zA-Z]{2,30}$/', $sname))
                    $snameError .= "Row $j -> $sname <br/>";


                $fname = $data->sheets[0]['cells'][$j][3];
                $fname = $conn->real_escape_string($fname);
                $fname = noSpace($fname);
                $fname = ucwords($fname);
                if(!preg_match('/^[a-zA-Z]{2,30}$/', $fname))
                    $fnameError .= "Row $j -> $fname <br/>";


                $mname = $data->sheets[0]['cells'][$j][4];
                $mname = $conn->real_escape_string($mname);
                $mname = noSpace($mname);
                $mname = ucwords($mname);
                if ($mname!='') {
                    if(!preg_match('/^[a-zA-Z]{2,30}$/', $mname))
                        $mnameError .= "Row $j -> $mname <br/>";
                }


                $gender = $data->sheets[0]['cells'][$j][5];
                $gender = $conn->real_escape_string($gender);
                $gender = strtoupper(noSpace($gender));
                if($gender!='M' && $gender!='F')
                    $genderError .= "Row $j -> $gender <br/>";


                $phone = $data->sheets[0]['cells'][$j][6];
                $phone = $conn->real_escape_string($phone);
                $phone = noSpace($phone);
                if ($phone!='') {
                    if(!preg_match('/^[0][1-9][0-9]{9}$/', $phone))
                        $phoneError .= "Row $j -> $phone <br/>";
                }

                if ($departmentError=='' && $matricError=='' && $snameError=='' && $fnameError=='' && $mnameError=='' && $phoneError=='' && $genderError=='')
                    array_push($sql_build, "('$matric', '$sname', '$fname', '$mname', '$gender', '$department', '$phone', CURRENT_TIMESTAMP)");

                
                //trap duplicate phone
                $Duplicate_phone .= (in_array($phone, $phone_array)) ? 
                                        "Row $j -> $phone <br/>" : '';
                array_push($phone_array, $phone);
                
                //trap duplicate matric
                $Duplicate_matric .= (in_array($matric, $matric_array)) ? 
                                        "Row $j -> $matric <br/>" : '';
                array_push($matric_array, $matric);
                $matric_list .= "'$matric' ";

            }
            unset($matric_array, $phone_array);

            //confirm if REG NO exist in database

            $report_file .= ($departmentError!='') ? "$departmentError<br/>" : '';
            $report_file .= ($matricError!='') ? '<b>Matric error:</b> <br/>'.$matricError : '';
            $report_file .= ($snameError!='') ? '<b>Surname error:</b> <br/>'.$snameError : '';
            $report_file .= ($fnameError!='') ? '<b>Firstname error:</b> <br/>'.$fnameError : '';
            $report_file .= ($mnameError!='') ? '<b>Middle name error:</b> <br/>'.$mnameError : '';
            $report_file .= ($genderError!='') ? '<b>Gender error:</b> <br/>'.$genderError : '';
            $report_file .= ($phoneError!='') ? '<b>Phone error:</b> <br/>'.$phoneError : '';
            $report_file .= ($Duplicate_matric!='') ? 
                            "<br/><b>Dublicate matric:</b><br/>$Duplicate_matric" : '';
            $report_file .= ($Duplicate_phone!='') ? 
                            "<br/><b>Dublicate phone number found:</b><br/>$Duplicate_phone" : '';


            if ($report_file=='') {
                $matric_list = space($matric_list);
                $matric_list = str_replace(' ', ', ', $matric_list);
                
                $sql = "SELECT matric FROM $dbname.student WHERE matric IN ({$matric_list})";
                
                $count = (int)countRows($sql);

                if ($count>0) {
                    $result = mysqli_query($conn, $sql);
                    while ($fetch = mysqli_fetch_assoc($result)) {
                        $report_file .= $fetch['matric'] . ' ';
                    }
                    $report_file = "Error: Matric $report_file exist(s) in database!";
                }//duplicate entries found
                else {
                    $sql = "INSERT INTO $dbname.student 
                            VALUES " . implode(', ', $sql_build);
                    if (query($sql)){
                        $report_file = 'Registration successful';
                        $matric = $sname = $fname = $mname = $gender = $department = $phone = "";
                    }
                    else
                        $report_file = 'Error: Unable to connect to database!';
                }
            }
        }//sheet exist
        /////////////////////////////////////////////////////////////////
        

    }// file is ok
} // excelsheel post

?>



        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Student</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-success m-0 font-weight-bold">Add Student</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="matric"><strong>Matric no.</strong></label>
                                                        <input class="form-control" type="text" name="matric" value="<?php echo $matric; ?>" <?php echo $matric_error; ?> required pattern="^[a-zA-Z][/][a-zA-Z]{2}[/][0-9]{2}[/][0-9]{4}$" valddue="H/CS/20/0987">
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
                                                <button class="btn btn-success btn-sm" type="submit" name="register_single">Register student</button>
                                            </div>
                                        </form>





                                        <form method="post" action="" enctype='multipart/form-data'>
                                            <h4 class="mt-4 mb-3 pt-3">Upload Batch</h4>
                                            <div class="mt-2 mb-2 text-danger" style="font-size:0.9em;font-style:italic;">
                                                <?php echo $report_file; ?>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-12 col-sm-9">
                                                    <div class="form-group">
                                                        <input type="file" class="form-control" name="excel_file" accept=".xls">
                                                        <label style="font-style:italic;font-size:0.8em;">Excel file</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3 align-items-end">
                                                    <div class="form-group">
                                                        <button class="btn btn-danger form-control" type="submit" name="excel_btn" style="width:100% !important;">Upload</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>



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
                                    <div class="card-header py-3">
                                        <p class="text-success m-0 font-weight-bold">Student List</p>
                                    </div>
                                    <div class="card-body">
                                        <form class="d-flex justify-content-end"><select class="form-control" style="max-width: 250px;"><option value="undefined">Select department</option></select></form>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="border-top-style: none;">Matric</th>
                                                        <th style="border-top-style: none;">Name</th>
                                                        <th style="border-top-style: none;">Gender</th>
                                                        <th style="border-top-style: none;">Phone</th>
                                                        <th style="border-top-style: none;"><i class="fas fa-user-cog"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
$sql = "SELECT matric, CONCAT(sname, ' ', fname, ' ', mname) AS fullname, 
                gender, department, phone
        FROM $dbname.student";
$count = countRows($sql);

if ($count < 1)
    echo "<tr><td colspan='5'>No record found</td>";
else {
    $result = mysqli_query($conn, $sql);
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>'.$fetch['matric'].'</td>
                <td>'.$fetch['fullname'].'</td>
                <td>'.$fetch['gender'].'</td>
                <td>'.$fetch['phone'].'</td>
                <td><a href="edit_student?matric='.$fetch['matric'].'" role="button" class="btn btn-dark btn-sm" type="button">Edit</button></td>
            </tr>';
    }
}
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