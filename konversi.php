<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "SELECT * FROM tmahasiswa WHERE nim = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_nim);

        $param_nim = $_SESSION["nim"];

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $nim = $row["nim"];
                $sks_sisa_konversi = $row["sks_sisa_konversi"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);

    $sql = "SELECT * FROM tmatkul WHERE kode_mk = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_kode_mk);

        $param_kode_mk = $id;

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $kode_mk = $row["kode_mk"];
                $nama_mk = $row["nama_mk"];
                $sks_mk = $row["sks_mk"];
                $semester_mk = $row["semester_mk"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);

    if (($sks_sisa_konversi - $sks_mk) >= 0){

        $sql = "INSERT INTO kontrakmatkul (kode_mk, nim, tipe, sem_kontrak) VALUES (?, ?, ?, ?)";
    
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_kode_mk, $param_nim, $param_tipe, $param_semester_kontrak);
    
            $param_kode_mk = $id;
            $param_nim = $_SESSION["nim"];
            $param_tipe = "konversi";
            $param_semester_kontrak = $_SESSION["semester_mahasiswa"];
    
            if(mysqli_stmt_execute($stmt)){
                echo "
                <script>
                    alert('Mata kuliah berhasil di konversi');
                    document.location.href = 'mahasiswa.php?id=". $_SESSION["nim"] ."';
                </script>
                ";
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
    
        mysqli_stmt_close($stmt);
    } else{
        echo "
        <script>
            alert('SKS tidak mencukupi');
            document.location.href = 'mahasiswa.php?id=". $_SESSION["nim"] ."';
        </script>
        ";
    }

}

?>