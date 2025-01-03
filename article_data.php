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
        include "koneksi.php";

        // Menentukan halaman aktif dan jumlah data per halaman
        $hlm = isset($_POST['hlm']) ? $_POST['hlm'] : 1;
        $limit = 5; 
        $limit_start = ($hlm - 1) * $limit; 
        $no = $limit_start + 1;

        // Query data artikel dengan limit
        $sql = "SELECT * FROM article ORDER BY tanggal DESC LIMIT $limit_start, $limit";
        $hasil = $conn->query($sql);

        // Query untuk menghitung total artikel
        $total_sql = "SELECT COUNT(*) AS total FROM article";
        $total_result = $conn->query($total_sql);
        $total_row = $total_result->fetch_assoc();
        $total_records = $total_row['total'];
        $jumlah_page = ceil($total_records / $limit);

        // Menampilkan data artikel
        while ($row = $hasil->fetch_assoc()) {
        ?>
        <div>
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
                        <div method="post" action="" enctype="multipart/form-data">
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
                               <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>                                </div>
                            </div>
                    
                        </div>
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
            
        </tr>
        <?php } ?>
    </tbody>
</table>

<p>Total article: <?= $total_records; ?></p>

<!-- Pagination -->
<nav class="mb-2">
    <ul class="pagination justify-content-end">
        <?php
        $jumlah_number = 1; // Jumlah halaman ke kiri/kanan dari halaman aktif
        $start_number = ($hlm > $jumlah_number) ? $hlm - $jumlah_number : 1;
        $end_number = ($hlm < ($jumlah_page - $jumlah_number)) ? $hlm + $jumlah_number : $jumlah_page;

        // Tombol "First" dan "Previous"
        if ($hlm == 1) {
            echo '<li class="page-item disabled"><a class="page-link" href="#">First</a></li>';
            echo '<li class="page-item disabled"><a class="page-link" href="#"><span aria-hidden="true">&laquo;</span></a></li>';
        } else {
            $link_prev = $hlm - 1;
            echo '<li class="page-item halaman" id="1"><a class="page-link" href="#">First</a></li>';
            echo '<li class="page-item halaman" id="'.$link_prev.'"><a class="page-link" href="#"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        // Tombol halaman
        for ($i = $start_number; $i <= $end_number; $i++) {
            $link_active = ($hlm == $i) ? ' active' : '';
            echo '<li class="page-item halaman'.$link_active.'" id="'.$i.'"><a class="page-link" href="#">'.$i.'</a></li>';
        }

        // Tombol "Next" dan "Last"
        if ($hlm == $jumlah_page) {
            echo '<li class="page-item disabled"><a class="page-link" href="#"><span aria-hidden="true">&raquo;</span></a></li>';
            echo '<li class="page-item disabled"><a class="page-link" href="#">Last</a></li>';
        } else {
            $link_next = $hlm + 1;
            echo '<li class="page-item halaman" id="'.$link_next.'"><a class="page-link" href="#"><span aria-hidden="true">&raquo;</span></a></li>';
            echo '<li class="page-item halaman" id="'.$jumlah_page.'"><a class="page-link" href="#">Last</a></li>';
        }
        ?>
    </ul>
</nav>
