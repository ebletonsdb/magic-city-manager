<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../inc/db_config.php');
    require('../inc/essentials.php'); 
    require('../inc/vendor/autoload.php');

    use Spipu\Html2Pdf\Html2Pdf;

    adminLogin();

if (isset($_POST['print_rapports'])) {
    $frm_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = mysqli_real_escape_string($con, $frm_data['id']);

    // Récupérer les détails du rapport
    $query1 = "SELECT * FROM `rapports` WHERE id = '$id'";
    $rapports = mysqli_fetch_assoc(mysqli_query($con, $query1));

    if (!$rapports) {
        die("Erreur : Rapport introuvable !");
    }

    $date_debut = date("Y-m-d", strtotime($rapports["date_debut"]));
    $date_fin = date("Y-m-d", strtotime($rapports["date_fin"]));
    $date = date("d-m-Y", strtotime($rapports["datetime"]));

    $query2 = "SELECT * FROM reservations WHERE datetime BETWEEN '$date_debut' AND '$date_fin'";
    $result = mysqli_query($con, $query2);
    $reservations = mysqli_fetch_all($result, MYSQLI_ASSOC);

    ob_start();
    ?>

    <style>
        table {
            width: 70%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: none;
            padding: 10px;
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .img{
            width: 250px;
            margin-bottom: -30px;
        }
        .header p{
            font-size: 18px;
        }
        .signature {
            text-align: right;
            margin-top: 50px;
        }
        .ligne, hr{
            width: 100%;
            height: 1px;
            border-bottom: 1px solid silver;
        }
        .position{
            width: 100%;
            display: flex;
            justify-content: center;
            padding-left: 370px;
        }
        .tab {
            width: 100%;
            border-collapse: collapse;
        }
        .tab th, .tab td {
            border: 1px solid silver;
            padding: 8px;
            text-align: center;
        }
    </style>

    <div class="header">
        <img src="../img/logo_magic.png" class="img">
        <p>Adresse : Rue 28 Carénage & Boulevard<br>magiccityfunparkcap@gmail.com<br>Tél : +509 4607-8690</p>
        <hr>
        <h3>RAPPORT</h3>
    </div>

    <p>Date : <b><?= htmlspecialchars($date) ?></b></p>
    <p>Commence le : <b><?= htmlspecialchars(date("d-m-Y", strtotime($date_debut))) ?></b></p>
    <p>Finir le : <b><?= htmlspecialchars(date("d-m-Y", strtotime($date_fin))) ?></b></p>
    <br><div class="ligne"></div>

    <table>
        <thead>
            <tr>
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
        <tbody>
            <?php $i = 1;
            foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($reservation['client']) ?></td>
                    <td><?= htmlspecialchars($reservation['package']) ?></td>
                    <td><?= htmlspecialchars($reservation['statut']) ?></td>
                    <td><?= number_format($reservation['montant'], 2) ?> gdes</td>
                    <td><?= number_format($reservation['versement'], 2) ?> gdes</td>
                    <td><?= number_format($reservation['balance'], 2) ?> gdes</td>
                    <td><?= htmlspecialchars($reservation['heure_r']) ?></td>
                    <td><?= htmlspecialchars(date("d-m-Y", strtotime($reservation["date_r"]))) ?></td>
                    <td><?= htmlspecialchars(date("d-m-Y", strtotime($reservation["datetime"]))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php
    $montant_total = array_sum(array_column($reservations, 'montant'));
    $versement_total = array_sum(array_column($reservations, 'versement'));
    $balance_total = array_sum(array_column($reservations, 'balance'));
    ?>

    <!-- <h4 style="float:right; padding: 10px; border: 1px solid silver;">Bilan des transactions</h4> -->
    <div class="position">
        <table class="tab">
            <thead>
                <tr>
                    <th>MONTANT TOTAL</th>
                    <th>VERSEMENT TOTAL</th>
                    <th>BALANCE TOTALE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= number_format($montant_total, 2) ?> gdes</td>
                    <td><?= number_format($versement_total, 2) ?> gdes</td>
                    <td><?= number_format($balance_total, 2) ?> gdes</td>
                </tr>
            </tbody>
        </table>
    </div><br>
    <p class="signature">Signature autorisée <br> <b>Magic City Fun Park</b></p>

    <?php
    $html = ob_get_clean();

    try {
        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML($html);
        $fileName = "Rapport_{$date_debut}_{$date_fin}.pdf";
        $html2pdf->output($fileName, 'I');
    } catch (Exception $e) {
        die("Erreur lors de la génération du PDF : " . $e->getMessage());
    }
    exit;
} else {
    die("Erreur : Requête invalide !");
}

?>
