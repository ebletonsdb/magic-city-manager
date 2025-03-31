<?php
    require('../inc/db_config.php');
    require('../inc/essentials.php');
    require('./inc/TCPDF/tcpdf.php');
    adminLogin();

    if (isset($_POST['print_rapports'])) {
        $frm_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = mysqli_real_escape_string($con, $frm_data['id']);

        $query1 = "SELECT * FROM `rapports` WHERE id = '$id'";
        $rapports = mysqli_fetch_assoc(mysqli_query($con, $query1));

        if (!$rapports) {
            die("Erreur : Rapport introuvable !");
        }

        $date_debut = date("Y-m-d", strtotime($rapports["date_debut"]));
        $date_fin = date("Y-m-d", strtotime($rapports["date_fin"]));
        $date = date("d-m-Y h:i A", strtotime($rapports["datetime"]));

        $query2 = "SELECT * FROM reservations WHERE datetime BETWEEN '$date_debut' AND '$date_fin'";
        $result = mysqli_query($con, $query2);
        $reservations = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Créer une nouvelle instance TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Magic City Fun Park');
        $pdf->SetTitle("Rapport du $date_debut au $date_fin");
        $pdf->SetMargins(8, 8, 8);
        $pdf->AddPage();
        

        // Ajout du logo
        $imagePath = '../img/logo_magic.png';
        if (file_exists($imagePath)) {
            $pdf->Image($imagePath, 70, 10, 70, 50, 'PNG');
        }

        // Titre et entête
        $pdf->Ln(48);
        $pdf->SetFont('helvetica', '', 14);
        $pdf->Cell(0, 5, "Rue 28 Carénage & Boulevard", 0, 1, 'C');
        $pdf->Cell(0, 5, "magiccityfunparkcap@gmail.com", 0, 1, 'C');
        $pdf->Cell(0, 5, "+509 4607-8690", 0, 1, 'C');
        $pdf->Ln(5);
        // trait
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
        $pdf->Ln(5); 
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, "RAPPORT", 0, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', '', 14);
        $pdf->Cell(0, 8, "Date : " . $date, 0, 1, 'R');
        $pdf->Cell(0, 8, "Commence le : " . date("d-m-Y", strtotime($date_debut)), 0, 1, 'L');
        $pdf->Cell(0, 8, "Finir le : " . date("d-m-Y", strtotime($date_fin)), 0, 1, 'L');
        $pdf->Ln(5);


        // Table des réservations
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(5, 8, "#", 0, 0, 'C');
        $pdf->Cell(35, 8, "Client", 0, 0, 'C');
        $pdf->Cell(15, 8, "Package", 0, 0, 'C');
        $pdf->Cell(17, 8, "Statut", 0, 0, 'C');
        $pdf->Cell(22, 8, "Montant", 0, 0, 'C');
        $pdf->Cell(22, 8, "Versement", 0, 0, 'C');
        $pdf->Cell(22, 8, "Balance", 0, 0, 'C');
        $pdf->Cell(15, 8, "Heure", 0, 0, 'C');
        $pdf->Cell(22, 8, "Date éven", 0, 0, 'C');
        $pdf->Cell(22, 8, "Date", 0, 1, 'C');
        
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 10);
        $i = 1;
        foreach ($reservations as $reservation) {
            $pdf->Cell(5, 8, $i++, 0, 0, 'C');
            $pdf->Cell(35, 8, htmlspecialchars($reservation['client']), 0, 0, 'C');
            $pdf->Cell(15, 8, htmlspecialchars($reservation['package']), 0, 0, 'C');
            $pdf->Cell(17, 8, htmlspecialchars($reservation['statut']), 0, 0, 'C');
            $pdf->Cell(22, 8, number_format($reservation['montant'], 0) . " gdes", 0, 0, 'C');
            $pdf->Cell(22, 8, number_format($reservation['versement'], 0) . " gdes", 0, 0, 'C');
            $pdf->Cell(22, 8, number_format($reservation['balance'], 0) . " gdes", 0, 0, 'C');
            $pdf->Cell(15, 8, htmlspecialchars($reservation['heure_r']), 0, 0, 'C');
            $pdf->Cell(22, 8, htmlspecialchars(date("d-m-Y", strtotime($reservation["date_r"]))), 0, 0, 'C');
            $pdf->Cell(22, 8, htmlspecialchars(date("d-m-Y", strtotime($reservation["datetime"]))), 0, 1, 'C');
            
            $pdf->SetDrawColor(192, 192, 192);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
            $pdf->Ln(1);
        }

        $montant_total = array_sum(array_column($reservations, 'montant'));
        $versement_total = array_sum(array_column($reservations, 'versement'));
        $balance_total = array_sum(array_column($reservations, 'balance'));

        $pdf->SetX(10); 
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(50, 10, "MONTANT TOTAL", 1, 0, 'C');
        $pdf->Cell(50, 10, "VERSEMENT TOTAL", 1, 0, 'C');
        $pdf->Cell(50, 10, "BALANCE TOTALE", 1, 1, 'C');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 10, number_format($montant_total, 2) . " gdes", 1, 0, 'C');
        $pdf->Cell(50, 10, number_format($versement_total, 2) . " gdes", 1, 0, 'C');
        $pdf->Cell(50, 10, number_format($balance_total, 2) . " gdes", 1, 1, 'C');

        // Signature
        $pdf->Ln(20);
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 5, "Signature autorisée", 0, 1, 'R');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 5, "Magic City Fun Park", 0, 1, 'R');

        // Sortie du PDF
        $fileName = "Rapport_{$date_debut}_{$date_fin}.pdf";
        $pdf->Output($fileName, 'D');
        exit;
    } else {
        die("Erreur : Requête invalide !");
    }
?>
