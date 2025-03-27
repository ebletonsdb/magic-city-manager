<?php
    require('inc/essentials.php');
    require('inc/db_config.php');

    session_start();
    if((isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
        header("Location: dashboard.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Connection</title>
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" href="css/styles_connecter.css">

</head>
<style>
    div.login-form{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 400PX;
    }
    .custom-alert{
        position: fixed;
        top: 25px;
        right: 25px;
    }
</style>
<body class="bg-light">

    <img class="wave" src="img/wave.png">
	<div class="container">
		<div class="img">
			<img src="img/bg.svg">
		</div>
		<div class="login-content">
			<form method="POST">
				<img src="img/logo.png" class="mb-5">
           		<div class="input-div one">
           		   <div class="i">
           		   		<i class="bx bx-envelope"></i>
           		   </div>
           		   <div class="div">
                        <input name="email" required type="text" class="form-control shadow-none" placeholder="Email" autocomplete>
                    </div>
           		</div>
           		<div class="input-div pass">
           		   <div class="i"> 
           		    	<i class="bx bx-lock"></i>
           		   </div>
                      <div class="div">
                        <input name="mdp" required type="password"  placeholder="Mot de passe" autocomplete>
                    </div>
            	</div>
                <button name="login" type="submit" class="btn text-white custom-bg shadow-none">CONNECTER</button>
            </form>
        </div>
    </div>

    <?php
        if(isset($_POST["login"])){
            $frm_data = filteration($_POST);

            $query = "SELECT * FROM utilisateurs WHERE email=?";
            $values = [$frm_data['email']];

            $res = select($query, $values, 's');

            if($res && $res->num_rows == 1){
                $row = $res->fetch_assoc();

                if (password_verify($frm_data['mdp'], $row['mdp'])) {
                    if ($row['statut'] == 0) {
                        echo"<script>alert('Votre compte a été désactivé, veuillez contacter l\'administrateur.')</script>";
                    } else {
                        $_SESSION['adminLogin'] = true;
                        $_SESSION['adminId'] = $row['id'];
                        $_SESSION['nom'] = $row['nom'];
                        $_SESSION['type'] = $row['type'];

                        $date_now = date("Y-m-d H:i:s");
                        $date_connexion = date("Y-m-d");

                        $session_exist = select("SELECT * FROM `sessions_utilisateurs` WHERE `utilisateur_id`=? AND DATE(`heure_connexion`)=? LIMIT 1",
                        [$row['id'],$date_connexion],"ss");
                        
                        if ($session_exist && $session_exist->num_rows > 0) {
                            $session_exist_fetch = $session_exist->fetch_assoc();
                            
                            $update_result = update("UPDATE `sessions_utilisateurs` SET `heure_deconnexion`=?, `statut` = ? WHERE `utilisateur_id`=? AND Date(`heure_connexion`)=?", 
                                ["-", "En Ligne", $session_exist_fetch['utilisateur_id'], $date_connexion], "ssis");
                            
                            if ($update_result) {
                                $_SESSION['date_connexion'] = $session_exist_fetch['heure_connexion'];
                                header("Location: dashboard.php");
                                exit();
                            }
                        } else {
                            $insert_result = insert("INSERT INTO `sessions_utilisateurs` (utilisateur_id, heure_connexion, statut) VALUES (?, ?, ?)", 
                                [$row['id'], $date_now, "En Ligne"], "sss");
                            
                            if ($insert_result) {
                                $_SESSION['date_connexion'] = $session_exist_fetch['heure_connexion'];
                                header("Location: dashboard.php");
                                exit();
                            }
                        }
                        
                    }
                } else {
                    echo"<script>alert('Erreur de connexion - mot de passe incorrect !')</script>";
                }
            } else {
                echo"<script>alert('Erreur de connexion - Email incorrect !')</script>";
            }
        }
    ?>

    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous">
	</script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const inputs = document.querySelectorAll(".input");

        function addcl(){
            let parent = this.parentNode.parentNode;
            parent.classList.add("focus");
        }

        function remcl(){
            let parent = this.parentNode.parentNode;
            if(this.value == ""){
                parent.classList.remove("focus");
            }
        }


        inputs.forEach(input => {
            input.addEventListener("focus", addcl);
            input.addEventListener("blur", remcl);
        });
    </script>
</body>
</html>