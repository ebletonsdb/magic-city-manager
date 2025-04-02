<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
require_once __DIR__ . '../inc/TCPDF/tcpdf.php';
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

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Magic City Fun Park');
    $pdf->SetTitle('Facture');
    $pdf->SetMargins(8, 8, 8);
    $pdf->AddPage();

    $imagePath = '../img/logo_magic.png';
    if (file_exists($imagePath)) {
        $pdf->Image($imagePath, 70, 10, 70, 50, 'PNG');
    }

    $pdf->Ln(48);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 5, "Adresse : Rue 28 Carénage & Boulevard", 0, 1, 'C');
    $pdf->Cell(0, 5, "magiccityfunparkcap@gmail.com", 0, 1, 'C');
    $pdf->Cell(0, 5, "+509 4607-8690", 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "FACTURE REÇUS", 0, 1, 'C');
    $pdf->Ln(5);

    // Reservation details
    $date = new DateTime($reservations['datetime']);
    $formattedDate = $date->format('d-m-Y h:i A');

    $pdf->SetFont('helvetica', '', 14);
    $pdf->Cell(0, 10, "Date : " . $formattedDate, 0, 1, 'L');
    $pdf->Cell(0, 10, "Nom du Client : " . $reservations['client'], 0, 1, 'L');
    $pdf->Cell(0, 10, "Téléphone : " . $clients['phone'], 0, 1, 'L');
    $pdf->Ln(10);

    // Line separator
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);

    // Add the table for items
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(50, 10, "Description", 0, 0, 'C');
    $pdf->Cell(40, 10, "Quantité", 0, 0, 'C');
    $pdf->Cell(40, 10, "Prix Unitaire (Gdes)", 0, 0, 'C');
    $pdf->Cell(40, 10, "Montant (Gdes)", 0, 1, 'C');

    $pdf->SetDrawColor(192, 192, 192);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
    $pdf->Ln(1);

    $pdf->SetFont('helvetica', '', 12);
    foreach ($package_plats as $plats) {
        $total_plats = $plats['prix'] * $plats['qte_plats'];
        $pdf->Cell(50, 10, $plats['nom'], 0, 0, 'C');
        $pdf->Cell(40, 10, $plats['qte_plats'], 0, 0, 'C');
        $pdf->Cell(40, 10, $plats['prix'], 0, 0, 'C');
        $pdf->Cell(40, 10, $total_plats, 0, 1, 'C');

        $pdf->SetDrawColor(192, 192, 192);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
        $pdf->Ln(1);
    }

    foreach ($package_boissons as $boissons) {
        $total_boissons = $boissons['prix'] * $boissons['qte_boissons'];
        $pdf->Cell(50, 10, $boissons['nom'], 0, 0, 'C');
        $pdf->Cell(40, 10, $boissons['qte_boissons'], 0, 0, 'C');
        $pdf->Cell(40, 10, $boissons['prix'], 0, 0, 'C');
        $pdf->Cell(40, 10, $total_boissons, 0, 1, 'C');

        $pdf->SetDrawColor(192, 192, 192);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
        $pdf->Ln(1);
    }

    $pdf->SetX(10); 
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 10, "TOTAL", 1, 0, 'C');
    $pdf->Cell(40, 10, "VERSEMENT", 1, 0, 'C');
    $pdf->Cell(40, 10, "BALANCE", 1, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 10, number_format($reservations['montant'], 0) . " gdes", 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($reservations['versement'], 0) . " gdes", 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($reservations['balance'], 0) . " gdes", 1, 1, 'C');

   // Signature
   $pdf->Ln(20);
   $pdf->SetFont('helvetica', 'I', 12);
   $pdf->Cell(0, 5, "Signature autorisée", 0, 1, 'R');
   $pdf->SetFont('helvetica', 'B', 12);
   $pdf->Cell(0, 5, "Magic City Fun Park", 0, 1, 'R');

    // Output the PDF
    $fileName = "Reservation_" . $reservations['client'] . ".pdf";
    // $pdf->Output($fileName, 'D');
    $pdf->Output(__DIR__ . '/' . $fileName, 'F');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    readfile(__DIR__ . '/' . $fileName);
    exit;

} else {
    die("Erreur : Requête invalide !");
}

?>
