<?php

    require('inc/db_config.php');
    require('inc/essentials.php');

    if(isset($_GET['email_confirmation'])){
        $data = filteration($_GET);

        $query = select("SELECT * FROM `utilisateurs` WHERE `email`=? AND `token`=? LIMIT 1", 
        [$data['email'], $data['token']], 'ii');

        if(mysqli_num_rows($query) ==1){
            $fetch = mysqli_fetch_assoc($query);
            if($fetch['verifier'] == 1){
                echo "<script>
                    alert('Email déjà vérifié !')
                </script>";
            }else{
                $update = update("UPDATE `utilisateurs` SET `verifier`=?, `statut`=? WHERE `id`=?", [1,1,$fetch['id']], 'ii');
                if($update){
                    echo "<script>alert('Vérification de l’e-mail réussie !')</script>";
                }else{
                    echo "<script>alert('Échec de la vérification de l’e-mail ! Serveur en panne !')</script>";
                }
            }
            echo"<script>
                window.location.href='index.php';
            </script>";
        }else{
            echo "<script>alert('Lien invalide !')</script>";
            echo"<script>
                window.location.href='index.php';
            </script>";
        }
    }
?>