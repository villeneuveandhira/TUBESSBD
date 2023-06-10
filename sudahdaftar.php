<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "SELECT * FROM kontrakmbkm WHERE nim = ? AND semester_kontrak = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ss", $param_nim, $param_semester_kontrak);

        $param_nim = $id;
        $param_semester_kontrak = $_SESSION['semester_mahasiswa'];

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            print_r($result);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $nim = $row["nim"];
                $status = $row["status"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    if($status == "sedang mendaftar"){
        header("location: sedangmendaftar.php?id=". $nim);
    } else if($status == "menunggu pembimbing"){
        header("location: menunggupembimbing.php?id=". $nim);
    } else if($status == "sedang mengikuti"){
        header("location: sedangmengikuti.php?id=". $nim);
    } else if($status == "selesai"){
        header("location: selesai.php?id=". $nim);
    } else if($status == "tidak diterima"){
        header("location: tidakditerima.php?id=". $nim);
    } else{
        header("location: mengundurkandiri.php?id=". $nim);
    }

    mysqli_stmt_close($stmt);
}

?>