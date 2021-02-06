<?php
header_remove("X-Powered-By"); 
header('Server: bravytech');

require_once('core/core.inc.php');
require_once('core/func.php');


if (!isset($_SESSION['control'])) {
    header('Location: logout');
}

$useremail = $_SESSION['control'];
$fullname = getRow("SELECT CONCAT(fname, ' ', lname) AS fullname 
                   FROM $dbname.master WHERE email='$useremail'")['fullname'];


$session_ = getRow("SELECT session 
    FROM $dbname.department WHERE sn='1'")['session'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Federal Polytechnic, Ilaro</title>
    <meta name="description" content="Our result notification system is designed to give students a quick notification of their result.">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="icon" href="assets/img/logo.png?h=h=582a279a0820df9f6e6a8cfa09dc36qqw" type="image/x-icon">
    <style type="text/css">
        #logout a,#logout i{color:#bb0000;}#logout a:hover,#logout i:hover{color:#dd0000;}
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-success p-0">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center justify-content-md-start align-items-md-center sidebar-brand m-0" href="#" style="width: 100%;">
                    <div class="sidebar-brand-icon rotate-n-15"></div>
                    <div><img class="img-fluid mr-1 fas" src="assets/img/logo.png" width="40px" height="40px"><span>FPI</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="nav navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item d-none">
                        <a class="nav-link <?php echo (isset($dashboard_active)) ? 'active':'' ?>" href="home"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($result_active)) ? 'active':'' ?>" href="result"><i class="fas fa-user"></i><span>Result</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($course_active)) ? 'active':'' ?>" href="course"><i class="fas fa-book"></i><span>Course</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($lecturer_active)) ? 'active':'' ?>" href="lecturer"><i class="fas fa-user-graduate"></i><span>Lecturer</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($student_active)) ? 'active':'' ?>" href="student"><i class="fas fa-user-cog"></i><span>Student</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($setting_active)) ? 'active':'' ?>" href="setting"><i class="fas fa-cogs"></i><span>Settings</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($password_active)) ? 'active':'' ?>" href="password"><i class="fas fa-key"></i><span>Password</span></a>
                    </li>
                    <li class="nav-item" id="logout">
                        <a class="nav-link logout" href="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                    </li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="nav navbar-nav flex-nowrap ml-auto">
                            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#"><i class="fas fa-search"></i></a>
                                <div class="dropdown-menu dropdown-menu-right p-3 animated--grow-in" aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto navbar-search w-100">
                                        <div class="input-group"><input class="bg-light form-control border-0 small" type="text" placeholder="Search for ...">
                                            <div class="input-group-append"><button class="btn btn-primary py-0" type="button"><i class="fas fa-search"></i></button></div>
                                        </div>
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#"><span class="d-none d-lg-inline mr-2 text-gray-600 small"><?php echo $fullname; ?></span><img class="border rounded-circle img-profile" src="assets/img/avartar.png"></a>
                                    <div
                                        class="dropdown-menu shadow dropdown-menu-right animated--grow-in">
                                        
                                            <a class="dropdown-item" href="logout"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Logout</a></div>
                    </div>
                    </li>
                    </ul>
            </div>
            </nav>