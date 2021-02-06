<?php
$result_active = '1';
require_once('header.php');
require_once 'php-excel-reader/excel_reader2.php';


$report = $report_file = $report_sms = '';
$error = 'style="background-color:#ffeeee"';

$code = $title = '';
$code_error = $title_error = '';

$code_get_error = $session_get_error = '';


$sql_get = '';



$disable_sms = getRow("SELECT COUNT(DISTINCT matric) AS num FROM $dbname.result
        WHERE session='{$session_}' AND sms=''")['num'];
$disable_sms = ((int)$disable_sms < 1) ? 'disabled' : '';



function remark($val) {
    if($val == 'AB')
        return '';
    elseif ($val>=80)
        return 'A';
    elseif ($val>=75)
        return 'AB';
    elseif ($val>=70)
        return 'B';
    elseif ($val>=65)
        return 'BC';
    elseif ($val>=60)
        return 'C';
    elseif ($val>=55)
        return 'CD';
    elseif ($val>=50)
        return 'D';
    elseif ($val>=45)
        return 'DE';
    elseif ($val>=40)
        return 'E';
    elseif ($val>=35)
        return 'EF';
    elseif ($val>=0)
        return 'FF';
}


//excel_file
//excel_btn
if (isset($_POST['excel_btn']) && is_post_request()) {
    $student_file = 'upload/result.xls';
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
            $codeError = $titleError = '';

            $sql_build = array();//build sql statement
            $code_array = array();//build sql statement
            $Duplicate_code = ''; //array to confirm duplicate REG NO.
            $code_list = ''; //array to confirm duplicate REG NO.

            $matric_array = array();//build sql statement
            $Duplicate_matric = ''; //array to confirm duplicate REG NO.
            $matric_list = '';

            $sessionError = $codeError = $matricError = $resultError = '';



            $session = $data->sheets[0]['cells'][4][2];
            $session = noSpace($session);
            if(!preg_match('/^[1-9][0-9]{3}[\/][1-9][0-9]{3}$/', $session))
                $sessionError = "$session is invalid for session<br/>";
            else {
                $session_ = getRow("SELECT COUNT(session) AS num
                                    FROM $dbname.department 
                                    WHERE sn='1' AND session='$session'")['num'];
                if((int)$session_ != 1)
                    $sessionError = "$session does't correspond with present session<br/>";
            }


            $code = $data->sheets[0]['cells'][5][2];
            $code = $conn->real_escape_string($code);
            $code = noSpace($code);
            $code = strtoupper(($code));
            if(!preg_match('/^[A-Z]{3}[0-9]{3}$/', $code))
                $codeError = "$code is and invalid course code<br/>";
            else {
                $code_ = getRow("SELECT COUNT(code) AS num
                                    FROM $dbname.course 
                                    WHERE code='$code'")['num'];
                if((int)$code_ != 1)
                    $codeError = "Course $code does't exist in database<br/>";
            }




            for($j=8; $j<=count($data->sheets[0]['cells'])+2; $j++) // loop used to get each row of the sheet
            { 
                $matric = $data->sheets[0]['cells'][$j][1];
                $matric = strtoupper(noSpace($matric));
                if(!preg_match('/^[a-zA-Z][\/][a-zA-Z]{2}[\/][0-9]{2}[\/][0-9]{4}$/', $matric)) {
                    $matricError .= "Row $j -> $matric <br/>";
                }


                $result = $data->sheets[0]['cells'][$j][2];
                $result = noSpace($result);
                if(!preg_match('/[0-9]{2}$/', $result) && $result!='AB') 
                    $resultError .= "Row $j -> $result <br/>";


                if ($codeError=='' && $titleError=='' && $sessionError=='' && $codeError=='')
                    array_push($sql_build, "(null, '$matric', '$code', '$result', '$session', '')");


                //trap duplicate
                $Duplicate_matric .= (in_array($matric, $matric_array)) ? 
                                        "Row $j -> $matric <br/>" : '';
                array_push($matric_array, $matric);
                $matric_list .= "'$matric' ";

            }
            unset($matric_array);


            $report_file .= ($sessionError!='') ? $sessionError : '';
            $report_file .= ($codeError!='') ? $codeError : '';
            $report_file .= ($matricError!='') ? '<b>Matric error:</b> <br/>'.$matricError : '';
            $report_file .= ($resultError!='') ? '<b>Result error:</b> <br/>'.$resultError : '';


            if ($report_file=='') {
                $matric_list = space($matric_list);
                $matric_list = str_replace(' ', ', ', $matric_list);
                
                $sql = "SELECT matric FROM $dbname.result 
                        WHERE matric IN ({$matric_list}) AND session='$session' AND code='$code'";
                $count = (int)countRows($sql);
                

                /////////////////get not registered students
                $registered_matric = array();
                $excel_matric = str_replace('\'', '', $matric_list);
                $excel_matric = explode(', ', $excel_matric);
                $sql_matric = "SELECT matric FROM $dbname.student WHERE matric IN ({$matric_list})";
                $result = mysqli_query($conn, $sql_matric);
                while ($fetch = mysqli_fetch_assoc($result)) 
                    array_push($registered_matric, $fetch['matric']);


                $not_in_db = array_diff($excel_matric, $registered_matric);
                ////////////////////////////////////////////
                
                
                if (count($not_in_db)>0) {
                    foreach ($not_in_db as $value)
                        $report_file .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$value . '<br/>';
                    $report_file = "Error: The following matirc numbers are not registered<br/> $report_file Please register them to upload or remove them from the excel sheet.";
                }
                elseif ($count>0) {
                    $result = mysqli_query($conn, $sql);
                    $report_file = '<br/>';
                    while ($fetch = mysqli_fetch_assoc($result)) {
                        $report_file .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$fetch['matric'] . '<br/>';
                    }
                    $report_file = "Error: Results for $report_file for this session and course exist(s) in database!";
                }//duplicate entries found
                else {
                    $sql = "INSERT INTO $dbname.result 
                            VALUES " . implode(', ', $sql_build);
                    if (query($sql)){
                        $report_file = 'Result uploaded successfully';
                    }
                    else
                        $report_file = 'Error: Unable to connect to database!';
                }
            }
        }//sheet exist
        /////////////////////////////////////////////////////////////////
        

    }// file is ok
} // excelsheel post





elseif (isset($_POST['sendSMS']) && is_post_request()) {
    $sql = "SELECT DISTINCT result.matric, student.fname, student.phone 
            FROM $dbname.result
            INNER JOIN $dbname.student ON result.matric=student.matric
            WHERE session='{$session_}' AND sms='' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $matric_list = [];
    $fname_list = [];
    $phone_list = [];
    while ($fetch = mysqli_fetch_assoc($result)) {
        array_push($matric_list, $fetch['matric']);
        $fname_list[$fetch['matric']] = $fetch['fname'];
        $phone_list[$fetch['matric']] = $fetch['phone'];
    }

    if (count($matric_list)<1) 
        $report_sms = 'No SMS to send';

    else {
        $report_sms = ''; $count = 0;
        foreach ($matric_list as $std_matric) {
            $sql = "SELECT code, score FROM $dbname.result 
                    WHERE matric='$std_matric' AND session='{$session_}' AND sms=''";
            
            $sms_message = '';
            $result = mysqli_query($conn, $sql);
            while ($fetch = mysqli_fetch_assoc($result)) 
                $sms_message .= $fetch['code'] . ':' . $fetch['score'] . ' ';

            $sms_message = space($sms_message);
            $sms_message = str_replace(' ', ', ', $sms_message);

            $sms_message = 'Dear '. $fname_list[$std_matric] . ', with matric ' . $std_matric
                            . ', your result for ' . $session_ . ' is ' . $sms_message;

            //sendSMS($phone_list[$std_matric], $sms_message);
            sendSMS('08165346948', $sms_message);
            ++$count;
        } //loop through the matric number
        $report_sms = "$count result(s) sent.";
    }
}




elseif (isset($_POST['getresultBtn']) && is_post_request()) {
    $code_get = $_POST['code_get'];
    $code_get = $conn->real_escape_string($code_get);
    $code_get = space($code_get);
    $code_get = ucwords($code_get);
    if ($code_get=='')
        $code_get_error = $error;

    $session_get = $_POST['session_get'];
    $session_get = $conn->real_escape_string($session_get);
    $session_get = space($session_get);
    $session_get = ucwords($session_get);
    if ($session_get=='')
        $session_get_error = 'Error: Please select a session';

    if ($code_get_error=='' && $session_get_error=='') 
        $sql_get = "SELECT result.matric, result.score, 
                            CONCAT(student.sname, ' ', student.fname, ' ', student.mname) AS fullname
                    FROM $dbname.result
                    INNER JOIN $dbname.student ON result.matric=student.matric 
                    WHERE code='$code_get' AND session='$session_get'";

}

?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Result Notification</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-body">
                                        <div class="mt-2 mb-3 text-danger" style="font-size:0.9em;font-style:italic;"></div>
                                        <p><strong>Session:</strong>&nbsp; &nbsp;2019/2020</p>
<?php
$student_result = countRows("SELECT DISTINCT matric 
                        FROM $dbname.result WHERE session='{$session_}'");

$sms_sent = countRows("SELECT DISTINCT matric 
                        FROM $dbname.result WHERE session='{$session_}' AND sms='1'");

$sms_not_sent = $student_result - $sms_sent;


?>                                        
                                        <p><strong>Total student:</strong>&nbsp; &nbsp;<?php echo $student_result; ?></p>
                                        <p class="text-success"><strong>SMS sent:</strong>&nbsp; &nbsp;<?php echo $sms_sent; ?></p>
                                        <p class="text-danger"><strong>SMS not sent:</strong>&nbsp; &nbsp;<?php echo $sms_not_sent; ?></p>
                                        <form method="post" action="" class="mt-4 mb-4">
                                            <div class="mt-4 mb-2 text-danger" style="font-size:0.9em;font-style:italic;">
                                                <?php echo $report_sms; ?>
                                            </div>
                                            <button class="btn btn-success" type="submit" onclick='sendSMS()' name="sendSMS" <?php echo $disable_sms; ?>>Send SMS</button>
                                        </form>


                                        <form method="post" action="" enctype='multipart/form-data' class="pt-3">
                                            <h4 class="mt-4 mb-3 pt-3">Upload Result</h4>
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
                                        <p class="text-success m-0 font-weight-bold">Student Result</p>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="department"><strong>Course code</strong></label>
                                                        <select class="form-control" id="code_get" name="code_get" <?php echo $code_get_error; ?> required>
                                                            <option value=''></option>
<?php
$sql = "SELECT code FROM $dbname.course";


$count = countRows($sql);

if ($count < 1)
    echo '';
else {
    $result = mysqli_query($conn, $sql);
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo "<option value='".$fetch['code']."'>".$fetch['code']."</option>";
    }
}
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="department">Session</label>
                                                        <select class="form-control" id="session_get" name="session_get" <?php echo $session_get_error; ?> required>
                                                            <option value=''></option>
<?php
    $start = 2014;
    $end = (int)date('Y')-2;
    while ($start++ <= $end) {
        $session_ = $start++ .'/'.$start--;
        echo "<option value='{$session_}'>{$session_}</option>";
    }
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-12">
                                                <button class="btn btn-success mr-4" type="submit" name="getresultBtn">Show results</button>
                                            </div>
                                            </div>
                                        </form>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="border-top-style: none;">Matric</th>
                                                        <th style="border-top-style: none;">Name</th>
                                                        <th style="border-top-style: none;">Score</th>
                                                        <th style="border-top-style: none;">Remark</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
if (isset($_POST['getresultBtn']) && is_post_request()) {
    $count = countRows($sql_get);

    if ($count < 1)
        echo "<tr><td colspan='4'>No record found</td>";
    else {
        $result = mysqli_query($conn, $sql_get);
        while ($fetch = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>'.$fetch['matric'].'</td>
                    <td>'.$fetch['fullname'].'</td>
                    <td>'.$fetch['score'].'</td>
                    <td>'.remark($fetch['score']).'
                </tr>';
        }
    }
}
else
    echo "<tr><td colspan='4'>No record found</td>";
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
function sendSMS(){
    if(!confirm('Send result notification?'))
        event.preventDefault();
}
</script>