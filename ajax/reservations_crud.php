<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 

    // add reservations

    if(isset($_POST['add_reservations'])){
        $frm_data = filteration($_POST);

        $balance = $frm_data['montant'] - $frm_data['versement'];
        $date_aujourdhui = date("d-m-Y");

        $statut = "";
        if($frm_data['montant'] > $frm_data['versement']){
            $statut = "En attente";
        }elseif(($frm_data['montant'] = $frm_data['versement'])){
            $statut = "En cours";
        }elseif (strtotime($frm_data['date_r']) < strtotime($date_aujourdhui)) {
            $statut = "Terminer";
        }

        if (strtotime($frm_data['date_r']) < strtotime($date_aujourdhui)) {
            echo "date Reservation non valider";
            exit;
        }else{
            $date_r = date("d-m-Y", strtotime($frm_data["date_r"]));
            $query = "INSERT INTO `reservations`(`client`, `package`, `statut`, `montant`, `versement`, `balance`, `date_r`, `heure_r`) VALUES (?,?,?,?,?,?,?,?)";
            $values = [$frm_data['nomclient'], $frm_data['package'], $statut, $frm_data['montant'], $frm_data['versement'], $balance, $frm_data["date_r"], $frm_data['heure_r']];
            $res = insert($query, $values, 'ssssssss');
            echo $res;
            exit;
        }
    }
    
    // get all reservations
    if(isset($_POST['get_all_reservations'])){
        $frm_data = filteration($_POST);
        $search_value = "%{$frm_data['search']}%";

        $query = "SELECT * FROM `reservations` WHERE (statut LIKE ? OR client LIKE ? OR package LIKE ? OR montant LIKE ? OR versement LIKE ? OR balance LIKE ? OR heure_r LIKE ? OR date_r LIKE ? OR datetime LIKE ?) ORDER BY id DESC";
        $values = array_fill(0, 9, $search_value);  
       
        $res = select($query, $values, 'sssssssss');
        $i = 1;
        $data = "";
        $dateNow = date("Y-m-d");

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit;
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $client = htmlspecialchars($row['client'], ENT_QUOTES, 'UTF-8'); 
                $package = htmlspecialchars($row['package'], ENT_QUOTES, 'UTF-8'); 
                $montant = $row['montant']; 
                $versement = $row['versement']; 
                $balance = $row['balance']; 
                $heure_r = htmlspecialchars($row['heure_r'], ENT_QUOTES, 'UTF-8'); 
                $date_r = date("Y-m-d", strtotime($row["date_r"]));
                $datetime = date("Y-m-d", strtotime($row["datetime"]));

                $balance_text = $balance > 0 ? "<span class='status pending'>{$balance} gdes</span>" : "<span class='status completed'>{$balance} gdes</span>";

                if ($date_r > $dateNow) {
                    $statut = ($montant > $versement) ? "<span class='status pending'>En attente</span>" : "<span class='status process'>En cours</span>";
                } 
                elseif ($date_r < $dateNow) {
                    $statut = ($montant > $versement) ? "<span class='status pending'>Annulée</span>" : "<span class='status completed'>Terminé</span>";
                } else { 
                    $statut = ($montant > $versement) ? "<span class='status pending'>En attente</span>" : "<span class='status process'>En cours</span>";
                }
                
                $del_btn ="<button type='button' onclick='remove_reservations($id)' class='btn btn-sm btn-danger'>
                    <i class='bi bi-trash fs-6'></i>
                </button>";

                $modify_btn = "<button type='button' onclick='edit_reservations($id)' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#edit_reservations'>
                    <i class='bi bi-pencil-square  fs-6'></i>
                </button>";
                
                $print_btn = "<button type='button' onclick='print_reservations($id)' class='btn btn-sm btn-success' data-bs-toggle='modal' data-bs-target='#print_reservations'>
                    <i class='bi bi-printer-fill fs-6'></i>
                </button>";
                
                $data .="
                    <tr class='align-middle'>
                        <td>$i</td>
                        <td>$client</td>
                        <td>$package</td>
                        <td>$statut</td>
                        <td>$montant gdes</td>
                        <td>$versement gdes</td>
                        <td>$balance_text</td>
                        <td>$heure_r</td>
                        <td>$date_r</td>
                        <td>$datetime</td>
                        <td>
                            $modify_btn
                            $del_btn
                            $print_btn
                        </td>
                    </tr>
                ";
                $i++;
            }
            echo $data;
        } else {
            echo "<p class='m-2'>Aucun reservations disponible.</p>";
        }
    }

    // get reservations
    if(isset($_POST["get_reservations"])){
        $frm_data = filteration($_POST);

        $res = select("SELECT * FROM `reservations` WHERE `id`=?", [$frm_data['get_reservations']], 'i');

        $reservationsdata = mysqli_fetch_assoc($res);

        $data = ["reservationsdata" => $reservationsdata];
        $data = json_encode($data);
        echo $data;
    }

    // edit reservations
    if(isset($_POST["edit_reservations"])){
        $frm_data = filteration($_POST);

        $reservation = select("SELECT montant, balance FROM `reservations` WHERE `id` = ?", 
        [$frm_data['id']], "i");

        $row = mysqli_fetch_assoc($reservation);
        $montant = $row['montant'];
        $balance = $row['balance'];

        $nouvelle_balance = $montant - $frm_data['versement'];

        $q = "UPDATE `reservations` SET `date_r` = ?, `heure_r` = ?, `versement` = ?, `balance` = ? WHERE `id` = ?";
        $values = [$frm_data['date_r'], $frm_data['heure_r'], $frm_data['versement'], $nouvelle_balance, $frm_data['id']];

        $res = update($q, $values, 'sssdi');
        echo $res;
    }
    
    // remove reservations
    if (isset($_POST['remove_reservations'])) {
        $frm_data = filteration($_POST);

        $res = deleteR("DELETE FROM `reservations` WHERE `id`=? ", [$frm_data['reservations_id']], 'i');

        if($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

?>