<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "SELECT * FROM kontrakmbkm WHERE nim = ? AND status = 'sedang mengikuti'";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_nim);

        $param_nim = $id;

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

    $sql = "UPDATE kontrakmbkm SET status = 'selesai' WHERE id_kontrakmbkm = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_id_kontrakmbkm);

        $param_id_kontrakmbkm = $id_kontrakmbkm;

        if(mysqli_stmt_execute($stmt)){
            echo "
            <script>
                alert('Anda sudah selesai menjalankan program MBKM');
                document.location.href = 'mahasiswa.php?id=". $_SESSION["nim"] ."';
            </script>
            ";
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);
}