<?php

    require('inc/essentials.php');
    require('inc/db_config.php');

    session_start();
    $session_id = $_SESSION['adminId'];
    $date_now = date("Y-m-d H:i:s");
    $date = date("Y-m-d");

    update("UPDATE `sessions_utilisateurs` SET `heure_deconnexion`=?, statut = ? WHERE `utilisateur_id`=? AND Date(`heure_connexion`)=?", [$date_now, "Hors Ligne", $session_id, $date], 'ssis');
    session_destroy();
    redirect('index.php');
?>
