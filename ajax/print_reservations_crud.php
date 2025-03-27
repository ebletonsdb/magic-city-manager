<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    require(__DIR__ . '/../inc/vendor/autoload.php');

    use Spipu\Html2Pdf\Html2Pdf;

    adminLogin();

if (isset($_POST['print_reservations'])) {
    $frm_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = mysqli_real_escape_string($con, $frm_data['id']);

    $query1 = "SELECT * FROM `reservations` WHERE id = '$id'";
    $reservations = mysqli_fetch_assoc(mysqli_query($con, $query1));

    $query2 = "SELECT id, qte_pers FROM `packages` WHERE nom = {$reservations['package']}";
    $packages = mysqli_fetch_assoc(mysqli_query($con, $query2));
    $id_package = $packages['id'];

    $query3 = "SELECT * FROM `clients` WHERE nom = '$reservations[client]'";
    $clients = mysqli_fetch_assoc(mysqli_query($con, $query3));

    $package_plats = select("SELECT pp.qte_plats, pl.id, pl.nom, pl.prix FROM packages_plats pp JOIN plats pl ON pp.id_plats = pl.id JOIN packages p ON pp.id_packages = p.id WHERE p.id=?", [$id_package], 'i');
    $package_boissons = select("SELECT pb.qte_boissons, bo.id, bo.nom, bo.prix FROM packages_boissons pb JOIN boissons bo ON pb.id_boissons = bo.id JOIN packages p ON pb.id_packages = p.id WHERE p.id=?", [$id_package], 'i');

    ob_start();
    ?>

    <style>
        .tab-one {
            width: 100%;
            border-collapse: collapse;
            /* font-size: 12px; */
        }
        .tab-one tr{
            font-size: 16px;
        }
        .tab-one th, .tab-one td {
            width: 25%;
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
            padding-left: 430px;
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
        <h3>FACTURE REÇUS</h3>
    </div>

    <p>Date : <b><?=$reservations['datetime']?></b></p>
    <p>Nom du Client : <b><?=$reservations['client']?></b></p>
    <p>Téléphone : <b><?=$clients ['phone']?></b></p><br>
    <br><div class="ligne"></div>

    <table class="tab-one">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix Unitaire (Gdes)</th>
                <th>Montant (Gdes)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($package_plats as $plats){
                $total_plats = $plats['prix']*$plats['qte_plats'];
            ?>
                <tr>
                    <td><?= $plats['nom'] ?></td>
                    <td><?= $plats['qte_plats'] ?></td>
                    <td><?= $plats['prix'] ?></td>
                    <td><?= $total_plats ?></td>
                </tr>
            <?php }
                foreach($package_boissons as $boissons){
                $total_boissons = $boissons['prix']*$boissons['qte_boissons'];
            ?>
                    <tr>
                        <td><?= $boissons['nom'] ?></td>
                        <td><?= $boissons['qte_boissons'] ?></td>
                        <td><?= $boissons['prix'] ?></td>
                        <td><?= $total_boissons ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <br><div class="ligne"></div>

    <div class="position">
        <table class="tab">
            <thead>
                <tr>
                    <th>TOTAL</th>
                    <th>VERSEMENT</th>
                    <th>BALANCE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= number_format($reservations['montant'], 2) ?> gdes</td>
                    <td><?= number_format($reservations['versement'], 2) ?> gdes</td>
                    <td><?= number_format($reservations['balance'], 2) ?> gdes</td>
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
        $fileName = "Reservation_$reservations[client].pdf";
        $html2pdf->output($fileName, 'I');
    } catch (Exception $e) {
        die("Erreur lors de la génération du PDF : " . $e->getMessage());
    }
    exit;
} else {
    die("Erreur : Requête invalide !");
}

?>
