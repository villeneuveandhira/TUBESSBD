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
                $sks_sisa_konversi = $row["sks_sisa_konversi"];
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

    <title>MBKM - Daftar Program</title>

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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $nama_mahasiswa ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
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

                    <h1 class="h3 mb-4 text-gray-800">Anda sudah menyelesaikan program MBKM</h1>
                    <p>Anda memiliki <?= $sks_sisa_konversi ?> SKS yang dapat dikonversi menjadi mata kuliah</p>

                    <div class="card shadow mb-4 text-left">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List mata kuliah yang dapat dikonversi</h6>
                        </div>
                        <div class="card-body">
                        <?php

                        require_once "config.php";

                        $sql = "SELECT * FROM tmatkul WHERE semester_mk > 4 AND kode_mk NOT IN (SELECT kode_mk FROM kontrakmatkul WHERE tipe = 'konversi' AND nim = ". $id .")";
                        if($result = mysqli_query($link, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                                        echo "<thead>";
                                            echo "<tr>";
                                                echo "<th>No.</th>";
                                                echo "<th>Kode Mata Kuliah</th>";
                                                echo "<th>Nama Mata Kuliah</th>";
                                                echo "<th>SKS</th>";
                                                echo "<th>Semester</th>";
                                                echo "<th>Aksi</th>";
                                            echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        $num = 1;
                                        while($row = mysqli_fetch_array($result)){
                                            echo "<tr>";
                                                echo "<td>" . $num++ . "</td>";
                                                echo "<td>" . $row['kode_mk'] . "</td>";
                                                echo "<td>" . $row['nama_mk'] . "</td>";
                                                echo "<td>" . $row['sks_mk'] . "</td>";
                                                echo "<td>" . $row['semester_mk'] . "</td>";
                                                echo "<td><a href='konversi.php?id=". $row["kode_mk"] ."' class='btn btn-primary btn-act'>Konversi</a></td>";
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";                            
                                    echo "</table>";
                                echo '</div>';

                                mysqli_free_result($result);
                            } else{
                                echo '<div class="alert alert-danger"><em>List kosong</em></div>';
                            }
                        } else{
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                        
                        ?>
                        </div>
                    </div>

                    <div class="card shadow mb-4 text-left">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List mata kuliah yang telah dikonversi</h6>
                        </div>
                        <div class="card-body">
                        <?php

                        require_once "config.php";

                        $sql = "SELECT * FROM kontrakmatkul WHERE tipe = 'konversi' AND nim = ". $id;
                        if($result = mysqli_query($link, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                                        echo "<thead>";
                                            echo "<tr>";
                                                echo "<th>No.</th>";
                                                echo "<th>Kode Mata Kuliah</th>";
                                                echo "<th>Nama Mata Kuliah</th>";
                                                echo "<th>SKS</th>";
                                                echo "<th>Semester</th>";
                                            echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        $num = 1;
                                        while($row = mysqli_fetch_array($result)){
                                            $sql2 = "SELECT * FROM tmatkul WHERE kode_mk = ?";

                                            if($stmt2 = mysqli_prepare($link, $sql2)){
                                                mysqli_stmt_bind_param($stmt2, "s", $param_kode_mk);

                                                $param_kode_mk = $row["kode_mk"];

                                                if(mysqli_stmt_execute($stmt2)){
                                                    $result2 = mysqli_stmt_get_result($stmt2);

                                                    if(mysqli_num_rows($result2) == 1){
                                                        $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

                                                        $kode_mk = $row2["kode_mk"];
                                                        $nama_mk = $row2["nama_mk"];
                                                        $sks_mk = $row2["sks_mk"];
                                                        $semester_mk = $row2["semester_mk"];
                                                    }
                                                }
                                            }
                                            echo "<tr>";
                                                echo "<td>" . $num++ . "</td>";
                                                echo "<td>" . $row2['kode_mk'] . "</td>";
                                                echo "<td>" . $row2['nama_mk'] . "</td>";
                                                echo "<td>" . $row2['sks_mk'] . "</td>";
                                                echo "<td>" . $row2['semester_mk'] . "</td>";
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";                            
                                    echo "</table>";
                                echo '</div>';

                                mysqli_free_result($result);
                            } else{
                                echo '<div class="alert alert-danger"><em>List kosong</em></div>';
                            }
                        } else{
                            echo "Oops! Something went wrong. Please try again later.";
                        }

                        mysqli_close($link);
                        ?>
                        </div>
                    </div>

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