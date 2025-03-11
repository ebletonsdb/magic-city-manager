<?php

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    // adminLogin(); 

    // Plats

    if(isset($_POST['add_plats'])){
        $frm_data = filteration($_POST);

        $plat_exist = select("SELECT * FROM `plats` WHERE `nom` = ? LIMIT 1", 
          [$frm_data['nom']], "s");

        if ($plat_exist && mysqli_num_rows($plat_exist) > 0) {
            echo "nom_already";
            exit;
        }

        $q = "INSERT INTO `plats`(`nom`, `prix`) VALUES (?,?)";
        $values = [$frm_data['nom'], $frm_data['prix']];
        $res = insert($q, $values, 'si');
        echo $res;
    }
    
    if(isset($_POST['get_all_plats'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `plats` WHERE (nom LIKE ? OR prix LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'ss');
        $i = 1;

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8'); 
                $prix = htmlspecialchars($row['prix'], ENT_QUOTES, 'UTF-8'); 
                echo <<<data
                    <tr>
                        <td>$i</td>
                        <td>$nom</td>
                        <td>$prix gdes</td>
                        <td>
                            <button type="button" onclick="remove_plats($row[id])" class="btn btn-danger btn-sm shadow-none">
                                <i class="bi bi-trash"></i>
                            </button>
                            <button type='button' onclick='edit_plats($row[id])' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#edit_plats'>
                                <i class='bi bi-pencil-square '></i>
                            </button>
                        </td>
                    </tr>
                data;
                $i++;
            }
        } else {
            echo "<p class='m-2'>Aucun Plat disponible.</p>";
        }
    }

    // get plats
    if(isset($_POST["get_plats"])){
        $frm_data = filteration($_POST);

        $res = select("SELECT * FROM `plats` WHERE `id`=?", [$frm_data['get_plats']], 'i');

        $platsdata = mysqli_fetch_assoc($res);

        $data = ["platsdata" => $platsdata];
        $data = json_encode($data);
        echo $data;
    }

     // edit plat
     if(isset($_POST["edit_plats"])){
        $frm_data = filteration($_POST);

        $q = "UPDATE `plats` SET `nom`=?,`prix`=? WHERE `id`=?";
        $values = [$frm_data['nom'], $frm_data['prix'], $frm_data['id']];
       
        $res = update($q, $values, 'sii');
        echo $res;
    }

    if (isset($_POST['remove_plats'])) {
        $frm_data = filteration($_POST);
        $values = [$frm_data['remove_plats']];
    
        $check_q = select('SELECT * FROM `packages_plats` WHERE `id_plats`=?', [$values], 'i');

        if(mysqli_num_rows($check_q) == 0){
            $q = "DELETE FROM `plats` WHERE `id` = ?";
            $res = deleteR($q, $values, "i");
            echo $res; 
        }else{
            echo "package_added";
        }
    }

    // Boissons
    if(isset($_POST['add_boissons'])){
        $frm_data = filteration($_POST);

        $boisson_exist = select("SELECT * FROM `boissons` WHERE `nom` = ? LIMIT 1", 
          [$frm_data['nom']], "s");

        if ($boisson_exist && mysqli_num_rows($boisson_exist) > 0) {
            echo "nom_already";
            exit;
        }

        $q = "INSERT INTO `boissons`(`nom`, `prix`) VALUES (?,?)";
        $values = [$frm_data['nom'], $frm_data['prix']];
        $res = insert($q, $values, 'si');
        echo $res;
    }

    if(isset($_POST['get_all_boissons'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `boissons` WHERE (nom LIKE ? OR prix LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'ss');
        $i = 1;

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8'); 
                $prix = htmlspecialchars($row['prix'], ENT_QUOTES, 'UTF-8'); 
                echo <<<data
                    <tr>
                        <td>$i</td>
                        <td>$nom</td>
                        <td>$prix gdes</td>
                        <td>
                            <button type="button" onclick="remove_boissons($row[id])" class="btn btn-danger btn-sm shadow-none">
                                <i class="bi bi-trash"></i>
                            </button>
                            
                            <button type='button' onclick='edit_boissons($row[id])' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#edit_boissons'>
                                <i class='bi bi-pencil-square '></i>
                            </button>
                        </td>
                    </tr>
                data;
                $i++;
            }
        } else {
            echo "<p class='m-2'>Aucun Boisson disponible.</p>";
        }
    }

    // get boisson
    if(isset($_POST["get_boissons"])){
        $frm_data = filteration($_POST);

        $res = select("SELECT * FROM `boissons` WHERE `id`=?", [$frm_data['get_boissons']], 'i');

        $boissonsdata = mysqli_fetch_assoc($res);

        $data = ["boissonsdata" => $boissonsdata];
        $data = json_encode($data);
        echo $data;
    }

    // edit plat
    if(isset($_POST["edit_boissons"])){
        $frm_data = filteration($_POST);

        $q = "UPDATE `boissons` SET `nom`=?,`prix`=? WHERE `id`=?";
        $values = [$frm_data['nom'], $frm_data['prix'], $frm_data['id']];
        
        $res = update($q, $values, 'sii');
        echo $res;
    }

    if (isset($_POST['remove_boissons'])) {
        $frm_data = filteration($_POST);
        $values = [$frm_data['remove_boissons']];
    
        $check_q = select('SELECT * FROM `packages_boissons` WHERE `id_boissons`=?', [$values], 'i');

        if(mysqli_num_rows($check_q) == 0){
            $q = "DELETE FROM `boissons` WHERE `id` = ?";
            $res = deleteR($q, $values, "i");
            echo $res; 
        }else{
            echo "package_added";
        }
    }
?>