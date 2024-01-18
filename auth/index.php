<?php
include_once("../koneksi.php");
session_start();


// Mengecek apakah parameter 'logout' ada dalam URL
if (isset($_GET['logout'])) {  
    session_unset();
    session_destroy();
    session_start();        
    $_SESSION['success'] = "Anda sudah berhasil logout";
}


// jika sudah login maka akan terlempar ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    $authId = $_SESSION['auth_id'];
    $query = "SELECT role FROM karyawan WHERE id = $authId";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['success'] = "Anda sudah login sebagai <b>{$row['role']}</b>. Silakan logout terlebih dahulu untuk mengganti akun.";
    }

    header('Location: /inspire/dashboard');
    exit();
}

// Chek username dan password
if (isset($_POST['login'])) {
    if ($_POST["captcha_code"] === $_SESSION["captcha_code"]) {
        $query = "SELECT * FROM karyawan";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            // Loop melalui hasil query
            while ($row = mysqli_fetch_assoc($result)) {
                $username = $row['nama'];
                $password = $row['password'];
                if ($_POST['user'] === $username && $_POST['pass'] === $password) {
                    // Login berhasil, atur sesi atau cookie untuk menandai bahwa pengguna telah login
                    $_SESSION['loggedin'] = true;
                    // Redirect ke halaman dashboard.php
                    $_SESSION['login'] = true;
                    $_SESSION['auth_id'] = $row['id'];
                    $query = "SELECT * FROM karyawan WHERE id = $row[id];";
                    $result = mysqli_query($con, $query);
                    $karyawan = mysqli_fetch_assoc($result);
                    $role = $karyawan['role'];
                    $_SESSION['success'] = "Anda sudah berhasil Login sebagai $role";
                    unset($_SESSION['error']);
                    header('Location: /inspire/dashboard');
                    exit();
                } else {
                    $_SESSION['error'] = 'Username atau password salah. Coba lagi.';
                }
            }
        } else {
            echo "Tidak ada data yang ditemukan.";
        }
    }
    else {
        $_SESSION['error'] = 'Login gagal! Captcha tidak sesuai <b>ULANGI LAGI</b>';
    }
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
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- site metas -->
    <title>Inspire</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="../assets/css/style.css?<?php echo time(); ?>">
    <!-- Responsive-->
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <!-- fevicon -->
    <link rel="icon" href="../assets/images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../assets/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-I2V0idzZZav+8Nw/3rWipxU7FpNGbg0T3U1WLb4/zs4I20mlhQWltJZ8b1xMQNyAe0LgR3lcZcuPXldfcrvDZw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Tweaks for older IEs-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
        media="screen">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<!-- body -->

<body class="main-layout wrapper choose_bg">
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger col-10 col-sm-8 col-md-5 col-lg-4 mx-auto text-center p-2 border rounded ">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <?php
    if (isset($_SESSION['warning'])) {
        echo '<div class="alert alert-warning col-10 col-sm-8 col-md-5 col-lg-4 mx-auto text-center p-2 border rounded ">' . $_SESSION['warning'] . '</div>';
        unset($_SESSION['warning']);
    }
    ?>
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success col-10 col-sm-8 col-md-5 col-lg-4 mx-auto text-center p-2 border rounded ">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>
    <div class="col-10 col-sm-8 col-md-5 col-lg-4 mx-auto text-center p-5 border rounded bg-white">
        <h1 style="font-size: 35px; font-weight: bold;">Login Akun</h1>
        <form role="form" method="post" action="/inspire/auth/">
            <div class="mb-3">
                <input type="text" class="form-control input" name="user" placeholder="Username" required />
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <input type="password" class="form-control input" name="pass" placeholder="Password"
                        id="password-input" required />
                    <button type="button" class="btn btn-outline-secondary" id="show-password-button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3" style="display: flex; align-items: left;">
                <img id="captchaImage" style="flex: 1;" src="captcha.php" />
                <input style="flex: 2;" type="text" class="form-control input ml-2" name="captcha_code" placeholder="Ketik ulang isi captcha disini" required />
                <button style="flex: 0.5;" type="button" class="btn btn-dark ml-2" onclick="refreshCaptcha()">
                    <i class="fas fa-sync-alt"></i> <!-- Ikon refresh berputar dari Font Awesome -->
                </button>
            </div>
            <div class="d-grid gap-2">
                <button name="login" class="btn btn-primary-login" style="width: 100%;">Login</button>
            </div>
        </form>
    </div>

    <!-- end google map js -->
</body>

<script>
const passwordInput = document.getElementById('password-input');
const showPasswordButton = document.getElementById('show-password-button');

showPasswordButton.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
    }
});
    function refreshCaptcha() {
        // Mendapatkan elemen gambar captcha
        var captchaImage = document.getElementById('captchaImage');
        
        // Menambahkan parameter timestamp untuk memaksa browser memuat ulang gambar captcha
        var timestamp = new Date().getTime();
        captchaImage.src = "captcha.php?timestamp=" + timestamp;
    }
</script>
</html>