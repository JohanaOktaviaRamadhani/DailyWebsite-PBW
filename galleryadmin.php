<?php
// Menyertakan koneksi ke database dan fungsi upload
include "koneksi.php";
include "upload_foto.php";

// Jika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tipe_media = $_POST['tipe_media']; // gambar atau video
    $tanggal = date("Y-m-d H:i:s");
    $media = '';
    $nama_media = $_FILES['media']['name'];

    // Jika file dikirim
    if ($nama_media != '') {
        $cek_upload = upload_foto($_FILES["media"]);
        if ($cek_upload['status']) {
            $media = $cek_upload['message'];
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=galleryadmin';
            </script>";
            die;
        }
    }

    $stmt = $conn->prepare("INSERT INTO tbl_gallery (judul, deskripsi, tipe, file, tanggal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $judul, $deskripsi, $tipe_media, $media, $tanggal);
    $simpan = $stmt->execute();

    if ($simpan) {
        echo "<script>
            alert('Media berhasil ditambahkan');
            document.location='admin.php?page=galleryadmin';
        </script>";
    } else {
        echo "<script>
            alert('Media gagal ditambahkan');
            document.location='admin.php?page=galleryadmin';
        </script>";
    }

    $stmt->close();
}

// Jika tombol update diklik
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tipe_media = $_POST['tipe_media'];
    $media = $_POST['media_lama'];

    if ($_FILES['media']['name'] != '') {
        if (file_exists("img/" . $media)) unlink("img/" . $media);
        $cek_upload = upload_foto($_FILES["media"]);
        if ($cek_upload['status']) {
            $media = $cek_upload['message'];
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=galleryadmin';
            </script>";
            die;
        }
    }

    $stmt = $conn->prepare("UPDATE tbl_gallery SET judul = ?, deskripsi = ?, tipe = ?, file = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $judul, $deskripsi, $tipe_media, $media, $id);
    $update = $stmt->execute();

    if ($update) {
        echo "<script>
            alert('Media berhasil diperbarui');
            document.location='admin.php?page=galleryadmin';
        </script>";
    } else {
        echo "<script>
            alert('Media gagal diperbarui');
            document.location='admin.php?page=galleryadmin';
        </script>";
    }

    $stmt->close();
}

// Jika tombol hapus diklik
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $media = $_POST['file']; // Menggunakan 'file' untuk memastikan nama yang sesuai

    if ($media && file_exists("img/" . $media)) unlink("uploads/" . $media);

    $stmt = $conn->prepare("DELETE FROM tbl_gallery WHERE id = ?");
    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>
            alert('Media berhasil dihapus');
            document.location='admin.php?page=galleryadmin';
        </script>";
    } else {
        echo "<script>
            alert('Media gagal dihapus');
            document.location='admin.php?page=galleryadmin';
        </script>";
    }

    $stmt->close();
}
?>


<div class="container py-4">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Media
    </button>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Tipe</th>
                    <th>Media</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM tbl_gallery ORDER BY tanggal DESC";
                $hasil = $conn->query($sql);
                $no = 1;
                while ($row = $hasil->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['tipe']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi'] ?? 'Tidak ada deskripsi') ?></td>
                        <td>
                            <?php if ($row['tipe'] === 'gambar'): ?>
                                <img src="img/<?= htmlspecialchars($row['file']) ?>" class="img-fluid" style="max-width: 100px;">
                            <?php elseif ($row['tipe'] === 'video'): ?>
                                <video width="100" controls>
                                    <source src="img/<?= htmlspecialchars($row['file']) ?>" type="video/mp4">
                                </video>
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
                    
                    <!-- Modal Tambah (di luar loop) -->
                    <div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalTambahLabel">Tambah Media</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="judul" class="form-label">Judul</label>
                                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Tuliskan Judul Media" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Tuliskan Deskripsi Media" rows="3" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipe_media" class="form-label">Tipe Media</label>
                                            <select class="form-select" id="tipe_media" name="tipe_media" required>
                                                <option value="gambar">Gambar</option>
                                                <option value="video">Video</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Media</label>
                                            <input type="file" class="form-control" id="file" name="media" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <input type="submit" value="Simpan" name="simpan" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                                        
                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Media</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label>Judul</label>
                                            <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($row['judul']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Tuliskan Deskripsi Media" rows="3" required></textarea>
                                            </div>
                                        <div class="mb-3">
                                            <label>Tipe Media</label>
                                            <select name="tipe_media" class="form-select">
                                                <option value="gambar" <?= $row['tipe'] === 'gambar' ? 'selected' : '' ?>>Gambar</option>
                                                <option value="video" <?= $row['tipe'] === 'video' ? 'selected' : '' ?>>Video</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Media</label>
                                            <input type="file" class="form-control" name="media">
                                            <input type="hidden" name="media_lama" value="<?= $row['file'] ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="update">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus<?= $row["id"] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Hapus Media</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="">
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus media ini?
                                        <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                        <input type="hidden" name="file" value="<?= $row["file"] ?>">
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
