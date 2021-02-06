<?php
$course_active = '1';
require_once('header.php');
require_once 'php-excel-reader/excel_reader2.php';


$report = $report_file = '';
$error = 'style="background-color:#ffeeee"';

$code = $title = '';
$code_error = $title_error = '';



//register user
if (isset($_POST['register_single']) && is_post_request()) {
    $code = $_POST['code'];
    $code = $conn->real_escape_string($code);
    $code = noSpace($code);
    $code = strtoupper(($code));
    if(!preg_match('/^[A-Z]{3}[0-9]{3}$/', $code))
        $code_error = $error;


    $title = $_POST['title'];
    $title = $conn->real_escape_string($title);
    $title = space($title);
    $title = strtoupper($title);
    if(!preg_match('/[A-Z0-9 #\-]{3,100}$/', $title))
        $title_error = $error;



    $code_ = getRow("SELECT COUNT(code) AS num 
                   FROM $dbname.course WHERE code='$code'")['num'];


    if ($code_error!='' || $title_error!='')
        $report = 'Error: Please check your input(s)';

    elseif ((int)$code_ > 0)
        $report = 'Error: Course code already exist!';

    else {
        $sql = "INSERT INTO $dbname.course 
                VALUES('$code', '$title')";
        if (query($sql)){
            $report = 'Registration successful';
            $code = $title = '';
        }
        else
            $report = 'Error: Unable to connect to database!';
    }//no error
}//upload single






//excel_file
//excel_btn
elseif (isset($_POST['excel_btn']) && is_post_request()) {
    $student_file = 'upload/course.xls';
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



            for($j=7; $j<=count($data->sheets[0]['cells'])+3; $j++) // loop used to get each row of the sheet
            { 
                $code = $data->sheets[0]['cells'][$j][1];
                $code = $conn->real_escape_string($code);
                $code = noSpace($code);
                $code = strtoupper(($code));
                if(!preg_match('/^[A-Z]{3}[0-9]{3}$/', $code))
                    $codeError .= "Row $j -> $code <br/>";


                $title = $data->sheets[0]['cells'][$j][2];
                $title = $conn->real_escape_string($title);
                $title = space($title);
                $title = strtoupper($title);
                if(!preg_match('/[A-Z0-9 #\-]{3,100}$/', $title))
                    $titleError .= "Row $j -> $title <br/>";


                if ($codeError=='' && $titleError=='')
                    array_push($sql_build, "('$code', '$title')");


                //trap duplicate
                $Duplicate_code .= (in_array($code, $code_array)) ? 
                                        "Row $j -> $code <br/>" : '';
                array_push($code_array, $code);
                $code_list .= "'$code' ";

            }

            $report_file .= ($codeError!='') ? '<b>Course code error:</b> <br/>'.$codeError : '';
            $report_file .= ($titleError!='') ? '<b>Course title error:</b> <br/>'.$titleError : '';
            $report_file .= ($Duplicate_code!='') ? '<b>Duplicate code:</b> <br/>'.$Duplicate_code : '';


            if ($report_file=='') {
                $code_list = space($code_list);
                $code_list = str_replace(' ', ', ', $code_list);
                
                $sql = "SELECT code FROM $dbname.course WHERE code IN ({$code_list})";
                
                $count = (int)countRows($sql);

                if ($count>0) {
                    $result = mysqli_query($conn, $sql);
                    while ($fetch = mysqli_fetch_assoc($result)) {
                        $report_file .= $fetch['code'] . ' ';
                    }
                    $report_file = "Error: Course code $report_file exist(s) in database!";
                }//duplicate entries found
                else {
                    $sql = "INSERT INTO $dbname.course 
                            VALUES " . implode(', ', $sql_build);
                    if (query($sql)){
                        $report_file = 'Registration successful';
                        $code = $title = '';
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
                <h3 class="text-dark mb-4">Course</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col mb-4">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-success m-0 font-weight-bold">Add Course</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-danger" style="font-size:0.9em;">
                                            <?php echo $report; ?>
                                        </div>
                                        <form method="post" action="">
                                            <div class="form-row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="code"><strong>Code</strong><br></label>
                                                        <input class="form-control" type="text" name="code" required pattern="^[a-zA-Z]{3}[0-9]{3}$" value="<?php echo $code; ?>" <?php echo $code_error; ?>>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="title"><strong>Title</strong><br></label>
                                                        <input class="form-control" type="text" name="title" pattern="^[a-zA-Z0-9 #\-]{3,100}$" required value="<?php echo $title; ?>" <?php echo $title_error; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group"><button class="btn btn-success btn-sm" type="submit" name="register_single">Register Course</button></div>
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
                                        <p class="text-success m-0 font-weight-bold">Course List</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="border-top-style: none;">Code</th>
                                                        <th style="border-top-style: none;">Title</th>
                                                        <th style="border-top-style: none;"><i class="fas fa-cog"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
$sql = "SELECT code, title
        FROM $dbname.course";
$count = countRows($sql);

if ($count < 1)
    echo "<tr><td colspan='3'>No record found</td>";
else {
    $result = mysqli_query($conn, $sql);
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>'.$fetch['code'].'</td>
                <td>'.$fetch['title'].'</td>
                <td><a href="edit_course?code='.$fetch['code'].'" role="button" class="btn btn-dark btn-sm" type="button">Edit</button></td>
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
        <footer class="bg-white sticky-footer">
            <div class="container my-auto">
                <div class="text-center my-auto copyright"><span>Copyright © Federal Polytechnic, Ilaro 2020</span></div>
            </div>
        </footer>
    </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="assets/js/script.min.js"></script>
</body>

</html>