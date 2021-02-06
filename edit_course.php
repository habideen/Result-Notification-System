<?php
require_once('header.php');


$report = '';
$error = 'style="background-color:#ffeeee"';

$code = $title = '';
$code_error = $title_error = '';



//register user
if (isset($_POST['edit_single']) && is_post_request()) {
    $initial = $_POST['initial'];
    $initial = $conn->real_escape_string($initial);
    $initial = noSpace($initial);

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
                   FROM $dbname.course WHERE code NOT IN ('$initial') AND code='$code'")['num'];


    if ($code_error!='' || $title_error!='')
        $report = 'Error: Please check your input(s)';

    elseif ((int)$code_ > 0)
        $report = 'Error: Course code already exist!';

    else {

        $sql = "UPDATE $dbname.course
                SET 
                    code='$code', title='$title' 
                WHERE code='$initial'";

        if (query($sql)){
            $report = 'Record edited successfully';
            $code = $title = '';
        }
        else
            $report = 'Error: Unable to connect to database!'.mysqli_error($conn);
    }
}



else {
    $temp = $code = $_GET['code'];
    $code = $conn->real_escape_string($code);
    $sql = "SELECT title
            FROM $dbname.course WHERE code='$code'";
    $count = countRows($sql);

    if ($count < 1)
        header('Location: course');

    $fetch = getRow($sql);

    $title = $fetch['title'];

}

?>



        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <div class="container-fluid">
                <h3 class="text-dark mb-4">Edit Lecturer</h3>
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
                                                <div class="col-12 col-sm-6 d-none">
                                                    <div class="form-group">
                                                        <label for="code"><strong>Code</strong><br></label>
                                                        <input class="form-control" type="text" name="initial" value="<?php echo $code; ?>" readonly>
                                                    </div>
                                                </div>
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
                                            <div class="form-group"><button class="btn btn-success" type="submit" name="edit_single">Edit Course</button></div>
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