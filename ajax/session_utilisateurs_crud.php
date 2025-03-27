<?php

    session_start();

    require('../inc/db_config.php');
    require('../inc/essentials.php');

    // get all utilisateurs

    if(isset($_POST['get_all_session_utilisateurs'])){
        $frm_data = filteration($_POST);
        $filter = isset($frm_data['filter']) ? $frm_data['filter'] : 'jour';

        $query = "SELECT s.*, u.nom AS utilisateur_nom, u.type AS utilisateur_type FROM `sessions_utilisateurs` s JOIN `utilisateurs` u ON s.utilisateur_id = u.id WHERE (u.nom LIKE ? OR s.heure_connexion LIKE ? OR s.heure_deconnexion LIKE ? OR s.statut LIKE ?)";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%"];
        
        switch ($filter) {
            case 'jour':
                $query .= " AND DATE(s.heure_connexion) = CURDATE()";
            break;
            case 'semaine':
                $query .= " AND YEARWEEK(s.heure_connexion, 1) = YEARWEEK(CURDATE(), 1)";
            break;
            case 'mois':
                $query .= " AND MONTH(s.heure_connexion) = MONTH(CURDATE()) AND YEAR(s.heure_connexion) = YEAR(CURDATE())";
            break;
            case 'annee':
                $query .= " AND YEAR(s.heure_connexion) = YEAR(CURDATE())";
            break;
            case 'admin':
                $query .= " AND u.type = 'admin'";
            break;
            case 'utilisateur':
                $query .= " AND u.type = 'utilisateur'";
            break;
        }
        $query .= " ORDER BY s.statut ASC";

        $res = select($query, $values, 'ssss');
        $i = 1;
        $data = "";

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit;
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $utilisateur_nom = htmlspecialchars($row['utilisateur_nom'], ENT_QUOTES, 'UTF-8'); 
                $utilisateur_type = htmlspecialchars($row['utilisateur_type'], ENT_QUOTES, 'UTF-8'); 
                $heure_connexion = date("d-m-Y g:i:s A", strtotime($row["heure_connexion"]));
                $heure_deconnexion = !empty($row["heure_deconnexion"]) && $row["heure_deconnexion"] !== "0000-00-00 00:00:00"
                    ? date("d-m-Y g:i:s A", strtotime($row["heure_deconnexion"])) 
                    : "Non Déconnecté";
                $statut = htmlspecialchars($row['statut'], ENT_QUOTES, 'UTF-8'); 

                $data .="
                    <tr class='align-middle text-center'>
                        <td>$i</td>
                        <td>$utilisateur_nom</td>
                        <td>$heure_connexion</td>
                        <td>$heure_deconnexion</td>
                        <td>$statut</td>
                        <td>$utilisateur_type</td>
                    </tr>
                ";
                $i++;
            }
            echo $data;
        } else {
            echo "<p class='m-2'>Aucun journal de connexion disponible.</p>";
        }
    }

?>