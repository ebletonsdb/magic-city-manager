<?php 

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin();  

    // add rapport 

    if(isset($_POST['add_rapports'])){
        $frm_data = filteration($_POST);

        $rapports_exist = select("SELECT COUNT(*) AS total 
              FROM rapports 
              WHERE (? BETWEEN date_debut AND date_fin) 
                OR (? BETWEEN date_debut AND date_fin) 
                OR (date_debut BETWEEN ? AND ?) 
                OR (date_fin BETWEEN ? AND ?)",
        [
            $frm_data['date_debut'], 
            $frm_data['date_fin'], 
            $frm_data['date_debut'], 
            $frm_data['date_fin'],
            $frm_data['date_debut'], 
            $frm_data['date_fin']
        ],"ssssss");
        
        $rapports_exist_fetch = mysqli_fetch_assoc($rapports_exist);

        if ($rapports_exist_fetch["total"] > 0) {
            echo "rapport_already"; 
            exit;
        }

        $query = "INSERT INTO `rapports`(`date_debut`, `date_fin`) VALUES (?,?)";
        $values = [$frm_data['date_debut'], $frm_data['date_fin']];
        $res = insert($query, $values, 'ss');
        echo $res;
    }
     
    // get all rapport
    if(isset($_POST['get_all_rapports'])){
        $frm_data = filteration($_POST);
        $query = "SELECT * FROM `rapports` WHERE (date_debut LIKE ? OR date_fin LIKE ? OR datetime LIKE ?) ORDER BY id DESC";
        $values = ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%"];
        $res = select($query, $values, 'sss');
        $i = 1;
        $data = "";

        if(mysqli_num_rows($res)==0){
            echo"<b> Aucun Donnee Disponible!</b>";
            exit; 
        }

        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){

                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
                $date_debut = date("Y-m-d", strtotime($row["date_debut"])); 
                $date_fin = date("Y-m-d", strtotime($row["date_fin"])); 
                $date = date("Y-m-d g:i:s A", strtotime($row["datetime"]));
                
                $del_btn ="<button type='button' onclick='remove_rapports($id)' class='btn btn-sm btn-danger'>
                    <i class='bi bi-trash fs-6'></i>
                </button>";

                $print_btn = "<button type='button' onclick='print_rapports($id)' class='btn btn-sm btn-success'>
                    <i class='bi bi-printer-fill fs-6'></i>
                </button>";

                $data .="
                    <tr class='align-middle'>
                        <td>$i</td> 
                        <td>$date_debut</td>
                        <td>$date_fin</td>
                        <td>$date</td>
                        <td>
                            $del_btn
                            $print_btn 
                        </td>
                    </tr>
                ";
                $i++;
            }
            echo $data;
        } else {
            echo "<p class='m-2'>Aucun rapport disponible.</p>";
        }
    }

    // pdf reservation
    if (isset($_POST['print_rapports'])) {
        $frm_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $id = mysqli_real_escape_string($con, $frm_data['id']);
    
        // Récupérer les détails du rapport
        $query1 = "SELECT * FROM `rapports` WHERE id = '$id'";
        $rapports = mysqli_fetch_assoc(mysqli_query($con, $query1));
    
        $date_debut = date("Y-m-d", strtotime($rapports["date_debut"])); 
        $date_fin = date("Y-m-d", strtotime($rapports["date_fin"]));
        $date = date("d-m-Y", strtotime($rapports["datetime"]));
    
        // Récupérer les réservations entre la période donnée
        $query2 = "SELECT * FROM reservations WHERE datetime BETWEEN '$date_debut' AND '$date_fin'";
        $result = mysqli_query($con, $query2);
        $reservations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
        // Initialiser Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
    
        $i = 1;
        $html = "
        <style>
            tr{
                font-size: 12px;
            }
        </style>
        <h1 style='text-align: center;'>MAGIK GRILL & BBQ</h1>
        <p style='text-align: center;'>Adresse : Rue 28 Carénage & Boulevard <br> magiccityfunparkcap@gmail.com <br> Tél : +509 4607-8690</p><hr>
        
        <h3 style='text-align: center;'>RAPPORT</h3>
        <p>Date : <b>{$date}</b></p>
        <p>Commence le : <b>" . date("d-m-Y", strtotime($date_debut)) . "</b></p>
        <p>Finir le : <b>" . date("d-m-Y", strtotime($date_fin)) . "</b></p><br>
    
        <table cellspacing='0' cellpadding='8' width='100%'>
            <thead>
                <tr style='border-bottom:1px solid silver'>
                    <th>#</th>
                    <th>Client</th>
                    <th>Package</th>
                    <th>Statut</th>
                    <th>Montant</th>
                    <th>Versement</th>
                    <th>Balance</th>
                    <th>Heure</th>
                    <th>Date événement</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";
    
        foreach ($reservations as $reservation) {
            $client = htmlspecialchars($reservation['client'], ENT_QUOTES, 'UTF-8');
            $package = htmlspecialchars($reservation['package'], ENT_QUOTES, 'UTF-8');
            $statut = htmlspecialchars($reservation['statut'], ENT_QUOTES, 'UTF-8');
            $montant = $reservation['montant'];
            $versement = $reservation['versement'];
            $balance = $reservation['balance'];
            $heure_r = htmlspecialchars($reservation['heure_r'], ENT_QUOTES, 'UTF-8');
            $date_r = date("d-m-Y", strtotime($reservation["date_r"]));
            $datetime = date("d-m-Y", strtotime($reservation["datetime"]));
    
            $html .= "<tr class='table1'>
                <td>$i</td>
                <td>$client</td>
                <td>$package</td>
                <td>$statut</td>
                <td>$montant gdes</td>
                <td>$versement gdes</td>
                <td>$balance gdes</td>
                <td>$heure_r</td>
                <td>$date_r</td>
                <td>$datetime</td>
            </tr>";
            $i++;
        }
    
        $html .= "</tbody></table>";
        $html .= "<h4 style='text-align: center; padding: 10px;border:1px solid silver;'>Bilan des transactions du rapport</h4>";
        $html .= "<table border='1' cellspacing='0' cellpadding='8' width='100%'>
            <thead>
                <tr>
                    <th>MONTANT TOTAL</th>
                    <th>VERSEMENT TOTAL</th>
                    <th>BALANCE TOTALE</th>
                </tr>
            </thead>
            <tbody>";
            $montant = 0;
            $versement = 0;
            $balance = 0;
            foreach ($reservations as $reservation) {
                $montant += $reservation['montant'];
                $versement += $reservation['versement'];
                $balance += $reservation['balance'];
            }
                
            $html .= "<tr style='text-align: center;'>
                <td>$montant gdes</td>
                <td>$versement gdes</td>
                <td>$balance gdes</td>
            </tr>
            </tbody></table>";
           

        $html .= "<p style='text-align: right; margin-top: 50px;'>Signature autorisée <br> <b>Magic City Fun Park</b></p>";
    
        // Charger le HTML et générer le PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        $fileName = "Rapport_{$date_debut}_{$date_fin}.pdf";
    
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        
        echo $dompdf->output();
        exit;
    }
    
    
    // remove rapport
    if (isset($_POST['remove_rapports'])) {
        $frm_data = filteration($_POST);

        $res = deleteR("DELETE FROM `rapports` WHERE `id`=? ", [$frm_data['rapports_id']], 'i');

        if($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

?>
