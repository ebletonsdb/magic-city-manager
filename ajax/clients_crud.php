<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 

    // add client 

    if(isset($_POST['add_clients'])){
        $frm_data = filteration($_POST);

        $client_exist = select("SELECT * FROM `clients` WHERE `nom`=? OR `phone`=? LIMIT 1",
        [$frm_data['nom'],$frm_data['phone']],"ss");
        
        if(mysqli_num_rows($client_exist) != 0){
            $client_exist_fetch = mysqli_fetch_assoc($client_exist);
            echo ($client_exist_fetch["nom"] == $frm_data["nom"]) ?"nom_already":"phone_already";
            exit;
        }

        $query = "INSERT INTO `clients`(`type`, `nom`, `addresse`, `phone`, `dob`) VALUES (?,?,?,?,?)";
        $values = [$frm_data['type'], $frm_data['nom'], $frm_data['addresse'], $frm_data['phone'], $frm_data['dob']];
        $res = insert($query, $values, 'sssss');
        echo $res;
    }
     
    // get all client
    if(isset($_POST['get_all_clients'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `clients` WHERE (type LIKE ? OR nom LIKE ? OR addresse LIKE ? OR phone LIKE ? OR dob LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'sssss');
        $i = 1;
        $data = "";

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit; 
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $type = ucwords(htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8')); 
                $nom = ucwords(htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8')); 
                $addresse = ucwords(htmlspecialchars($row['addresse'], ENT_QUOTES, 'UTF-8')); 
                $phone = htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); 
                $dob = htmlspecialchars($row['dob'], ENT_QUOTES, 'UTF-8'); 
                $date = date("Y-m-d", strtotime($row["datetime"]));
                
                $del_btn = '';
                $modify_btn = '';

                if($_SESSION['type'] == "utilisateur"){
					$del_btn .= '';
				}else{
                    $del_btn .="<button type='button' onclick='remove_client($id)' class='btn btn-sm btn-danger'>
                        <i class='bi bi-trash '></i>
                    </button>";
 
                    $modify_btn .= "<button type='button' onclick='edit_clients($id)' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#edit_clients'>
                        <i class='bi bi-pencil-square '></i>
                    </button>";
                }

                $data .="
                    <tr class='align-middle'>
                        <td>$i</td> 
                        <td>$nom</td>
                        <td>$type</td>
                        <td>$addresse</td>
                        <td>$phone</td>
                        <td>$dob</td>
                        <td>$date</td>
                        <td>
                            $modify_btn 
                            $del_btn
                        </td>
                    </tr>
                ";
                $i++;
            }
            echo $data;
        } else {
            echo "<p class='m-2'>Aucun Client disponible.</p>";
        }
    }

    // get client
    if(isset($_POST["get_clients"])){
        $frm_data = filteration($_POST);

        $res = select("SELECT * FROM `clients` WHERE `id`=?", [$frm_data['get_clients']], 'i');

        $clientsdata = mysqli_fetch_assoc($res);

        $data = ["clientsdata" => $clientsdata];
        $data = json_encode($data);
        echo $data;
    }

    // edit client
    if(isset($_POST["edit_clients"])){
        $frm_data = filteration($_POST);

        $q = "UPDATE `clients` SET `nom`=?,`type`=?,`addresse`=?,`phone`=?,`dob`=? WHERE `id`=?";
        $values = [$frm_data['nom'], $frm_data['type'], $frm_data['addresse'], $frm_data['phone'], $frm_data['dob'], $frm_data['id']];
       
        $res = update($q, $values, 'sssisi');
        echo $res;
    }
    
    // remove client
    if (isset($_POST['remove_client'])) {
        $frm_data = filteration($_POST);

        $res = deleteR("DELETE FROM `clients` WHERE `id`=? ", [$frm_data['client_id']], 'i');

        if($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

?>