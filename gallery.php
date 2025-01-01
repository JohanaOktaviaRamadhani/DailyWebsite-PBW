<?php
include 'navbar.php';  // Sertakan file navbar
include 'koneksi.php'; // Sertakan koneksi database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="img/LOGO-DAILY.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    
    <title>Gallery Web Daily Johana</title>
    <style>
        .gallery-section {
            padding: 20px;
        }
        .gallery-container {
            border: 1px solid #5d5eaa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .gallery-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .video-container {
            display: flex;
            justify-content: center;
        }
        .video-container iframe {
            max-width: 100%;
            height: 315px;
        }
    </style>
</head>
<body>
    
    <!-- HEADER -->
    <section id="hero" class="text-sm-start">
        <div class="container">
            <div class="d-sm-flex flex-sm-row-reverse align-items-center">
                <img src="img/LOGO-DAILY.png" class="img-fluid" width="400" alt="Hero Image">
                <div>
                    <h4 class="lead-bold display-7">Halooo, Selamat datang di gallery website saya!</h4>
                    <p>
                        Di sini, Anda bisa melihat beberapa momen berharga dari berbagai kegiatan saya bersama DOSCOM. Setiap foto yang ditampilkan menangkap semangat kolaborasi, kreativitas, dan kebersamaan yang terus memotivasi kami untuk berkembang dan berbagi ilmu di dunia teknologi. Selamat menikmati!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- GALLERY -->
    <div class="container text-center gallery-section">
        <div class="row g-4">
            <!-- Foto Section -->
            <div class="col-md-6">
                <div class="gallery-container">
                    <h2 class="gallery-title">- FOTO -</h2>
                    <div class="row row-cols-2 g-2">
                        <?php
                        // Query untuk mengambil data gambar dari database
                        $sql_foto = "SELECT * FROM tbl_gallery WHERE tipe = 'gambar' ORDER BY tanggal DESC";
                        $result_foto = $conn->query($sql_foto);

                        if ($result_foto->num_rows > 0) {
                            while ($row = $result_foto->fetch_assoc()) {
                                echo '<div class="col">';
                                echo '<img src="img/'.$row['file'].'" class="img-fluid" alt="Foto">';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Tidak ada foto yang tersedia.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Video Section -->
            <div class="col-md-6">
                <div class="gallery-container">
                    <h2 class="gallery-title">- VIDEO -</h2>
                    <div class="video-container">
                        <?php
                        // Query untuk mengambil data video dari database
                        $sql_video = "SELECT * FROM tbl_gallery WHERE tipe = 'video' ORDER BY tanggal DESC";
                        $result_video = $conn->query($sql_video);

                        if ($result_video->num_rows > 0) {
                            while ($row = $result_video->fetch_assoc()) {
                                echo '<div class="col mb-4">';
                                // Untuk video, gunakan tag video dengan source yang sesuai
                                echo '<video width="100%" controls>';
                                echo '<source src="img/'.$row['file'].'" type="video/mp4">';
                                echo 'Your browser does not support the video tag.';
                                echo '</video>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Tidak ada video yang tersedia.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-lg-start text-white" style="background-color: #472e8d">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
          © 2024 Dibuat oleh Johana Oktavia Ramadhani - A11.2023.15024
        </div>
    </footer>

    <script>
        window.setTimeout("tampilWaktu()", 1000);
  
        function tampilWaktu() {
            var waktu = new Date();
            var bulan = waktu.getMonth() + 1;

            setTimeout("tampilWaktu()", 1000);

            document.getElementById("tanggal").innerHTML = 
            waktu.getDate() + "/" + bulan + "/" + waktu.getFullYear();
            document.getElementById("jam").innerHTML = 
            waktu.getHours() + ":" + waktu.getMinutes() + ":" + waktu.getSeconds();
        }
    </script>
    <script>
        const toggleButton = document.getElementById('toggle-mode');
        const body = document.body;

        toggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-mode');

        if (body.classList.contains('dark-mode')) {
            toggleButton.textContent = 'Light Mode';
        } else {
        toggleButton.textContent = 'Dark Mode';
        }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
