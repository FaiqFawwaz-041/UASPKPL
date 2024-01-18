<?php
include_once("koneksi.php");
session_start();

if($_SESSION['login'] != true){
    $_SESSION['warning'] = 'Anda perlu login terlebih dahulu untuk mengakses halaman dashboard.';
    header('Location: /inspire/auth');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- site metas -->
    <title>Inspire</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="../assets/css/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/style2.css?<?php echo time(); ?>">
    <!-- Responsive-->
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <!-- fevicon -->
    <link rel="icon" href="../assets/images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../assets/css/jquery.mCustomScrollbar.min.css">
    <!-- Tweaks for older IEs-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
        media="screen">
</head>

<body class="main-layout" style="background-color: #11aeef;">
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div id="successDiv" class="alert alert-success text-center p-3" style="position: absolute; top: 10%; left: 50%; transform: translate(-50%, -50%); z-index: 999; font-size: 15px;">' . $_SESSION['success'] . '<button onclick="removeDiv()" class="custom-button px-2">x</button></div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['warning'])) {
        echo '<div id="successDiv" class="alert alert-warning text-center p-3" style="position: absolute; top: 10%; left: 50%; transform: translate(-50%, -50%); z-index: 999; font-size: 15px;">' . $_SESSION['warning'] . '<button onclick="removeDiv()" class="custom-button px-2">x</button></div>';
        unset($_SESSION['warning']);
    }
    ?>
    <script>
    function removeDiv() {
        var successDiv = document.getElementById('successDiv');
        successDiv.remove();
    }
    </script>
    <div class="wrapper">
        <!-- end loader -->
        <div class="sidebar">
            <!-- Sidebar  -->
            <nav id="sidebar">
                <div id="dismiss">
                    <i class="fa fa-arrow-left"></i>
                </div>
                <ul class="list-unstyled components">
                    <li><a href="/inspire/dashboard">Dashboard</a></li>
                    <?php 
                            $id= $_SESSION["auth_id"];
                            $role="";
                            if (isset($_SESSION["auth_id"])){
                                $query = "SELECT * FROM karyawan WHERE id = $id";
                                $result = mysqli_query($con, $query);
                                $karyawan = mysqli_fetch_assoc($result);
                                $role=$karyawan['role'];
                            }
                            if($role =="admin"){
                                echo '<li><a href="/inspire/karyawan">Karyawan</a></li>';
                            }
                            ?>
                    <li><a href="/inspire/kategori">Kategori</a></li>
                    <li><a href="/inspire/barang">Barang</a></li>
                    <li><a href="/inspire/auth?logout">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div id="content">
            <!-- header -->
            <header>
                <!-- header inner -->
                <div class="header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                                <div class="full">
                                    <div class="center-desk">
                                        <div class="logo">
                                            <a href="/inspire/">Inspire</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <ul class="btn">
                                    <li class="btn-nav"><a href="/inspire/dashboard">Dashboard</a></li>
                                    <?php 
                                    $id= $_SESSION["auth_id"];
                                    $role="";
                                    if (isset($_SESSION["auth_id"])){
                                        $query = "SELECT * FROM karyawan WHERE id = $id";
                                        $result = mysqli_query($con, $query);
                                        $karyawan = mysqli_fetch_assoc($result);
                                        $role=$karyawan['role'];
                                    }
                                    if($role =="admin"){
                                        echo '<li class="btn-nav"><a href="/inspire/karyawan">Karyawan</a></li>';
                                    }
                                    ?>
                                    <li class="btn-nav"><a href="/inspire/kategori">Kategori</a></li>
                                    <li class="btn-nav"><a href="/inspire/barang">Barang</a></li>
                                    <li class="btn-nav"><a href="/inspire/auth?logout">Logout</a></li>
                                    <li><button class="btn-side" type="button" id="sidebarCollapse">
                                            <img src="../assets/images/menu_icon.png" alt="#" />
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </header>