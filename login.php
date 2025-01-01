<?php
//memulai session atau melanjutkan session yang sudah ada
session_start();

//menyertakan code dari file koneksi
include "koneksi.php";

//check jika sudah ada user yang login arahkan ke halaman admin
if (isset($_SESSION['username'])) { 
    header("location:admin.php"); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['user'];
  
  //menggunakan fungsi enkripsi md5 supaya sama dengan password  yang tersimpan di database
  $password = md5($_POST['pass']);

  //prepared statement
  $stmt = $conn->prepare("SELECT username, password 
                          FROM tbl_user 
                          WHERE username=? AND password=?");

  //parameter binding 
  $stmt->bind_param("ss", $username, $password); //username string dan password string
  
  //database executes the statement
  $stmt->execute();
  
  //menampung hasil eksekusi
  $hasil = $stmt->get_result();
  
  //mengambil baris dari hasil sebagai array asosiatif
  $row = $hasil->fetch_array(MYSQLI_ASSOC);

  //check apakah ada baris hasil data user yang cocok
  if (!empty($row)) {
    //jika ada, simpan variable username pada session
    $_SESSION['username'] = $row['username'];

    //mengalihkan ke halaman admin
    header("location:admin.php");
  } else {
    //jika tidak ada (gagal), alihkan kembali ke halaman login
    header("location:login.php");
  }

  //menutup koneksi database
  $stmt->close();
  $conn->close();
} else {
  ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <title>Daily Journal</title>
        <style>
            body {
              height: 100%;
              background-color:rgb(214, 201, 250) !important;
            }

            .global-container {
              height: 100%;
              display: flex;
              align-items: center;
              justify-content: center;
              color: #5d5eaa;
            }

            .login-form {
              width: 360px;
              height: 450px;
              padding: 20px;
              background-color: #5d5eaa;
              !important;
              border-radius: 5px !important;
              box-shadow: 0 4px 6px;
            }

            input[type="email"],
            input[type="password"] {
              background:rgb(214, 201, 250);
              color:aliceblue;
              border: 2px solid rgb(0, 0, 0);
              border-radius: 10px;
              margin-bottom: 25px;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
              outline: none;
              background :rgb(74, 77, 147);
              color:none;
              border:none;
              color: white;
              margin-bottom: 25px;
            }

            .card-title {
              font-weight: 700;
              padding-top: 20px;
            }

            .btn {
              background: rgb(245, 247, 246) !important;
              color: black !important;
              transform: translateY(10px);
              font-size: 14px;
              border-radius: 10px !important;
            }
        </style>
        <title>Login</title>
    </head>
    <body>
      <br>
      <br>
      <br>
        <div class="global-container">
            <div class="card login-form justify-content">
                <div class="card-body">
                    <h1 class="card-title text-center text-white">L O G I N</h1>
                </div>
                <div class="card-text">
                <form method="POST">
                    <div class="mb-3">
                        <label for="exampleInputUsername" class="form-label text-white">Username</label>
                        <input name="user" type="username" class="form-control" id="exampleInputUsername" aria-describedby="usernameHelp">
                        <div id="usernameHelp" class="form-text text-white">We'll never share your email with anyone else.</div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label text-white">Password</label>
                        <input name="pass" type="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label text-white" for="exampleCheck1 text-white">Check me out</label>
                    </div>
                    <div class="d-flex gap-2 col-6 mx-auto">
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Cancel</button>  <!-- Tombol Cancel -->
                  </div>


                </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
          </script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
          <script></script>
    </body>
    </html>
  <?php
}
?>
