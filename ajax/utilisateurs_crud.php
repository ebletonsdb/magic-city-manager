<?php
    require('../inc/db_config.php');
    require('../inc/essentials.php');
    date_default_timezone_set('America/Port-au-Prince');

    // add utilisateurs
    if(isset($_POST['add_utilisateurs'])){
        $frm_data = filteration($_POST);

        // mot de passse different 
        if($frm_data['mdp'] != $frm_data['cmdp']){
            echo'pass_missmatch';
            exit;
        }

        //check utilisateur existe 
        $u_exist = select("SELECT * FROM `utilisateurs` WHERE `email`=? OR `phone`=? LIMIT 1",
        [$frm_data['email'],$frm_data['phone']],"ss");
        
        if(mysqli_num_rows($u_exist) != 0){
            $u_exist_fetch = mysqli_fetch_assoc($u_exist);
            echo ($u_exist_fetch["email"] == $frm_data["email"]) ?"email_already":"phone_already";
            exit;
        }

        // envoyer confirmation link par email pour l'utilisateur 
        $token = bin2hex(random_bytes(16));
        // if(!send_mail($frm_data['email'],$token,"email_confirmation")){
        //     echo 'mail_failed';
        //     exit;
        // }

        $enc_mdp = password_hash($frm_data['mdp'], PASSWORD_BCRYPT);

        $query = "INSERT INTO `utilisateurs`(`nom`, `email`, `phone`, `addresse`, `mdp`, `type`, `token`) VALUES (?,?,?,?,?,?,?)";
        $values = [$frm_data['nom'], $frm_data['email'], $frm_data['phone'], $frm_data['addresse'], $enc_mdp, $frm_data['type'], $token];
        $res = insert($query, $values, 'sssssss');
        echo $res;
    }
    
    // get all utilisateurs

    if(isset($_POST['get_all_utilisateurs'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `utilisateurs` WHERE (type LIKE ? OR nom LIKE ? OR addresse LIKE ? OR phone LIKE ? OR email LIKE ? OR `type` LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'ssssss');
        $i = 1;
        $data = "";

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit;
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $type = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'); 
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8'); 
                $addresse = htmlspecialchars($row['addresse'], ENT_QUOTES, 'UTF-8'); 
                $phone = htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); 
                $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); 
                // $verifier = htmlspecialchars($row['verifier'], ENT_QUOTES, 'UTF-8'); 
                $date = date("Y-m-d", strtotime($row["datetime"]));
                
                $del_btn ="<button type='button' onclick='remove_utilisateurs($id)' class='btn btn-danger  btn-sm m-1'>
                    <i class='bi bi-trash '></i>
                </button>";

                $modify_btn = "<button type='button' onclick='edit_utilisateurs($id)' class='btn btn-primary btn-sm m-1' data-bs-toggle='modal' data-bs-target='#edit_utilisateurs'>
                    <i class='bi bi-pencil-square '></i>
                </button>";

                $verifier = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
                if($row["verifier"]){
                    $verifier = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
                }

                $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
                if(!$row["statut"]){
                    $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
                }

                $data .="
                    <tr class='align-middle text-center text-s'>
                        <td>$i</td>
                        <td>$nom</td>
                        <td>$type</td>
                        <td>$email</td>
                        <td>$addresse</td>
                        <td>$phone</td>
                        <td>$status</td>
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
            echo "<p class='m-2'>Aucun utilisateurs disponible.</p>";
        }
    }

    if(isset($_POST["toggle_status"])){
        $frm_data = filteration($_POST);

        $q = "UPDATE `utilisateurs` SET `statut`=? WHERE `id`=?";
        $v = [$frm_data['value'], $frm_data['toggle_status']];

        if(update($q, $v, 'ii')){
            echo 1;
        }else{
            echo 0;
        }
    }

    // get utilisateurs
    if(isset($_POST["get_utilisateurs"])){
        $frm_data = filteration($_POST);

        $res = select("SELECT * FROM `utilisateurs` WHERE `id`=?", [$frm_data['get_utilisateurs']], 'i');

        $utilisateursdata = mysqli_fetch_assoc($res);

        $data = ["utilisateursdata" => $utilisateursdata];
        $data = json_encode($data);
        echo $data;
    }

    // edit utilisateurs
    if(isset($_POST["edit_utilisateurs"])){
        $frm_data = filteration($_POST);

        // 
        $q_check_email = "SELECT COUNT(*) AS count FROM `utilisateurs` WHERE `email` = ? AND `id` != ?";
        $values_check_email = [$frm_data['email'], $frm_data['id']];
        
        $res_check_email = select($q_check_email, $values_check_email, 'si');
        $row_check_email = mysqli_fetch_assoc($res_check_email);

        // 
        $q_check_phone = "SELECT COUNT(*) AS count FROM `utilisateurs` WHERE `phone` = ? AND `id` != ?";
        $values_check_phone = [$frm_data['phone'], $frm_data['id']];
        
        $res_check_phone = select($q_check_phone, $values_check_phone, 'si');
        $row_check_phone = mysqli_fetch_assoc($res_check_phone);
        
        if($frm_data['mdp'] != $frm_data['cmdp']){
            echo("mdp_dif");
        }else{
            if ($row_check_email['count'] > 0) {
                echo "email_existe";
            } elseif ($row_check_phone['count'] > 0) {
                echo "phone_existe";
            } else {
                $enc_mdp = password_hash($frm_data['cmdp'], PASSWORD_BCRYPT);

                $q = "UPDATE `utilisateurs` SET `type`=?, `nom`=?, `email`=?, `addresse`=?,`phone`=?,`mdp`=? WHERE `id`=?";
                $values = [$frm_data['type'], $frm_data['nom'], $frm_data['email'], $frm_data['addresse'], $frm_data['phone'], $enc_mdp, $frm_data['id']];
            
                $res = update($q, $values, 'ssssssi');
                echo $res;
            }
        }
    }
    
    // remove utilisateurs
    
    if (isset($_POST['remove_utilisateurs'])) {
        $frm_data = filteration($_POST);

        $res = deleteR("DELETE FROM `utilisateurs` WHERE `id`=? ", [$frm_data['utilisateurs_id']], 'i');

        if($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

    if(isset($_POST['forgot_pass'])){
        $data = filteration($_POST);

        $u_exist = select('SELECT * FROM `user_crud` WHERE `email`=? LIMIT 1', [$data['email']],'s');

        if(mysqli_num_rows($u_exist) == 0){
            echo 'inv_email';
        }else{
            $u_fetch = mysqli_fetch_assoc($u_exist);
            if($u_fetch['is_verified']==0){
                echo 'not_verified';
            }else if($u_fetch['status']==0){
                echo 'inactive';
            }else{
                // send reset link to email
                $token = bin2hex(random_bytes(16));

                if(!send_mail($data['email'], $token, "account_recovery")){
                    echo "mail_failed";
                }else{
                    $date = date("Y-m-d");
                    $query = mysqli_query($con, "UPDATE `user_crud` SET `token`='$token', `t_expire`='$date' WHERE `id`='$u_fetch[id]'");
                    
                    if($query){
                        echo 1;
                        echo "<script>console.log('$data[email], $token, $date')</script>";
                    }else{
                        echo "upd_failed";
                    }
                }
            }
        }
    }

    if(isset($_POST['recovery_utilisateurs'])){
        $data = filteration($_POST);

        $en_pass = password_hash($data['mdp'], PASSWORD_BCRYPT);

        $query = "UPDATE `utilisateurs` SET `mdp`=?, `token`=?, `t_expire`=?
            WHERE `email`=? AND `token`=?";
        $values = [$en_pass, null, null, $data['email'], $data['token']];

        if(update($query, $values,'sssss')){
            echo 1;
        }else{
            echo 'failed';
        }
    }
?>
