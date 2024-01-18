<?php
include_once("../koneksi.php");

// untuk mencetak data karyawan
if (isset($_GET['cetak'])) {
    // memanggil library FPDF
    require('../fpdf/fpdf.php');
    // intance object dan memberikan pengaturan halaman PDF
    $pdf = new FPDF('l', 'mm', 'A5');
    // membuat halaman baru
    $pdf->AddPage();
    // setting jenis font yang akan digunakan
    $pdf->SetFont('Arial', 'B', 16);
    // mencetak string
    $pdf->Cell(190, 7, 'TOKO PAKAN INSPIRE', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 7, 'DAFTAR KARYAWAN', 0, 1, 'C');
    // Memberikan space kebawah agar tidak terlalu rapat
    $pdf->Cell(10, 7, '', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 6, 'ID', 1, 0, 'C');
    $pdf->Cell(50, 6, 'NAMA KARYAWAN', 1, 0, );
    $pdf->Cell(40, 6, 'NO TELPON', 1, 0, );
    $pdf->Cell(50, 6, 'JAM KERJA', 1, 0, 'C');
    $pdf->Cell(30, 6, 'ROLE', 1, 1);
    $pdf->SetFont('Arial', '', 10);
    $data_karyawan = mysqli_query($con, "select * from karyawan");
    while ($row = mysqli_fetch_array($data_karyawan)) {
        $pdf->Cell(15, 6, $row['id'], 1, 0, 'C');
        $pdf->Cell(50, 6, $row['nama'], 1, 0);
        $pdf->Cell(40, 6, $row['telepon'], 1, 0);
        $shift_sekarang = mysqli_query($con, "SELECT * FROM shift WHERE id = $row[id_shift]");
        while ($data_shift_sekarang = mysqli_fetch_array($shift_sekarang)) {
            $pdf->Cell(50, 6, $data_shift_sekarang['jam_mulai'] . ' - ' . $data_shift_sekarang['jam_selesai'], 1, 0, 'C');
        }
        $pdf->Cell(30, 6, $row['role'], 1, 1 );
    }
    $pdf->Output();

}
else {

    include_once("../layout.php");

    $result = mysqli_query($con, "SELECT * FROM karyawan");
    $result2 = mysqli_query($con, "SELECT * FROM shift");


    // untuk disable button
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["disable"])) {
        $_SESSION["warning"] = "Anda tidak dapat mengedit atau menghapus data anda sendiri";
        echo '<script type="text/javascript">';
        echo 'window.location.href = "/inspire/karyawan/";';
        echo '</script>';
    }
    // Menambahkan data ke dalam database
    if (isset($_POST['submit'])) {
        $nama = $_POST['nama'];
        $notelp = $_POST['kode_negara'] . $_POST['notelp'];
        $shift = $_POST['shift'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
        $error = false;
        $errnama = false;
        $errnotelp = false;
        $errpass = false;

        // Validasi Nama: Hanya huruf diizinkan
        if (!ctype_alpha(str_replace(' ', '', $nama))) {
            $error = true;
            $errnama = true;
        }

        // Validasi Nomor Telepon: Maksimal 13 angka
        if (strlen($notelp) >= 16) {
            $error = true;
            $errnotelp = true;
        }

        // Validasi Password: Tidak mengandung spasi, karakter HTML, PHP, atau query SQL
        if (strpos($pass, ' ') !== false || strip_tags($pass) !== $pass || strpos($pass, '<?php') !== false || strpos($pass, '?>') !== false || strpos($pass, 'SELECT') !== false || strpos($pass, 'FROM') !== false || strpos($pass, 'AND') !== false || strpos($pass, 'OR') !== false) {
            $error = true;
            $errpass = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errnama === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nama hanya boleh mengandung huruf.";
            }
            if ($errnotelp === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nomor telepon maksimal 13 angka.";
            }
            if ($errpass === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Password tidak boleh mengandung spasi, karakter HTML, PHP, atau query SQL.";
            }
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/karyawan/?tambah";';
            echo '</script>';
        } else {
            $resulty = mysqli_query($con, "INSERT INTO karyawan (nama, telepon, id_shift, password, role) VALUES ('$nama', '$notelp', '$shift', '$pass', '$role')");
            $_SESSION['success'] = "Berhasil menambah data karyawan baru";
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/karyawan/";';
            echo '</script>';
        }
    }


    // Mengupdate data yang sudah di edit di form
    if (isset($_POST['update'])) {
        $id_update = $_POST['id'];
        $nama = $_POST['nama'];
        $notelp = $_POST['kode_negara'] . $_POST['notelp'];
        $shift = $_POST['shift'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];

        $error = false;
        $errnama = false;
        $errnotelp = false;
        $errpass = false;

        // Validasi Nama: Hanya huruf diizinkan
        if (!ctype_alpha(str_replace(' ', '', $nama))) {
            $error = true;
            $errnama = true;
        }

        // Validasi Nomor Telepon: Maksimal 13 angka
        if (strlen($notelp) >= 16) {
            $error = true;
            $errnotelp = true;
        }

        // Validasi Password: Tidak mengandung spasi, karakter HTML, PHP, atau query SQL
        if (strpos($pass, ' ') !== false || strip_tags($pass) !== $pass || strpos($pass, '<?php') !== false || strpos($pass, '?>') !== false || strpos($pass, 'SELECT') !== false || strpos($pass, 'FROM') !== false || strpos($pass, 'AND') !== false || strpos($pass, 'OR') !== false) {
            $error = true;
            $errpass = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errnama) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nama hanya boleh mengandung huruf.";
            }
            if ($errnotelp === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nomor telepon maksimal 13 angka.";
            }
            if ($errpass === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Password tidak boleh mengandung spasi, karakter HTML, PHP, atau query SQL.";
            }
            $_SESSION['error_id'] = $id_update;
            // Mengarahkan kembali ke halaman sebelumnya
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/karyawan/?edit";';
            echo '</script>';
        } else {
            $resultv = mysqli_query($con, "UPDATE karyawan SET nama = '$nama', telepon = '$notelp', id_shift = '$shift', password = '$pass', role = '$role' WHERE id = '$id_update'");

            $_SESSION['success'] = "Berhasil mengubah data karyawan";
            unset($_SESSION['error_id']);
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/karyawan/";';
            echo '</script>';
        }
    }


    // Menghapus Data dari database berdasarkan ID dengan Method POST ['delete']
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $result = mysqli_query($con, "DELETE FROM karyawan WHERE id = '$id'");
        $_SESSION['success'] = "Berhasil menghapus data karyawan";
        echo '<script type="text/javascript">';
        echo 'window.location.href = "/inspire/karyawan";';
        echo '</script>';
    }

    // Mencari data karyawan dengan inputan nama
    if (isset($_POST['cari'])) {
        $nama = $_POST['nama_cari'];
        $resultcari = mysqli_query($con, "SELECT * FROM karyawan WHERE nama LIKE '%$nama%'");
    }

    // Container Edit
    if (isset($_POST['edit']) || isset($_GET['edit'])) {
        $result22 = mysqli_query($con, "SELECT * FROM shift");
        if(isset($_GET['edit'])) {
            $id = $_SESSION['error_id'];
        } else {
            $id = $_POST['id'];
        }
        $resultkaryawan = mysqli_query($con, "SELECT * FROM karyawan WHERE id=$id");
        while ($karyawan = mysqli_fetch_array($resultkaryawan)) {
            $id_karyawan = $karyawan['id'];
            $nama_karyawan = $karyawan['nama'];
            $telepon_karyawan = $karyawan['telepon'];
            $shift_karyawan = $karyawan['id_shift'];
            $pass_karyawan = $karyawan['password'];
            $role_karyawan = $karyawan['role'];
        }
        $shift_sekarang = mysqli_query($con, "SELECT * FROM shift WHERE id=$shift_karyawan");
        $kode_negara = substr($telepon_karyawan, 0, 3);
        $nomor_telepon = substr($telepon_karyawan, 3);
        ?>   
    <div class="container" style="margin-bottom: -80px;">
        <div class=" col-12 mx-auto text-center p-4  border rounded bg-white">
            <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
                <a href="/inspire/karyawan/">
                    <button class="custom-button px-3" style="color:red;">X</button>
                </a>
            </h1>
            <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
        ?>
            <h1 style="font-size: 35px; font-weight: bold;" class="text-left">Ubah Data Karyawan</h1>
            <form role="form" method="post" action="/inspire/karyawan/">
                
                <input type="hidden" name="id" value="<?=$id_karyawan?>" />
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Nama</label>
                    <input type="text" class="form-control input" name="nama" placeholder="Nama karyawan" 
                    required pattern=".{4,}" title="Minimal 4 karakter" style="flex: 2;" value="<?= $nama_karyawan;?>" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black label-telp" style="flex: 1;">No.Telpon</label>
                    <div class="divtelp"style="display: flex">
                    <select class="form-control input" name="kode_negara" style="flex: 1;" required>
                        <option value="<?=$kode_negara;?>"><?=$kode_negara;?></option>
                        <option value="+62">+62</option>
                        <option value="+99">+99</option>
                        <option value="+44">+44</option>
                    </select>
                    <input type="number" class="form-control input" name="notelp" placeholder="nomer telepon" required
                        style="flex: 2;" value="<?= $nomor_telepon;?>" />
                    </div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Shift</label>
                    <select id="shift" name="shift" class="form-control input" style="flex: 2;">
                        <?php
                    while ($shift = mysqli_fetch_array($shift_sekarang)) {?>
                        <option value="<?= $shift['id']?>"><?= $shift['shift']?> [ <?= $shift['jam_mulai']?> -
                            <?= $shift['jam_selesai']?> ]</option>
                        <?php } ?>
                        <?php
                    while ($shift = mysqli_fetch_array($result2)) {
                        if($shift['id'] != $shift_karyawan) {?>
                        <option value="<?= $shift['id']?>"><?= $shift['shift']?> [ <?= $shift['jam_mulai']?> -
                            <?= $shift['jam_selesai']?> ]</option>
                        <?php }
                        }?>
                    </select>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Password</label>
                    <div class="input-group" style="flex: 2;">
                        <input type="password" class="form-control input " style="margin-left:-10px;" name="pass"
                            placeholder="Password" id="password-input" required value="<?= $pass_karyawan;?>" />
                        <button type="button" class="btn btn-outline-secondary" id="show-password-button">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                <div id="password-validation" style="color: red; align-items: end;"></div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Role</label>
                    <select id="shift" name="role" class="form-control input" style="flex: 2;">
                        <?php if($role_karyawan == 'admin') {?>
                        <option value="admin">Admin</option>
                        <option value="shopkeeper">Shopkeeper </option>
                        <?php } else { ?>
                        <option value="shopkeeper">Shopkeeper </option>
                        <option value="admin">Admin</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button name="update" class="btn btn-primary-login mx-5"
                        style="display: flex;  text-align: left;">Simpan
                        Data</button>
                </div>

            </form>
        </div>
    </div>
<?php }?>

<!-- // Container Tambah atau Create -->
<?php
if (isset($_GET['tambah'])) {
    $resultz = mysqli_query($con, "SELECT * FROM shift");       ?> 
    <div class="container" style="margin-bottom: -80px;">
        <div class=" col-12 mx-auto text-center p-4  border rounded bg-white">
            <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
                <a href="/inspire/karyawan/">
                    <button class="custom-button px-3" style="color:red;">X</button>
                </a>
            </h1>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
    ?>
            <h1 style="font-size: 35px; font-weight: bold;" class="text-left">Form Tambah Karyawan</h1>
            <form role="form" method="post" action="/inspire/karyawan/">
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Nama</label>
                    <input type="text" class="form-control input" name="nama" placeholder="Nama karyawan" 
                    required pattern=".{4,}" title="Minimal 4 karakter" style="flex: 2;" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black label-telp">No.Telpon</label>
                    <div class="divtelp"style="display: flex">
                        <select class="form-control input" name="kode_negara" style="flex: 1;" required>
                            <option value="+62">+62</option>
                            <option value="+1">+1</option>
                            <option value="+44">+44</option>
                        </select>
                        <input type="number" class="form-control input" name="notelp" placeholder="Nomor telepon" required style="flex: 2;" />
                    </div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Shift</label>
                    <select id="shift" name="shift" class="form-control input" style="flex: 2;">
                        <?php
                while ($shift = mysqli_fetch_array($resultz)) {?>
                        <option value="<?= $shift['id']?>"><?= $shift['shift']?> [ <?= $shift['jam_mulai']?> -
                            <?= $shift['jam_selesai']?> ]</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Password</label>
                <div class="input-group" style="flex: 2;">
                    <input type="password" class="form-control input" style="margin-left:-10px;" name="pass" placeholder="Password" id="password-input" required />
                    <button type="button" class="btn btn-outline-secondary" id="show-password-button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                <div id="password-validation" style="color: red; align-items: end;"></div>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Role</label>
                    <select id="shift" name="role" class="form-control input" style="flex: 2;">
                        <option value="admin">Admin</option>
                        <option value="shopkeeper">Shopkeeper </option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button name="submit" class="btn btn-primary-login mx-5"
                        style="display: flex;  text-align: left;">Tambah
                        Data</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?php   if (isset($_GET['cari'])) {  ?>
<div class="container" style="margin-bottom: 80px;">
    <form action="/inspire/karyawan/?cari" method="post">
        <div class="mb-3 " style="display: flex; align-items: center;">
        <input type="text" class="form-control input" name="nama_cari" placeholder="Cari berdasarkan nama"style="flex: 1;" >
        <button name="cari" class="btn btn-primary ml-2">Cari Karyawan</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama karyawan</th>
                <th>No Telepon</th>
                <th>Shift</th>
                <th>Role</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
        <?php   if (isset($_POST['cari'])) {  ?>
            <?php
    $shiftData = array();
            while ($shift = mysqli_fetch_array($result2)) {
                $shiftData[$shift['id']] = $shift['shift'];
            }
            while ($karyawan = mysqli_fetch_array($resultcari)) {
                echo "<tr>";
                echo "<td>" . $karyawan['id'] . "</td>";
                echo "<td>" . $karyawan['nama'] . "</td>";
                echo "<td>" . $karyawan['telepon'] . "</td>";
                if (isset($shiftData[$karyawan['id_shift']])) {
                    echo "<td>" . $shiftData[$karyawan['id_shift']] . "</td>";
                }
                echo "<td>" . $karyawan['role'] . "</td>";
                $id = $karyawan['id'];
                if($_SESSION['auth_id'] === $karyawan['id']) {
                    echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                    <input type='hidden' name='id' value='$id' />
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Edit</button>
                    </td>
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Delete</button>
                    </td>
                </form>
                ";
                } else {
                    echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                        <input type='hidden'  name='id' value='$id' />
                        <td class='text-center' style='width:0;'>
                                <button name='edit' class='btn btn-info'>Edit</button>
                        </td>
                        <td class='text-center' style='width:0;'>
                                <button name='delete' class='btn btn-danger'>Delete</button>
                        </td> 
                </form>";
                }
            }
        } else { ?>

        <?php
           $shiftData = array();
            while ($shift = mysqli_fetch_array($result2)) {
                $shiftData[$shift['id']] = $shift['shift'];
            }
            while ($karyawan = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $karyawan['id'] . "</td>";
                echo "<td>" . $karyawan['nama'] . "</td>";
                echo "<td>" . $karyawan['telepon'] . "</td>";
                if (isset($shiftData[$karyawan['id_shift']])) {
                    echo "<td>" . $shiftData[$karyawan['id_shift']] . "</td>";
                }
                echo "<td>" . $karyawan['role'] . "</td>";
                $id = $karyawan['id'];
                if($_SESSION['auth_id'] === $karyawan['id']) {
                    echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                    <input type='hidden' name='id' value='$id' />
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Edit</button>
                    </td>
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Delete</button>
                    </td>
                </form>
                ";
                } else {
                    echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                        <input type='hidden'  name='id' value='$id' />
                        <td class='text-center' style='width:0;'>
                                <button name='edit' class='btn btn-info'>Edit</button>
                        </td>
                        <td class='text-center' style='width:0;'>
                                <button name='delete' class='btn btn-danger'>Delete</button>
                        </td> 
                </form>";
                }
            }
        } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php   if (!isset($_GET['cari'])) {  ?>
<!-- Container Show atau read atau untuk menampilkan semua data -->
<div class="container" style="margin-bottom: 80px;">
    <?php   if (!isset($_GET['tambah']) && !isset($_POST['edit']) && !isset($_GET['edit'])) {  ?>
        <a href="/inspire/karyawan?tambah" class="btn btn-success">Tambah Karyawan Baru</a>
        <a href="/inspire/karyawan?cari" class="btn btn-primary ml-2">Cari Karyawan</a>
        <a href="/inspire/karyawan?cetak" class="btn btn-primary ml-2" style="float: right;">Cetak Data Karyawan</a>
        <br /><br />
    <?php } ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama karyawan</th>
                <th>No Telepon</th>
                <th>Shift</th>
                <th>Role</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
            <?php
            $shiftData = array();
    while ($shift = mysqli_fetch_array($result2)) {
        $shiftData[$shift['id']] = $shift['shift'];
    }
    while ($karyawan = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $karyawan['id'] . "</td>";
        echo "<td>" . $karyawan['nama'] . "</td>";
        echo "<td>" . $karyawan['telepon'] . "</td>";
        if (isset($shiftData[$karyawan['id_shift']])) {
            echo "<td>" . $shiftData[$karyawan['id_shift']] . "</td>";
        }
        echo "<td>" . $karyawan['role'] . "</td>";
        $id = $karyawan['id'];
        if($_SESSION['auth_id'] === $karyawan['id']) {
            echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                    <input type='hidden' name='id' value='$id' />
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Edit</button>
                    </td>
                    <td class='text-center' style='width:0;'>
                        <button name='disable' class='btn btn-secondary' onclick='disableButton(this)'>Delete</button>
                    </td>
                </form>
                ";
        } else {
            echo   "<form role='form' method='post' action='/inspire/karyawan/'>
                        <input type='hidden'  name='id' value='$id' />
                        <td class='text-center' style='width:0;'>
                                <button name='edit' class='btn btn-info'>Edit</button>
                        </td>
                        <td class='text-center' style='width:0;'>
                                <button name='delete' class='btn btn-danger'>Delete</button>
                        </td> 
                </form>";
        }
    }
    ?>
        </tbody>
    </table>
</div>
<?php } ?>

<!-- // untuk mematikan tombol aksi  -->
<script>
function disableButton(button) {
    // Menonaktifkan tombol
    button.disabled = false;
    button.innerText = 'Processing...';
}
// Menangkap elemen-elemen yang dibutuhkan
var passwordInput = document.getElementById('password-input');
    var passwordValidation = document.getElementById('password-validation');
    var showPasswordButton = document.getElementById('show-password-button');
    var form = document.querySelector('form'); // Menangkap elemen formulir

    // Fungsi untuk menampilkan atau menyembunyikan password
    showPasswordButton.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });

    // Fungsi untuk validasi password dan mencegah pengiriman formulir jika tidak valid
    form.addEventListener('submit', function (event) {
        var password = passwordInput.value;

        // Reset pesan validasi
        passwordValidation.textContent = '';

        // Validasi panjang minimal
        if (password.length < 8) {
            passwordValidation.textContent = 'Password harus memiliki minimal 8 karakter.';
            event.preventDefault(); // Mencegah pengiriman formulir
            return;
        }

        // Validasi huruf besar, huruf kecil, angka, dan karakter khusus
        if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password) || !/[!@#\$%^&*(),.?_":{}|<>]/.test(password)) {
            passwordValidation.textContent = 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.';
            event.preventDefault(); // Mencegah pengiriman formulir
            return;
        }

        // Jika password memenuhi semua kriteria, tampilkan pesan validasi hijau
        passwordValidation.textContent = 'Password memenuhi semua kriteria.';
        passwordValidation.style.color = 'green';
    });
</script>             
<?php
include_once("../end_layout.php");
}
?>