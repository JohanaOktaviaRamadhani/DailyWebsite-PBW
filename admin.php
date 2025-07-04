<?php
session_start();

if (!isset($_SESSION['nama_admin'])) {
    header("location:login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Daily Journal | Admin Panel</title>
    <link rel="icon" href="img/logo.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        html { position: relative; min-height: 100%; }
        body { margin-bottom: 100px; }
        footer { position: absolute; bottom: 0; width: 100%; height: 100px;}
        .navbar { background-color: #6B46C1; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: #fff; }
        .navbar-nav .nav-link { color: #fff !important; }
        .navbar-nav .nav-link:hover { color: #F6AD55 !important; }
        .dropdown-menu { background-color: #6B46C1; border: none; z-index: 1050 !important; }
        .dropdown-item { color: #fff; }
        .dropdown-item:hover { background-color: #F6AD55; }
        .logout-item {
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            text-align: center;
            border-top: 1px solid #F6AD55; 
            border-bottom: 1px solid #F6AD55; 
            transition: all 0.3s ease;
        }
        .logout-item:hover {
            background-color: #F6AD55; 
            color: #6B46C1; 
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #content { background-color: #fff; padding: 30px; border-radius: 8px; }
        .lead.display-6 { color: #1A202C; }
        .border-danger-subtle { border-color: #6B46C1 !important; }
        footer { background-color: #472e8d; color: #FFFFFF; }
        footer .h2, footer .text-light { color: #FFFFFF !important; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="admin.php?page=dashboard">My Bisnis</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-dark">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?page=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?page=stok">Stok</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?page=adminTransaksi">Transaksi</a>
                    </li> 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['nama_admin']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item logout-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li> 
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section id="content" class="p-5">
        <div class="container">
            <?php
            $allowed_pages = ['dashboard', 'stok', 'adminTransaksi'];

            $currentPage = $_GET['page'] ?? 'dashboard';

            if (in_array($currentPage, $allowed_pages)) {
                $pageTitle = match ($currentPage) {
                    'stok' => 'Stok Produk',
                    'adminTransaksi' => 'Manajemen Transaksi',
                    default => ucfirst($currentPage)
                };

                echo "<h4 class='lead display-6 pb-2 border-bottom border-danger-subtle'>$pageTitle</h4>";
                include("$currentPage.php");
            } else {
                echo "<h4 class='lead display-6 pb-2 border-bottom border-danger-subtle'>Dashboard</h4>";
                include("dashboard.php");
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center text-lg-start text-white">
        <div style="background-color: #472e8d">
            <div class="container text-center text-md-start mt-5">
                <div class="row mt-3">
                    <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                        <br>
                        <h6 class="text-uppercase fw-bold">About Me</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p>Saya Johana Oktavia Ramadhani, mahasiswi Teknik Informatika di Universitas Dian Nuswantoro.</p>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <br>
                        <h6 class="text-uppercase fw-bold">Contact Me</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><i class="bi bi-house-door-fill"></i> Semarang, Jawa Tengah</p>
                        <p><i class="bi bi-envelope-fill"></i> hanaoktavia82281@gmail.com</p>
                        <p><i class="bi bi-phone-fill"></i> 089661235659</p>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <br>
                        <h6 class="text-uppercase fw-bold">Follow Me</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p>
                            <a href="https://www.instagram.com/jhnaoktv_?igsh=ZmprcGJ2OHJmZTA4" class="text-white me-4"><i class="bi bi-instagram"></i></a>
                            <a href="https://www.linkedin.com/in/johanaoktavia" class="text-white me-4"><i class="bi bi-linkedin"></i></a>
                            <a href="https://github.com/JohanaOktaviaRamadhani" class="text-white me-4"><i class="bi bi-github"></i></a>
                        </p>
                    </div>
                </div>
                <br>
            </div>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0,0,0,0.2)">
            © 2024 Dibuat oleh Johana Oktavia Ramadhani - A11.2023.15024
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
