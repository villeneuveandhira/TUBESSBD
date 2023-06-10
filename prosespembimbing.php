<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $nip = trim($_GET["nip"]);
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "UPDATE kontrakmbkm SET nip_pembimbingmbkm = ?, status = 'sedang mengikuti' WHERE id_kontrakmbkm = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ss", $param_nip_pembimbingmbkm, $param_id_kontrakmbkm);

        $param_nip_pembimbingmbkm = $nip;
        $param_id_kontrakmbkm = $id;

        if(mysqli_stmt_execute($stmt)){
            echo "
            <script>
                alert('Pembimbing berhasil dipilih');
                document.location.href = 'mahasiswa.php?id=". $_SESSION["nim"] ."';
            </script>
            ";
        }
    }

    mysqli_stmt_close($stmt);
}

?>