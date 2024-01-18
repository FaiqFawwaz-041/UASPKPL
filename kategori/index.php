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
    $pdf->Cell(190, 7, 'DAFTAR KATEGORI BARANG', 0, 1, 'C');
    // Memberikan space kebawah agar tidak terlalu rapat
    $pdf->Cell(10, 7, '', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 6, 'ID', 1, 0, 'C');
    $pdf->Cell(50, 6, 'NAMA KATEGORI', 1, 0, );
    $pdf->Cell(120, 6, 'KETERANGAN', 1, 1, );
    $pdf->SetFont('Arial', '', 10);
    $data_kategori = mysqli_query($con, "select * from kategori");
    while ($row = mysqli_fetch_array($data_kategori)) {
        // Menghitung tinggi sel berdasarkan panjang teks
        $tinggi_sel = ceil($pdf->GetStringWidth($row['keterangan']) / 120) * 6;

        $pdf->Cell(15, $tinggi_sel, $row['id'], 1, 0, 'C');
        $pdf->Cell(50, $tinggi_sel, $row['kategori'], 1, 0);
        $pdf->Multicell(120, 6, $row['keterangan'], 1, 0);
    }
    $pdf->Output();

}
else {
    include_once("../layout.php");
    $result = mysqli_query($con, "SELECT * FROM kategori");


    if (isset($_POST['submit'])) {
        $kategori = $_POST['kategori'];
        $keterangan = $_POST['keterangan'];
        $error = false;
        $errkategori = false;
        $errketerangan = false;

        // Validasi Nama: Hanya huruf diizinkan
        if (!ctype_alpha(str_replace(' ', '', $kategori))) {
            $error = true;
            $errkategori = true;
        }
        // Validasi Keterangan: Tidak mengandung karakter HTML, PHP, atau query SQL
        if (strip_tags($keterangan) !== $keterangan || strpos($keterangan, '<?php') !== false || strpos($keterangan, '?>') !== false || strpos($keterangan, 'SELECT') !== false || strpos($keterangan, 'FROM') !== false || strpos($keterangan, 'AND') !== false || strpos($keterangan, 'OR') !== false) {
            $error = true;
            $errketerangan = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errkategori === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nama kategori hanya boleh mengandung huruf.";
            }
            if ($errketerangan === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Keterangan tidak boleh mengandung karakter HTML, PHP, atau query SQL.";
            }
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/kategori/?tambah";';
            echo '</script>';

        } else {
            $resultx = mysqli_query($con, "INSERT INTO kategori (kategori, keterangan) VALUES ('$kategori', '$keterangan')");

            $_SESSION['success'] = "Berhasil menambah data Kategori baru";
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/kategori";';
            echo '</script>';
        }


    }

    if (isset($_POST['update'])) {
        $id_update = $_POST['id'];
        $kategori = $_POST['kategori'];
        $keterangan = $_POST['keterangan'];
        $error = false;
        $errkategori = false;
        $errketerangan = false;

        // Validasi Nama: Hanya huruf diizinkan
        if (!ctype_alpha(str_replace(' ', '', $kategori))) {
            $error = true;
            $errkategori = true;
        }
        // Validasi Keterangan: Tidak mengandung karakter HTML, PHP, atau query SQL
        if (strip_tags($keterangan) !== $keterangan || strpos($keterangan, '<?php') !== false || strpos($keterangan, '?>') !== false || strpos($keterangan, 'SELECT') !== false || strpos($keterangan, 'FROM') !== false || strpos($keterangan, 'AND') !== false || strpos($keterangan, 'OR') !== false) {
            $error = true;
            $errketerangan = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errkategori === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Nama kategori hanya boleh mengandung huruf.";
            }
            if ($errketerangan === true) {
                $_SESSION['error'] = $_SESSION['error'] . "<br> - Keterangan tidak boleh mengandung karakter HTML, PHP, atau query SQL.";
            }
            $_SESSION['error_id'] = $id_update;
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/kategori/?edit";';
            echo '</script>';

        } else {
            $resulty = mysqli_query($con, "UPDATE kategori SET kategori = '$kategori ', keterangan = '$keterangan' WHERE id = '$id_update'");

            $_SESSION['success'] = "Berhasil mengubah data kategori";
            echo '<script type="text/javascript">';
            echo 'window.location.href = "/inspire/kategori";';
            echo '</script>';
        }


    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        try {
            // Melakukan penghapusan data
            $resultz = mysqli_query($con, "DELETE FROM kategori WHERE id = '$id'");

            if ($resultz) {
                $_SESSION['success'] = "Berhasil menghapus data kategori";
            } else {
                throw new Exception("Gagal menghapus data kategori. Kesalahan query: " . mysqli_error($con));
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            // Mengambil pesan sebelum karakter '('
            $errorMessage = strstr($errorMessage, '(', true);

            $_SESSION['warning'] = $errorMessage ?: "Gagal menghapus data kategori. Terjadi kesalahan tidak diketahui.";
        }
        echo '<script type="text/javascript">';
        echo 'window.location.href = "/inspire/kategori";';
        echo '</script>';
    }

    // Mencari data kategori dengan inputan nama
    if (isset($_POST['carinama'])) {
        $inputan_cari = $_POST['inputan_cari'];
        $resultcari = mysqli_query($con, "SELECT * FROM kategori WHERE kategori LIKE '%$inputan_cari%'");
    }
    // Mencari data kategori dengan inputan deskripsi
    if (isset($_POST['cariketerangan'])) {
        $inputan_cari = $_POST['inputan_cari'];
        $resultcari = mysqli_query($con, "SELECT * FROM kategori WHERE keterangan LIKE '%$inputan_cari%'");
    }

    if (isset($_POST['edit']) || isset($_GET['edit'])) {
        if(isset($_GET['edit'])) {
            $id = $_SESSION['error_id'];
        } else {
            $id = $_POST['id'];
        }
        $resultkategori = mysqli_query($con, "SELECT * FROM kategori WHERE id=$id");
        while ($kategori = mysqli_fetch_array($resultkategori)) {
            $id_kategori = $kategori['id'];
            $kategori_kategori = $kategori['kategori'];
            $keterangan_kategori = $kategori['keterangan'];
        }   ?>
    <div class="container" style="margin-bottom: -80px;">
        <div class=" col-12 mx-auto text-center p-4  border rounded bg-white">
            <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
                <a href="/inspire/kategori/">
                    <button class="custom-button px-3" style="color:red;">X</button>
                </a>
            </h1>
            <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
        ?>
            <h1 style="font-size: 35px; font-weight: bold;" class="text-left">Ubah Data kategori</h1>
            <form role="form" method="post" action="/inspire/kategori/">

                <input type="hidden" name="id" value="<?=$id_kategori?>" />
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Nama Kategori</label>
                    <input type="text" class="form-control input" name="kategori" placeholder="Nama kategori" required pattern=".{4,}" 
                        title="Minimal 4 karakter" style="flex: 2;" value="<?= $kategori_kategori;?>" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Kategori</label>
                    <textarea class="form-control input" name="keterangan" placeholder="keterangan kategori" required minlength="20"
                    title="Minimal 20 karakter" style="flex: 2;"><?= $keterangan_kategori;?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button name="update" class="btn btn-primary-login mx-5"
                        style="display: flex;  text-align: left;">Simpan
                        Data</button>
                </div>

            </form>
        </div>
    </div>
<?php }
    if (isset($_GET['tambah'])) {  ?>

<div class="container" style="margin-bottom: -80px;">
    <div class=" col-12 mx-auto text-center p-4  border rounded bg-white">
        <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
            <a href="/inspire/kategori/">
                <button class="custom-button px-3" style="color:red;">X</button>
            </a>
        </h1>
        <?php
                if (isset($_SESSION['error'])) {
                    echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
        ?>
        <h1 style="font-size: 35px; font-weight: bold;" class="text-left">Form Tambah Kategori</h1>
        <form role="form" method="post" action="/inspire/kategori/">
            <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;"> Nama Kategori</label>
                <input type="text" class="form-control input" name="kategori" placeholder="Nama kategori" required pattern=".{4,}" 
                title="Minimal 4 karakter" style="flex: 2;" />
            </div>
            <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                <label for="nama" class="text-left font-weight-bold text-black " style="flex: 1;">Keterangan</label>
                <textarea class="form-control input" name="keterangan" placeholder="keterangan kategori" required minlength="20"
                    title="Minimal 20 karakter" style="flex: 2;"></textarea>
            </div>

            <div class="d-grid gap-2">
                <button name="submit" class="btn btn-primary-login mx-5"
                    style="display: flex;  text-align: left;">Tambah
                    Data</button>
            </div>

        </form>
    </div>
</div>

<?php }
    if (isset($_GET['cari'])) {  ?>
 <div class="container" style="margin-bottom: 80px;">
    <form action="/inspire/kategori/?cari" method="post">
        <div class="mb-3 " style="display: flex; align-items: center;">
        <input type="text" class="form-control input" name="inputan_cari" placeholder="Cari berdasarkan nama"style="flex: 1;" >
        <button name="carinama" class="btn btn-primary ml-2">Cari berdasar nama</button>
        <button name="cariketerangan" class="btn btn-primary ml-2">Cari berdasar keterangan</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Keterangan</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
        <?php   if (isset($_POST['carinama']) || isset($_POST['cariketerangan'])) {  ?>
            <?php
                       while ($kategori = mysqli_fetch_array($resultcari)) {
                           echo "<tr>";
                           echo "<td>" . $kategori['id'] . "</td>";
                           echo "<td>" . $kategori['kategori'] . "</td>";
                           echo "<td>" . $kategori['keterangan'] . "</td>";
                           $id = $kategori['id'];
                           echo   "<form role='form' method='post' action='/inspire/kategori/'>
                                <input type='hidden'  name='id' value='$id' />
                                <td class='text-center' style='width:0;'>
                                    <button name='edit' class='btn btn-info'>Edit</button>
                                </td>
                                <td class='text-center' style='width:0;'>
                                    <button name='delete' class='btn btn-danger'>Delete</button>
                                </td> 
                                </form>";
                       }
        } else {
            while ($kategori = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $kategori['id'] . "</td>";
                echo "<td>" . $kategori['kategori'] . "</td>";
                echo "<td>" . $kategori['keterangan'] . "</td>";
                $id = $kategori['id'];
                echo   "<form role='form' method='post' action='/inspire/kategori/'>
                                    <input type='hidden'  name='id' value='$id' />
                                    <td class='text-center' style='width:0;'>
                                        <button name='edit' class='btn btn-info'>Edit</button>
                                    </td>
                                    <td class='text-center' style='width:0;'>
                                        <button name='delete' class='btn btn-danger'>Delete</button>
                                    </td> 
                                    </form>";
            }
        }?>
        </tbody>
    </table>
</div>


<?php } else {

    if (!isset($_GET['carinama']) && !isset($_GET['cariketerangan'])) {  ?>
<div class="container" style="margin-bottom: 80px;">
    <?php   if (!isset($_GET['tambah']) && !isset($_POST['edit']) && !isset($_GET['edit'])) {  ?>
        <a href="/inspire/kategori?tambah" class="btn btn-success">Tambah Kategori Baru</a>
        <a href="/inspire/kategori?cari" class="btn btn-primary ml-2">Cari Kategori</a>
        <a href="/inspire/kategori?cetak" class="btn btn-primary ml-2" style="float: right;">Cetak Data Kategori</a>
        <br /><br />
    <?php } ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Keterangan</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
            <?php
                        while ($kategori = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $kategori['id'] . "</td>";
                            echo "<td>" . $kategori['kategori'] . "</td>";
                            echo "<td>" . $kategori['keterangan'] . "</td>";
                            $id = $kategori['id'];
                            echo   "<form role='form' method='post' action='/inspire/kategori/'>
                                <input type='hidden'  name='id' value='$id' />
                                <td class='text-center' style='width:0;'>
                                    <button name='edit' class='btn btn-info'>Edit</button>
                                </td>
                                <td class='text-center' style='width:0;'>
                                    <button name='delete' class='btn btn-danger'>Delete</button>
                                </td> 
                                </form>";
                        }
        ?>
        </tbody>
    </table>
</div>
<?php }
    } ?>



<?php
include_once("../end_layout.php");
}
?>