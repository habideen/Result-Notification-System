<?php
$lecturer_active = '1';
require_once('header.php');
require_once 'php-excel-reader/excel_reader2.php';


$report = $report_file = '';
$error = 'style="background-color:#ffeeee"';


$sname = $fname = $mname = $gender = $address = $phone = "";
$sname_error = $fname_error = $mname_error = $gender_error = $address_error = $phone_error = "";



//register user
if (isset($_POST['register_single']) && is_post_request()) {
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


    $address = $_POST['address'];
    $address = $conn->real_escape_string($address);
    $address = ucwords(space($address));
    if ($address!='') {
        if(!preg_match('/^[a-zA-Z 0-9 .,]{3,100}$/', $address))
            $address_error = $error;
    }


    $phone = $_POST['phone'];
    $phone = $conn->real_escape_string($phone);
    $phone = noSpace($phone);
    if ($phone!='') {
        if(!preg_match('/^[0][1-9][0-9]{9}$/', $phone))
            $phone_error = $error;
    }




    if ($sname_error!='' || $fname_error!='' || $mname_error!='' || $gender_error!='' || $address_error!='' || $phone_error!='')
        $report = 'Error: Please check your input(s)';

    else {
        $sql = "INSERT INTO $dbname.lecturer 
                VALUES(null, '$sname', '$fname', '$mname', '$gender', '$address', '$phone', CURRENT_TIMESTAMP)";
        if (query($sql)){
            $report = 'Registration successful';
            $matric = $sname = $fname = $mname = $gender = $department = $phone = "";
        }
        else
            $report = 'Error: Unable to connect to database!'; akin18rich
    }//no error
} //register single






//excel_file
//excel_btn
elseif (isset($_POST['excel_btn']) && is_post_request()) {
    $student_file = 'upload/lecturer.xls';
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
            $snameError = $fnameError = $mnameError = $genderError = $phoneError = $addressError = '';

            $sql_build = array();//build sql statement
            $matric_array = array();//build sql statement
            $Duplicate_matric = ''; //array to confirm duplicate REG NO.



            for($j=7; $j<=count($data->sheets[0]['cells'])+3; $j++) // loop used to get each row of the sheet
            { 
                $sname = $data->sheets[0]['cells'][$j][1];
                $sname = $conn->real_escape_string($sname);
                $sname = noSpace($sname);
                $sname = ucwords($sname);
                if(!preg_match('/^[a-zA-Z]{2,30}$/', $sname))
                    $snameError .= "Row $j -> $sname <br/>";


                $fname = $data->sheets[0]['cells'][$j][2];
                $fname = $conn->real_escape_string($fname);
                $fname = noSpace($fname);
                $fname = ucwords($fname);
                if(!preg_match('/^[a-zA-Z]{2,30}$/', $fname))
                    $fnameError .= "Row $j -> $fname <br/>";


                $mname = $data->sheets[0]['cells'][$j][3];
                $mname = $conn->real_escape_string($mname);
                $mname = noSpace($mname);
                $mname = ucwords($mname);
                if ($mname!='') {
                    if(!preg_match('/^[a-zA-Z]{2,30}$/', $mname))
                        $mnameError .= "Row $j -> $mname <br/>";
                }


                $gender = $data->sheets[0]['cells'][$j][4];
                $gender = $conn->real_escape_string($gender);
                $gender = strtoupper(noSpace($gender));
                if($gender!='M' && $gender!='F')
                    $genderError .= "Row $j -> $gender <br/>";


                $address = $data->sheets[0]['cells'][$j][5];
                $address = $conn->real_escape_string($address);
                $address = noSpace($address);
                if ($address!='') {
                    if(!preg_match('/^[a-zA-Z 0-9 .,]{3,100}$/', $address))
                        $addressError .= "Row $j -> $address <br/>";
                }


                $phone = $data->sheets[0]['cells'][$j][6];
                $phone = $conn->real_escape_string($phone);
                $phone = noSpace($phone);
                if ($phone!='') {
                    if(!preg_match('/^[0][1-9][0-9]{9}$/', $phone))
                        $phoneError .= "Row $j -> $phone <br/>";
                }

                if ($snameError=='' && $fnameError=='' && $mnameError=='' && $genderError=='' && $addressError=='' && $phoneError=='')
                    array_push($sql_build, "(null, '$sname', '$fname', '$mname', '$gender', '$address', '$phone', CURRENT_TIMESTAMP)");

            }

            $report_file .= ($snameError!='') ? '<b>Surname error:</b> <br/>'.$snameError : '';
            $report_file .= ($fnameError!='') ? '<b>Firstname error:</b> <br/>'.$fnameError : '';
            $report_file .= ($mnameError!='') ? '<b>Middle name error:</b> <br/>'.$mnameError : '';
            $report_file .= ($genderError!='') ? '<b>Gender error:</b> <br/>'.$genderError : '';
            $report_file .= ($phoneError!='') ? '<b>Phone error:</b> <br/>'.$phoneError : '';
            $report_file .= ($addressError!='') ? '<b>Address error:</b> <br/>'.$addressError : '';


            if ($report_file=='') {
                $sql = "INSERT INTO $dbname.lecturer 
                        VALUES " . implode(', ', $sql_build);
                if (query($sql)){
                    $report_file = 'Registration successful';
                    $sname = $fname = $mname = $gender = $address = $phone = '';
                }
                else
                    $report_file = 'Error: Unable to connect to database!';
            }
        }//sheet exist
        /////////////////////////////////////////////////////////////////
        

    }// file is ok
} // excelsheel post





?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Lecturer</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-success m-0 font-weight-bold">Add Lecture</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
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
                                                    <div class="form-group"><label for="username"><strong>Office Address</strong><br></label><input class="form-control" type="text" name="address" value="<?php echo $address; ?>" <?php echo $address_error; ?> pattern="^[a-zA-Z 0-9 .,]{3,100}$" required=""></div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group"><label for="phone"><strong>Phone no.</strong></label><input class="form-control" type="text" name="phone" value="<?php echo $phone; ?>" <?php echo $phone_error; ?> pattern="^[0][1-9][0-9]{9}$" required></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-success" type="submit" name="register_single">Register Lecture</button>
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
                                        <p class="text-success m-0 font-weight-bold">Lecturer List</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="border-top-style: none;">Name</th>
                                                        <th style="border-top-style: none;">Gender</th>
                                                        <th style="border-top-style: none;">Office</th>
                                                        <th style="border-top-style: none;">Phone</th>
                                                        <th style="border-top-style: none;"><i class="fas fa-user-cog"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
$sql = "SELECT id, CONCAT(sname, ' ', fname, ' ', mname) AS fullname, 
                gender, address, phone
        FROM $dbname.lecturer";
$count = countRows($sql);

if ($count < 1)
    echo "<tr><td colspan='5'>No record found</td>";
else {
    $result = mysqli_query($conn, $sql);
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>'.$fetch['fullname'].'</td>
                <td>'.$fetch['gender'].'</td>
                <td>'.$fetch['address'].'</td>
                <td>'.$fetch['phone'].'</td>
                <td><a href="edit_lecturer?id='.$fetch['id'].'" role="button" class="btn btn-dark btn-sm" type="button">Edit</button></td>
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

</script>