<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "SELECT * FROM tmahasiswa WHERE nim = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_nim);

        $param_nim = $id;

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $nim = $row["nim"];
                $nama_mahasiswa = $row["nama_mahasiswa"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);

    $sql = "SELECT * FROM kontrakmbkm WHERE nim = ? AND status = 'sedang mendaftar' AND semester_kontrak = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ss", $param_nim, $param_semester_kontrak);

        $param_nim = $id;
        $param_semester_kontrak = $_SESSION["semester_mahasiswa"];

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $nim = $row["nim"];
                $id_program = $row["id_program"];
                $id_kontrakmbkm = $row["id_kontrakmbkm"];
                $status = $row["status"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MBKM - Status Mahasiswa</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div id="wrapper">

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="index.php" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $nama_mahasiswa ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                        <li class="nav-item align-self-center">
                            <a href='logout.php' class='btn btn-danger mr-2'>Logout</a>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid text-center">

                    <h1 class="h3 mb-4 text-gray-800">Anda sudah terdaftar dalam program MBKM</h1>

                    <div class="container-fluid text-left">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Program Anda</h6>
                            </div>
                            <div class="card-body">

                    <?php

                    $sql = "SELECT * FROM tprogrammbkm WHERE id_program = ?";

                    if($stmt = mysqli_prepare($link, $sql)){
                        mysqli_stmt_bind_param($stmt, "s", $param_id_program);

                        $param_id_program = $id_program;

                        if(mysqli_stmt_execute($stmt)){
                            $result = mysqli_stmt_get_result($stmt);

                            if(mysqli_num_rows($result) == 1){
                                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            
                                $nama_program = $row["nama_program"];
                                $jenis_program = $row["jenis_program"];
                                $durasi = $row["durasi"];
                                $sks_program = $row["sks_program"];
                                $lingkup_program = $row["lingkup_program"];
                            } else{
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                    }

                    echo "<p>Nama Program = ". $nama_program ."</p>";
                    echo "<p>Jenis Program = ". $jenis_program ."</p>";
                    echo "<p>Durasi = ". $durasi ." bulan</p>";
                    echo "<p>SKS Program = ". $sks_program ."</p>";
                    echo "<p>Lingkup Program = ". $lingkup_program ." universitas</p>";

                    mysqli_stmt_close($stmt);
                    
                    ?>

                            </div>
                        </div>
                    </div>

                    <p>Mohon tunggu konfirmasi lebih lanjut</p>
                    <p>Pendaftaran anda sudah diterima? Klik <a href='terimapendaftaran.php?id=<?= $id; ?>'>disini</a></p>
                    <p>Pendaftaran anda ditolak? Klik <a href='tolakpendaftaran.php?id=<?= $id; ?>' class='text-danger'>disini</a></p>
                </div>

            </div>

        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>