<?php
// Menyertakan koneksi ke database
include "koneksi.php";
include "upload_foto.php";

// Jika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = date("Y-m-d H:i:s");
    $username = $_SESSION['username'];
    $gambar = '';
    $nama_gambar = $_FILES['gambar']['name'];

    if ($nama_gambar != '') {
        $cek_upload = upload_foto($_FILES["gambar"]);
        if ($cek_upload['status']) {
            $gambar = $cek_upload['message'];
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=article';
            </script>";
            die;
        }
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        if ($nama_gambar == '') {
            $gambar = $_POST['gambar_lama'];
        } else {
            unlink("img/" . $_POST['gambar_lama']);
        }
        $stmt = $conn->prepare("UPDATE article SET judul = ?, isi = ?, gambar = ?, tanggal = ?, username = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $judul, $isi, $gambar, $tanggal, $username, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO article (judul, isi, gambar, tanggal, username) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $judul, $isi, $gambar, $tanggal, $username);
    }

    $simpan = $stmt->execute();
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



<script>
$(document).ready(function () {
    function load_data(hlm = 1) {
        $.ajax({
            url: "article_data.php",
            method: "POST",
            data: { hlm: hlm },
            success: function (data) {
                $('#article_data').html(data);
            }
        });
    }

    load_data();

    $(document).on('click', '.halaman', function () {
        var hlm = $(this).attr("id");
        load_data(hlm);
    });
});
</script>
<div class="container py-4">
    <button type="button" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Artikel
    </button>
    <div class="table-responsive" id="article_data"></div>
</div>



