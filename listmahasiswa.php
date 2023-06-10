<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MBKM - Admin</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin.php">
                <div class="sidebar-brand-text mx-3">Pendataan MBKM</div>
            </a>

            <hr class="sidebar-divider my-0">
            
            <li class="nav-item">
                <a class="nav-link" href="admin.php">
                    <span>Dashboard</span></a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="liststatus.php">
                    <span>Status</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="listmahasiswa.php">
                    <span>Mahasiswa</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="listprogram.php">
                    <span>Program</span></a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="listpembimbing.php">
                    <span>Pembimbing</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="reset.php">
                    <span>Reset</span></a>
            </li>

        </ul>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="index.php" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
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

                <div class="container-fluid">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List mahasiswa</h6>
                        </div>
                        <div class="card-body">
                        <?php

                        require_once "config.php";

                        $sql = "SELECT * FROM tmahasiswa";
                        if($result = mysqli_query($link, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                                        echo "<thead>";
                                            echo "<tr>";
                                                echo "<th>No.</th>";
                                                echo "<th>NIM</th>";
                                                echo "<th>Nama</th>";
                                                echo "<th>Prodi</th>";
                                                echo "<th>Semester</th>";
                                                echo "<th>Sisa SKS Dalam Universitas</th>";
                                                echo "<th>Sisa SKS Luar Universitas</th>";
                                                echo "<th>Status</th>";
                                            echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        $num = 1;
                                        $tipe = '';
                                        while($row = mysqli_fetch_array($result)){
                                            echo "<tr>";
                                                echo "<td>" . $num++ . "</td>";
                                                echo "<td>" . $row['nim'] . "</td>";
                                                echo "<td>" . $row['nama_mahasiswa'] . "</td>";
                                                echo "<td>" . $row['prodi'] . "</td>";
                                                echo "<td>" . $row['semester_mahasiswa'] . "</td>";
                                                echo "<td>" . $row['sks_dalam_univ'] . "</td>";
                                                echo "<td>" . $row['sks_luar_univ'] . "</td>";
                                                if ($row['status_mahasiswa'] == 'belum berpartisipasi') $tipe = 'danger';
                                                else $tipe = 'success';
                                                echo "<td><span class = 'badge badge-". $tipe ."'>" . $row['status_mahasiswa'] . "</span></td>";
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

    <script src="vendor/chart.js/Chart.min.js"></script>

    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>