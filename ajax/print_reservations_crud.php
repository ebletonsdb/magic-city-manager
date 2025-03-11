<?php

    require('../inc/essentials.php');
    require('../inc/db_config.php');
    require '../inc/vendor/autoload.php';
    use Dompdf\Dompdf;
    use Dompdf\Options;
    adminLogin();

    // Configuration de Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

// Vérifier si les données sont envoyées
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $id = $_POST["id"];
//     $date_now = date("d/m/Y");

    // Exemple de données (remplace avec celles de ta base de données)
    // $items = [
    //     ["quantite" => 1, "description" => "Pizza medium (Poulet)", "prix" => 1650, "montant" => 1650],
    //     ["quantite" => 1, "description" => "Pizza large (Poulet)", "prix" => 2050, "montant" => 2050],
    //     ["quantite" => 10, "description" => "Brochette de bœuf", "prix" => 1000, "montant" => 10000],
    //     ["quantite" => 10, "description" => "Wings", "prix" => 1000, "montant" => 10000],
    //     ["quantite" => 1, "description" => "Champagne JP Chenet", "prix" => 4500, "montant" => 4500],
    //     ["quantite" => 1, "description" => "Vin rouge", "prix" => 4000, "montant" => 4000],
    //     ["quantite" => 10, "description" => "Jus naturel", "prix" => 300, "montant" => 3000],
    //     ["quantite" => 10, "description" => "7 UP", "prix" => 200, "montant" => 2000],
    //     ["quantite" => 1, "description" => "Frais de réservation", "prix" => 10000, "montant" => 10000]
    // ];

    // $total = array_sum(array_column($items, "montant"));

    // Contenu HTML du PDF
    // $html = "
    // <h2 style='text-align: center;'>MAGIK GRILL & BBQ</h2>
    // <p style='text-align: center;'>Adresse : Rue 28 Carénage & Boulevard</p>
    // <p style='text-align: center;'>magiccityfunparkcap@gmail.com | Tél : +509 4607-8690</p>
    
    // <h3 style='text-align: center;'>FACTURE</h3>
    // <p>Date : $date_now</p>
    // <p>Nom du Client : <b>Ebsonde Norcilien</b></p>
    // <p>Téléphone : <b>44935205</b></p>

    // <table border='1' cellspacing='0' cellpadding='8' width='100%'>
    //     <thead>
    //         <tr>
    //             <th>Quantité</th>
    //             <th>Description</th>
    //             <th>Prix Unitaire (Gdes)</th>
    //             <th>Montant (Gdes)</th>
    //         </tr>
    //     </thead>
    //     <tbody>";

    // foreach ($items as $item) {
    //     $html .= "<tr>
    //         <td>{$item['quantite']}</td>
    //         <td>{$item['description']}</td>
    //         <td>{$item['prix']}</td>
    //         <td>{$item['montant']}</td>
    //     </tr>";
    // }

    // $html .= "</tbody>
    // </table>
    // <p style='text-align: right;'><b>Total en gourdes : $total</b></p>
    // <p style='text-align: right;'><b>Versement : $total</b></p>
    // <p style='text-align: right;'><b>Balance : 0</b></p>

    // <h4>Nombre de personnes : 20</h4>
    // <p>Date événement : 27/Février/2025</p>
    // <p>Heure de début : 4h00 | Heure de fin : 7h00</p>

    // <p style='text-align: right; margin-top: 50px;'>Signature autorisée</p>
    // <p style='text-align: right;'><b>Directrice Magik Grill</b></p>
    // ";

    $html = "<h1>Votre contenu HTML ici</h1>";

    // Charger le HTML et générer le PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Créer le PDF dans une variable sans l'enregistrer sur le serveur
    $pdfOutput = $dompdf->output();

    $filePath = "pdfs/reservation_" . time() . ".pdf";  // Exemple de dossier où le fichier est enregistré

    // Enregistrer le PDF sur le serveur
    file_put_contents($filePath, $pdfOutput);

    // Retourner l'URL du fichier PDF dans la réponse JSON
    echo json_encode(["file" => $filePath]);
// }
?>
