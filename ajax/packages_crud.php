<?php

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 
 
    if(isset($_POST['add_packages'])){ 
        $total_plats = 0;
        $total_boissons = 0;

        $id_plats = filteration(json_decode($_POST['id_plats']));
        $prix_plats = filteration(json_decode($_POST['prix_plats']));
        $id_boissons = filteration(json_decode($_POST['id_boissons']));
        $prix_boissons = filteration(json_decode($_POST['prix_boissons']));

        foreach ($prix_plats as $prix_plat) {
            $total_plats += (float)$prix_plat;
        }
        foreach ($prix_boissons as $prix_boisson) {
            $total_boissons += (float)$prix_boisson;
        }

        $frm_data = filteration($_POST);
        $flag = 0;
        $somme_total = $total_plats + $total_boissons;

        $q = "INSERT INTO `packages`(`nom`, `prix`) VALUES (?,?)";
        $values = [$frm_data['nom'], $somme_total];
        
        if(insert($q, $values, 'si')){
            $flag = 1;
        }
        $id_packages = mysqli_insert_id($con);

        $q2 = "INSERT INTO `packages_boissons`(`id_packages`, `id_boissons`) VALUES (?,?)";
        if($stmt = mysqli_prepare($con, $q2)){
            foreach($id_boissons as $b){
                mysqli_stmt_bind_param($stmt,"ii", $id_packages, $b);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
        }else{
            $flag = 0;
            die("query cannot be prepared - insert");
        }

        $q3 = "INSERT INTO `packages_plats`(`id_packages`, `id_plats`) VALUES (?,?)";
        if($stmt = mysqli_prepare($con, $q3)){
            foreach($id_plats as $p){
                mysqli_stmt_bind_param($stmt,"ii", $id_packages, $p);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
        }else{
            $flag = 0;
            die("query cannot be prepared - insert");
        }

        if($flag){
            echo 1;
        }else{
            echo 0;
        }
    }
    
    if(isset($_POST['get_all_packages'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `packages` WHERE (nom LIKE ? OR qte_pers LIKE ? OR prix LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'sss');

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit; 
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8'); 
                $prix = htmlspecialchars($row['prix'], ENT_QUOTES, 'UTF-8'); 
                $datetime = date("Y-m-d g:i:s A", strtotime($row["datetime"]));

                $res2 = select("SELECT pl.id, pl.nom, pl.prix, pp.qte_plats FROM plats pl JOIN packages_plats pp ON pl.id = pp.id_plats JOIN packages p ON pp.id_packages = p.id WHERE p.id=?", [$id], 'i');
                $res3 = select("SELECT bo.id, bo.nom, bo.prix, pb.qte_boissons FROM boissons bo JOIN packages_boissons pb ON bo.id = pb.id_boissons JOIN packages p ON pb.id_packages = p.id WHERE p.id=?", [$id], 'i');
                $total = 0;
                    echo"<div class='col-lg-4 col-md-5 mb-4 scale'>
                        <div class='bg-white rounded shadow p-4 border-top border-4 border-dark pop'>
                        <div class='text-center'>
                            <h4>ID(#$row[nom])</h4>
                            <h6 class='text-danger'>$datetime</h6>
                        </div><hr>
                        <div class='table-data-plats'>
                           <button type='button' onclick='add_qte_details_plats($id)' class='btn btn-success shadow-none btn-sm mb-4' data-bs-toggle='modal' data-bs-target='#add_qte' data-id='$id'>
                                <i class='bi bi-plus-square m-1'></i>Ajouter les Quantitées
                            </button>

                            <div class='table-responsive'>
                                <table class='table table-hover border text-center'>
                                    <thead>
                                        <tr class='bg-dark'>
                                            <th scope='col' class='bg-dark text-light text-center'>Quantité de personnes : $row[qte_pers]</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div><hr>

                            <div class='table-responsive'>
                                <table class='table table-hover border text-center'>
                                    <thead>
                                        <tr class='bg-dark'>
                                            <th scope='col' class='bg-dark text-light text-center'>Plats</th>
                                            <th scope='col' class='bg-dark text-light text-center'>Prix(gdes)</th>
                                            <th scope='col' class='bg-dark text-light text-center'>Qte</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                                        foreach($res2 as $plats){
                                            $total_plats = $plats['prix']*$plats['qte_plats'];
                                            $total += $total_plats;
                                            echo"
                                            <tr class='align-middle'>
                                                <td>$plats[nom]</td>
                                                <td>$plats[prix]</td>
                                                <td>$plats[qte_plats]</td>
                                            </tr>";
                                        }   
                                    echo"</tbody>
                                </table>
                            </div>
                        </div><hr>
                        <div class='table-data-boissons'>
                            <div class='table-responsive'>
                                <table class='table table-hover border  text-center'>
                                    <thead>
                                        <tr class='bg-dark '>
                                            <th scope='col' class='bg-dark text-light text-center'>Boissons</th>
                                            <th scope='col' class='bg-dark text-light text-center'>Prix(gdes)</th>
                                            <th scope='col' class='bg-dark text-light text-center'>Qte</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                                    foreach($res3 as $boissons){
                                        $total_boissons = $boissons['prix']*$boissons['qte_boissons'];
                                        $total += $total_boissons;
                                        echo"
                                        <tr class='align-middle'>
                                            <td>$boissons[nom]</td>
                                            <td>$boissons[prix]</td>
                                            <td>$boissons[qte_boissons]</td>
                                        </tr>";
                                    }
                                    echo "</tbody>
                                </table>
                            </div>
                        </div><hr>
                        <div class='table-data'>
                            <div class='order'>
                                <div class='card border-0 shadow-sm'>
                                    <div class='card-body'>
                                        <div class='table-responsive'>
                                            <table class='table table-hover border'>
                                                <thead>
                                                    <tr class='bg-dark '>
                                                        <th scope='col' class='bg-dark text-light text-center'>Sommme du package</th>
                                                    </tr>
                                                </thead>
                                                <tbody id='total-data'>
                                                    <tr>
                                                        <td>$total gourdes</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                            if($_SESSION['type'] == 'utilisateur'){

                            }else{
                                echo "<hr><div class='d-flex justify-content-center'>
                                <button type='button' onclick='edit_package($id)' class='btn btn-primary shadow-none btn-sm m-1' data-bs-toggle='modal' data-bs-target='#edit_package'>
                                    Moddifier
                                </button>
                                <button type='button' onclick='remove_package($id)' class='btn btn-danger shadow-none btn-sm m-1'>
                                    Supprimer
                                </button>";
                            }
                        echo"</div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<p class='m-2'>Aucun package disponible.</p>";
        }
    }

    // add qte

    if(isset($_POST["add_qte"])){

        $id_package = filter_input(INPUT_POST, 'id_package', FILTER_SANITIZE_NUMBER_INT);
        $qte_pers = filter_input(INPUT_POST, 'qte_pers', FILTER_SANITIZE_NUMBER_INT);
        $qte_plats = json_decode($_POST['qte_plats'] ?? "[]", true);
        $id_plats = json_decode($_POST['id_plats'] ?? "[]", true);
        $prix_plats = json_decode($_POST['prix_plats'] ?? "[]", true);
        $qte_boissons = json_decode($_POST['qte_boissons'] ?? "[]", true);
        $id_boissons = json_decode($_POST['id_boissons'] ?? "[]", true);
        $prix_boissons = json_decode($_POST['prix_boissons'] ?? "[]", true);

        $flag = false;
        $total = 0;

        if (!is_array($qte_plats) || !is_array($id_plats) || !is_array($qte_boissons) || !is_array($id_boissons)) {
            echo "0"; 
            exit;
        }


        if(!empty($qte_pers)){
            $q = "UPDATE `packages` SET `qte_pers`=? WHERE `id`=?";
            $values = [(int) $qte_pers, (int) $id_package];
            if (update($q, $values, 'ii')) {
                $flag = true;
            }
        }

        if (!empty($id_plats)) {
            for ($i = 0; $i < count($id_plats); $i++) {
                $q = "UPDATE `packages_plats` SET `qte_plats`=? WHERE `id_packages`=? AND `id_plats`=?";
                $values = [(int) $qte_plats[$i], $id_package, (int) $id_plats[$i]];
                if (update($q, $values, 'iii')) {
                    $flag = true;
                }
                $total += (int) $qte_plats[$i] * (int)$prix_plats[$i];
            }
        }

        if (!empty($id_boissons)) {
            for ($i = 0; $i < count($id_boissons); $i++) {
                $q = "UPDATE `packages_boissons` SET `qte_boissons`=? WHERE `id_packages`=? AND `id_boissons`=?";
                $values = [(int) $qte_boissons[$i], $id_package, (int) $id_boissons[$i]];
                if (update($q, $values, 'iii')) {
                    $flag = true;
                }
                $total += (int)$qte_boissons[$i] * (int)$prix_boissons[$i];
            }
        }

        $q = "UPDATE `packages` SET `prix`=? WHERE `id`=?";
        $values = [(float) $total, (int) $id_package];
        update($q, $values, 'di');

        echo $flag ? "1" : "0";

    }

    // get package
    if(isset($_POST["get_package"])){
        $frm_data = filteration($_POST);

        $res1 = select("SELECT * FROM `packages` WHERE `id`=?", [$frm_data['get_package']], 'i');
        $res2 = select("SELECT * FROM `packages_plats` WHERE `id_packages`=?", [$frm_data['get_package']], 'i');
        $res3 = select("SELECT * FROM `packages_boissons` WHERE `id_packages`=?", [$frm_data['get_package']], 'i');

        $packagedata = mysqli_fetch_assoc($res1);
        $plats = [];
        $boissons = [];

        while($row = mysqli_fetch_assoc($res2)){
            array_push( $plats, $row['id_plats']);
        }

        while($row = mysqli_fetch_assoc($res3)){
            array_push( $boissons, $row['id_boissons']);
        }

        $data = ["packagedata" => $packagedata,"plats"=> $plats,"boissons"=> $boissons];
        $data = json_encode($data);
        echo $data;
    }

    // edit package 
    if(isset($_POST["edit_packages"])){

        $plats = json_decode($_POST['plats'], true);
        $boissons = json_decode($_POST['boissons'], true);
        $frm_data = filteration($_POST);
        $package_id = $frm_data['id']; 

        $flag = 0;
    
        $del_plats = deleteR("DELETE FROM `packages_plats` WHERE `id_packages`=?", [$package_id], 'i');
        $del_boissons = deleteR("DELETE FROM `packages_boissons` WHERE `id_packages`=?", [$package_id], 'i');
    
        if(!$del_boissons || !$del_plats){
            $flag = 0;
        }
    
        $q2 = "INSERT INTO `packages_boissons`(`id_packages`, `id_boissons`) VALUES (?,?)";
        $stmt = mysqli_prepare($con, $q2);
        if($stmt){
            foreach($boissons as $boisson_id){
                mysqli_stmt_bind_param($stmt, "ii", $package_id, $boisson_id);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
            $flag = 1;
        } else {
            die("Erreur d'insertion des boissons");
        }
    
        $q3 = "INSERT INTO `packages_plats`(`id_packages`, `id_plats`) VALUES (?,?)";
        $stmt = mysqli_prepare($con, $q3);
        if($stmt){
            foreach($plats as $plat_id){
                mysqli_stmt_bind_param($stmt, "ii", $package_id, $plat_id);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
            $flag = 1;
        } else {
            die("Erreur d'insertion des plats");
        }
    
        echo $flag ? 1 : 0;

    }

   // remove package
if (isset($_POST['remove_package'])) {
    $frm_data = filteration($_POST);

    
    deleteR("DELETE FROM `packages_boissons` WHERE `id_packages` = ?", [$frm_data['id']], 'i');
    deleteR("DELETE FROM `packages_plats` WHERE `id_packages` = ?", [$frm_data['id']], 'i');

    $res = deleteR("DELETE FROM `packages` WHERE `id`=? ", [$frm_data['id']], 'i');

    if($res) {
        echo 1;
    }else{
        echo 0;
    }
}
?>