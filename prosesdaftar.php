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
                $sks_dalam_univ = $row["sks_dalam_univ"];
                $sks_luar_univ = $row["sks_luar_univ"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);

    $sql = "SELECT * FROM tprogrammbkm WHERE id_program = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_id_program);

        $param_id_program = $id;

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $sks_program = $row["sks_program"];
                $lingkup_program = $row["lingkup_program"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);

    $flag = 0;

    if ($lingkup_program == 'dalam'){
        if (($sks_dalam_univ - $sks_program) >= 0){
            $flag = 1;
        }
    } else{
        if (($sks_luar_univ - $sks_program) >= 0){
            $flag = 1;
        }
    }

    if ($flag == 1){
        $sql = "INSERT INTO kontrakmbkm (nim, id_program, status, semester_kontrak) VALUES (?, ?, ?, ?)";
    
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_nim, $param_id_program, $param_status, $param_semester_kontrak);
    
            $param_nim = $_SESSION["nim"];
            $param_id_program = $id;
            $param_status = "sedang mendaftar";
            $param_semester_kontrak = $_SESSION["semester_mahasiswa"];
    
            if(mysqli_stmt_execute($stmt)){
                echo "
                <script>
                    alert('Pendaftaran berhasil');
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
            alert('SKS sudah melebihi batas');
            document.location.href = 'mahasiswa.php?id=". $_SESSION["nim"] ."';
        </script>
        ";
    }
}

?>