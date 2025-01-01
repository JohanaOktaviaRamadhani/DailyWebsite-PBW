<?php
// Menyertakan koneksi ke database
include "koneksi.php";
include "upload_foto.php";

//jika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = date("Y-m-d H:i:s");
    $username = $_SESSION['username'];
    $gambar = '';
    $nama_gambar = $_FILES['gambar']['name'];

    //jika ada file yang dikirim  
    if ($nama_gambar != '') {
        //panggil function upload_foto untuk cek spesifikasi file yg dikirimkan user
        //function ini memiliki 2 keluaran yaitu status dan message
        $cek_upload = upload_foto($_FILES["gambar"]);

        //cek status true/false
        if ($cek_upload['status']) {
            //jika true maka message berisi nama file gambar
            $gambar = $cek_upload['message'];
        } else {
            //jika true maka message berisi pesan error, tampilkan dalam alert
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=article';
            </script>";
            die;
        }
    }

    //cek apakah ada id yang dikirimkan dari form
    if (isset($_POST['id'])) {
        //jika ada id, lakukan update data dengan id tersebut
        $id = $_POST['id'];

        if ($nama_gambar == '') {
            //jika tidak ganti gambar
            $gambar = $_POST['gambar_lama'];
        } else {
            //jika ganti gambar, hapus gambar lama
            unlink("img/" . $_POST['gambar_lama']);
        }

        $stmt = $conn->prepare("UPDATE article 
                                SET 
                                judul =?,
                                isi =?,
                                gambar = ?,
                                tanggal = ?,
                                username = ?
                                WHERE id = ?");

        $stmt->bind_param("sssssi", $judul, $isi, $gambar, $tanggal, $username, $id);
        $simpan = $stmt->execute();
    } else {
        //jika tidak ada id, lakukan insert data baru
        $stmt = $conn->prepare("INSERT INTO article (judul, isi, gambar, tanggal, username)
                                VALUES (?,?,?,?,?)");

        $stmt->bind_param("sssss", $judul, $isi, $gambar, $tanggal, $username);
        $simpan = $stmt->execute();
    }

    if ($simpan) {
        echo "<script>
            alert('Simpan data sukses');
            document.location='admin.php?page=article';
        </script>";
    } else {
        echo "<script>
            alert('Simpan data gagal');
            document.location='admin.php?page=article';
        </script>";
    }

    $stmt->close();
    $conn->close();
}

// Update Artikel
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $gambar = $_POST['gambar_lama'];

    if ($_FILES['gambar']['name']) {
        if (file_exists("img/" . $gambar)) unlink("img/" . $gambar);
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "img/" . $gambar);
    }

    $stmt = $conn->prepare("UPDATE article SET judul = ?, isi = ?, gambar = ? WHERE id = ?");
    $stmt->bind_param("sssi", $judul, $isi, $gambar, $id);
    $stmt->execute();
    
    // Redirect setelah proses selesai
    header("Location: admin.php?page=article");
    exit;
}

// Hapus Artikel
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];

    if ($gambar && file_exists("img/" . $gambar)) unlink("img/" . $gambar);
    $stmt = $conn->prepare("DELETE FROM article WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Redirect setelah proses selesai
    header("Location: admin.php?page=article");
    exit;
}
?>

<div class="container py-4">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Artikel
    </button>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Judul</th>
                    <th style="width: 50%;">Isi</th>
                    <th style="width: 10%;">Gambar</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM article ORDER BY tanggal DESC";
                $hasil = $conn->query($sql);

                $no = 1;
                while ($row = $hasil->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row["judul"]) ?></strong>
                            <br>pada: <?= htmlspecialchars($row["tanggal"]) ?>
                            <br>oleh: <?= htmlspecialchars($row["username"]) ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($row["isi"])) ?></td>
                        <td>
                            <?php if ($row["gambar"] != '' && file_exists('img/' . $row["gambar"])): ?>
                                <img src="img/<?= htmlspecialchars($row["gambar"]) ?>" class="img-fluid" style="max-width: 100px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Tombol Edit -->
                            <a href="#" title="edit" class="badge rounded-pill text-bg-success" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row["id"] ?>">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Tombol Delete -->
                            <a href="#" title="delete" class="badge rounded-pill text-bg-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row["id"] ?>">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Modal Tambah-->
                    <div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Article</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="formGroupExampleInput" class="form-label">Judul</label>
                                            <input type="text" class="form-control" name="judul" placeholder="Tuliskan Judul Artikel" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="floatingTextarea2">Isi</label>
                                            <textarea class="form-control" placeholder="Tuliskan Isi Artikel" name="isi" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formGroupExampleInput2" class="form-label">Gambar</label>
                                            <input type="file" class="form-control" name="gambar">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row["id"] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Artikel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="post" action="" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                        <div class="mb-3">
                                            <label>Judul</label>
                                            <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($row["judul"]) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Isi</label>
                                            <textarea class="form-control" name="isi" required><?= htmlspecialchars($row["isi"]) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>Gambar</label>
                                            <input type="file" class="form-control" name="gambar">
                                            <input type="hidden" name="gambar_lama" value="<?= $row["gambar"] ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus<?= $row["id"] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Hapus Artikel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="">
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus artikel ini?
                                        <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                        <input type="hidden" name="gambar" value="<?= $row["gambar"] ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                        <button type="submit" class="btn btn-danger" name="hapus">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
