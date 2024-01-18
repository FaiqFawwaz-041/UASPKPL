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
    $pdf->Cell(190, 7, 'DAFTAR DATA BARANG', 0, 1, 'C');
    // Memberikan space kebawah agar tidak terlalu rapat
    $pdf->Cell(10, 7, '', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 6, 'ID', 1, 0, 'C');
    $pdf->Cell(50, 6, 'NAMA', 1, 0, );
    $pdf->Cell(25, 6, 'HARGA', 1, 0, );
    $pdf->Cell(25, 6, 'SATUAN', 1, 0, 'C');
    $pdf->Cell(45, 6, 'KATEGORI', 1, 0, 'C');
    $pdf->Cell(30, 6, 'STOK', 1, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $data_barang = mysqli_query($con, "select * from barang");
    $hitung=0;
    while ($row = mysqli_fetch_array($data_barang)) {
        $pdf->Cell(15, 6, $row['id'], 1, 0, 'C');
        $pdf->Cell(50, 6, $row['nama'], 1, 0);
        $pdf->Cell(25, 6, 'Rp'. $row['harga'], 1, 0);
        $pdf->Cell(25, 6, $row['satuan'], 1, 0, 'C');
        $data_kategori_barang = mysqli_query($con, "SELECT * FROM kategori WHERE id = $row[id_kategori]");
        while ($row_kategori = mysqli_fetch_array($data_kategori_barang)) {
            $pdf->Cell(45, 6, $row_kategori ['kategori'], 1, 0, 'C');
        }
        $pdf->Cell(30, 6, $row['stok'], 1, 1, 'C' );
        $hitung++;
        if($hitung%13==0){
            $pdf->SetY(118);
            // Select Arial italic 8
            $pdf->SetFont('Arial', 'I', 8);
            // Print centered page number
            $pdf->Cell(0, 10, 'Page '.$pdf->PageNo(), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
        }
    }
    $pdf->Output();
}
else {
    include_once("../layout.php");

    // Mengambil Data Page dengan Method GET
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

    // Jumlah item per halaman
    $items_per_page = 5;

    // Menghitung offset dan mengatur halaman
    $offset = ($current_page - 1) * $items_per_page;
    $result = mysqli_query($con, "SELECT * FROM barang LIMIT $offset, $items_per_page");
    $result_kategori = mysqli_query($con, "SELECT * FROM kategori");
    $rslt_kategori = mysqli_query($con, "SELECT * FROM kategori");


    // Menambahkan data ke dalam database
    if (isset($_POST['create'])) {
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $satuan = $_POST['satuan'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['id_kategori'];
        $error = false;
        $errnama = false;
        $errharga = false;
        $errstok = false;

        // Validasi Nama: Hanya huruf dan spasi diizinkan
        if (!ctype_alpha(str_replace(' ', '', $nama))) {
            $error = true;
            $errnama = true;
        }

        // Maksimal nilai untuk tipe data integer
        $maxIntegerValue = PHP_INT_MAX;
        // Validasi Harga: Tidak melebihi batas maksimum integer
        if ($harga > $maxIntegerValue) {
            $error = true;
            $errharga = true;
        }
        if ($stok > $maxIntegerValue) {
            $error = true;
            $errstok = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errnama === true) {
                $_SESSION['error'] .= "<br> - Nama barang hanya boleh mengandung huruf dan spasi.";
            }
            if ($errharga === true) {
                $_SESSION['error'] .= "<br> - Harga melebihi batas maksimum yang diizinkan.";
            }
            if ($errstok === true) {
                $_SESSION['error'] .= "<br> - Stok melebihi batas maksimum yang diizinkan.";
            }
            echo '<script>window.location.href = "/inspire/barang?tambah";</script>';
        } else {
            $resultz = mysqli_query($con, "INSERT INTO barang (nama, harga, satuan, stok, id_kategori) VALUES ('$nama', '$harga', '$satuan', '$stok', '$id_kategori')");
            if ($resultz) {
                $_SESSION['success'] = "Berhasil menambah data barang baru";
                echo '<script>window.location.href = "/inspire/barang";</script>';
            } else {
                $_SESSION['error'] = "Gagal menambah data barang baru";
            }
        }
    }


    // Mengupdate data yang sudah di edit di form
    if (isset($_POST['update'])) {
        $id_update = $_POST['id'];
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $satuan = $_POST['satuan'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['kategori'];

        $error = false;
        $errnama = false;
        $errharga = false;
        $errstok = false;

        // Validasi Nama: Hanya huruf dan spasi diizinkan
        if (!ctype_alpha(str_replace(' ', '', $nama))) {
            $error = true;
            $errnama = true;
        }

        // Maksimal nilai untuk tipe data integer
        $maxIntegerValue = PHP_INT_MAX;
        // Validasi Harga: Tidak melebihi batas maksimum integer
        if ($harga > $maxIntegerValue) {
            $error = true;
            $errharga = true;
        }
        if ($stok > $maxIntegerValue) {
            $error = true;
            $errstok = true;
        }

        if ($error === true) {
            $_SESSION['error'] = "Ada kesalahan pada data yang diinputkan :";
            if ($errnama === true) {
                $_SESSION['error'] .= "<br> - Nama barang hanya boleh mengandung huruf dan spasi.";
            }
            if ($errharga === true) {
                $_SESSION['error'] .= "<br> - Harga melebihi batas maksimum yang diizinkan.";
            }
            if ($errstok === true) {
                $_SESSION['error'] .= "<br> - Stok melebihi batas maksimum yang diizinkan.";
            }
            $_SESSION['error_id'] = $id_update;
            echo '<script>window.location.href = "/inspire/barang?edit";</script>';
        } else {
            $results = mysqli_query($con, "UPDATE barang SET nama = '$nama', harga = '$harga', satuan = '$satuan', stok = '$stok', id_kategori = '$id_kategori' WHERE id = '$id_update'");
            if ($results) {
                $_SESSION['success'] = "Berhasil mengubah data barang";
                echo '<script>window.location.href = "/inspire/barang";</script>';
            } else {
                $_SESSION['error'] = "Gagal mengubah data barang";
            }

        }


    }

    // Menghapus Data dari database berdasarkan ID dengan Method POST ['delete']
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $result = mysqli_query($con, "DELETE FROM barang WHERE id = '$id'");

        if ($result) {
            $_SESSION['success'] = "Berhasil menghapus data barang";
            echo '<script>window.location.href = "/inspire/barang";</script>';
        } else {
            $_SESSION['error'] = "Gagal menghapus data barang";
        }
    }

    // Mencari data barang dengan inputan nama
    if (isset($_POST['cari'])) {
        $nama = $_POST['nama_cari'];
        $resultcari = mysqli_query($con, "SELECT * FROM barang WHERE nama LIKE '%$nama%'");
    }

    // Container Edit
    if (isset($_POST['edit']) || isset($_GET['edit'])) {
        if(isset($_GET['edit'])) {
            $id = $_SESSION['error_id'];
        } else {
            $id = $_POST['id'];
        }
        $result2 = mysqli_query($con, "SELECT * FROM kategori");
        $resultbarang = mysqli_query($con, "SELECT * FROM barang WHERE id=$id");

        while ($barang = mysqli_fetch_array($resultbarang)) {
            $id_barang = $barang['id'];
            $nama_barang = $barang['nama'];
            $harga_barang = $barang['harga'];
            $satuan_barang = $barang['satuan'];
            $stok_barang = $barang['stok'];
            $id_kategori_barang = $barang['id_kategori'];
        }

        $kategori_sekarang = mysqli_query($con, "SELECT * FROM kategori WHERE id=$id_kategori_barang");
        ?>  
    <div class="container" style="margin-bottom: -80px;">
        <div class="col-12 mx-auto text-center p-4 border rounded bg-white">
            <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
                <a href="/inspire/barang/">
                    <button class="custom-button px-3" style="color:red;">X</button>
                </a>
            </h1>
            <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
        ?>
            <h1 style="font-size: 35px; font-weight: bold;" class="text-left">Ubah Data barang</h1>
            <form id_kategori="form" method="post" action="/inspire/barang/">
                <input type="hidden" name="id" value="<?= $id_barang ?>" />
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Nama</label>
                    <input type="text" class="form-control input" name="nama" placeholder="Nama barang" required pattern=".{4,}" 
                        title="Minimal 4 karakter" style="flex: 2;" value="<?= $nama_barang; ?>" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Harga</label>
                    <input type="number" class="form-control input" name="harga" placeholder="Rp." required style="flex: 2;" value="<?= $harga_barang; ?>" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Satuan</label>
                    <select id="satuan" name="satuan" class="form-control input" style="flex: 2;">
                    <option value="<?= $satuan_barang; ?>"><?= $satuan_barang; ?></option>
                     <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                    </select>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Stok</label>
                    <input type="number" class="form-control input" name="stok" placeholder="Stok Barang" required style="flex: 2;" value="<?= $stok_barang; ?>" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Kategori</label>
                    <select id="kategori" name="kategori" class="form-control input" style="flex: 2;">
                        <?php
                    while ($kategori = mysqli_fetch_array($kategori_sekarang)) { ?>
                            <option value="<?= $kategori['id'] ?>"><?= $kategori['kategori'] ?> </option>
                        <?php
                    }
                    while ($kategorie = mysqli_fetch_array($result2)) {
                        if ($kategorie['id'] != $kategori['id']) { ?>
                                <option value="<?= $kategorie['id'] ?>"><?= $kategorie['kategori'] ?> </option>
                            <?php }
                        }
        ?>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button name="update" class="btn btn-primary-login mx-5" style="display: flex; text-align: left;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
<?php
    }

    // Container Tambah atau Create
    if (isset($_GET['tambah'])) {  ?>
    <div class="container" style="margin-bottom: -80px;">
        <div class="col-12 mx-auto text-center p-4 border rounded bg-white">
            <h1 style="font-size: 25px; font-weight: bold;" class="text-right">
                <a href="/inspire/barang/">
                    <button class="custom-button px-3" style="color:red;">X</button>
                </a>
            </h1>
            <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div id="successDiv" class="alert alert-danger text-start p-3 m-4" style="font-size: 15px;">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
        ?>
            <h1 style="font-size: 35px; font-weight: bold;" class="text-left mx-4">Form Tambah barang</h1>
            <form id_kategori="form" method="post" action="/inspire/barang/">
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Nama</label>
                    <input type="text" class="form-control input" name="nama" placeholder="Nama barang" required pattern=".{4,}" 
                        title="Minimal 4 karakter" style="flex: 2;" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Harga</label>
                    <input type="number" class="form-control input" name="harga" placeholder="Rp. " required style="flex: 2;" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Satuan</label>
                    <select id="satuan" name="satuan" class="form-control input" style="flex: 2;">
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                    </select>
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">stok</label>
                    <input type="number" class="form-control input" name="stok" placeholder="0" required style="flex: 2;" />
                </div>
                <div class="mb-3 mx-5" style="display: flex; align-items: center;">
                    <label for="nama" class="text-left font-weight-bold text-black" style="flex: 1;">Kategori</label>
                    <select id="kategori" name="id_kategori" class="form-control input" style="flex: 2;">
                        <?php
                    while ($kategori = mysqli_fetch_array($rslt_kategori)) { ?>
                            <option value="<?= $kategori['id'] ?>"><?= $kategori['kategori'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="d-grid gap-2 row">
                    <button name="create" class="btn btn-primary-login mx-5" style="display: flex; text-align: left;">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
<?php
    }
    if (isset($_GET['cari'])) {  ?>
<div class="container mb-5">
<form action="/inspire/barang/?cari" method="post">
        <div class="mb-3 " style="display: flex; align-items: center;">
        <input type="text" class="form-control input" name="nama_cari" placeholder="Cari berdasarkan nama"style="flex: 1;" >
        <button name="cari" class="btn btn-primary ml-2">Cari Barang</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
        
        <?php   if (isset($_POST['cari'])) {
            $kategori_array = array();
            while ($kategori = mysqli_fetch_array($result_kategori)) {
                $kategori_array[$kategori['id']] = $kategori['kategori'];
            }

            while ($barang = mysqli_fetch_array($resultcari)) {
                echo "<tr>";
                echo "<td>" . $barang['id'] . "</td>";
                echo "<td>" . $barang['nama'] . "</td>";
                echo "<td>" . $barang['harga'] . "</td>";
                echo "<td>" . $barang['satuan'] . "</td>";
                echo "<td>" . $barang['stok'] . "</td>";
                if (isset($kategori_array[$barang['id_kategori']])) {
                    echo "<td>Pakan " . $kategori_array[$barang['id_kategori']] . "</td>";
                }
                $id_barang = $barang['id'];
                echo"<form role='form' method='post' action='/inspire/barang/'>
                                        <input type='hidden'  name='id' value='$id_barang' />
                                        <td class='text-center' style='width:0;'>
                                            <button name='edit' class='btn btn-info'>Edit</button>
                                        </td>
                                        <td class='text-center' style='width:0;'>
                                            <button name='delete' class='btn btn-danger'>Delete</button>    
                                        </td>
                                    </form> ";
            }?>
                    </tbody>
                </table>
                <?php
        } else {
            $kategori_array = array();
            while ($kategori = mysqli_fetch_array($result_kategori)) {
                $kategori_array[$kategori['id']] = $kategori['kategori'];
            }

            while ($barang = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $barang['id'] . "</td>";
                echo "<td>" . $barang['nama'] . "</td>";
                echo "<td>" . $barang['harga'] . "</td>";
                echo "<td>" . $barang['satuan'] . "</td>";
                echo "<td>" . $barang['stok'] . "</td>";
                if (isset($kategori_array[$barang['id_kategori']])) {
                    echo "<td>Pakan " . $kategori_array[$barang['id_kategori']] . "</td>";
                }
                $id_barang = $barang['id'];
                echo"<form role='form' method='post' action='/inspire/barang/'>
                                        <input type='hidden'  name='id' value='$id_barang' />
                                        <td class='text-center' style='width:0;'>
                                            <button name='edit' class='btn btn-info'>Edit</button>
                                        </td>
                                        <td class='text-center' style='width:0;'>
                                            <button name='delete' class='btn btn-danger'>Delete</button>    
                                        </td>
                                    </form> ";
            }
            $query_total = "SELECT COUNT(*) as total FROM barang";
            ?>
                    </tbody>
                </table>
               <?php
                $result_total = mysqli_query($con, $query_total);
            $total_items = mysqli_fetch_array($result_total)['total'];
            $total_pages = ceil($total_items / $items_per_page);
            echo '<ul class="pagination">';
            for ($page = 1; $page <= $total_pages; $page++) {
                echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '"><a class="page-link" href="?cari=&page=' . $page . '">' . $page . '</a></li>';
            }
            echo "</ul>";
        }?>
    <?php echo "</div>";
    } else {  ?>
<!-- Container Show atau read atau untuk menampilkan semua data -->
<div class="container mb-5">
    <?php
        // Tombol Tambah hanya ada saat tidak sedang menambah atau mengupdate
        if (!isset($_GET['tambah']) && !isset($_POST['edit']) && !isset($_GET['edit'])) {  ?>
        <a href="/inspire/barang?tambah" class="btn btn-success">Tambah Data Baru</a>
        <a href="/inspire/barang?cari" class="btn btn-primary ml-2">Cari Barang</a>
        <a href="/inspire/barang?cetak" class="btn btn-primary ml-2" style="float: right;">Cetak Data Barang</a>
        <br /><br />
    <?php } ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th colspan="2" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color:white;">
            <?php
                $kategori_array = array();
        while ($kategori = mysqli_fetch_array($result_kategori)) {
            $kategori_array[$kategori['id']] = $kategori['kategori'];
        }

        while ($barang = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $barang['id'] . "</td>";
            echo "<td>" . $barang['nama'] . "</td>";
            echo "<td>" . $barang['harga'] . "</td>";
            echo "<td>" . $barang['satuan'] . "</td>";
            echo "<td>" . $barang['stok'] . "</td>";
            if (isset($kategori_array[$barang['id_kategori']])) {
                echo "<td>Pakan " . $kategori_array[$barang['id_kategori']] . "</td>";
            }
            $id_barang = $barang['id'];
            echo"<form role='form' method='post' action='/inspire/barang/'>
                        <input type='hidden'  name='id' value='$id_barang' />
                        <td class='text-center' style='width:0;'>
                            <button name='edit' class='btn btn-info'>Edit</button>
                        </td>
                        <td class='text-center' style='width:0;'>
                            <button name='delete' class='btn btn-danger'>Delete</button>    
                        </td>
                    </form> ";
        }   ?>
        </tbody>
    </table>
    <?php $query_total = "SELECT COUNT(*) as total FROM barang";
        $result_total = mysqli_query($con, $query_total);
        $total_items = mysqli_fetch_array($result_total)['total'];
        $total_pages = ceil($total_items / $items_per_page);
        echo '<ul class="pagination">';
        for ($page = 1; $page <= $total_pages; $page++) {
            echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '"><a class="page-link" href="?page=' . $page . '">' . $page . '</a></li>';
        }
        echo "</ul> 
</div>";

    }
    include_once("../end_layout.php");
}
?>