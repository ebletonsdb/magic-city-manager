<?php
    require('inc/essentials.php');
    require('inc/db_config.php');
    adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once('inc/links.php'); ?>
	<title>Magic City | Accueil</title>
</head>
<body>

	<?php

		$count_reservations = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `reservations`"));

		$count_clients = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `clients`"));

		$somme_montant = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(versement) AS `somme` 
        FROM `reservations`"));

		$client_dob = mysqli_query($con, "SELECT 
			*, 
			DATE_FORMAT(CONCAT(YEAR(CURDATE()), '-', MONTH(dob), '-', DAY(dob)), '%d-%m-%Y') AS date_anniversaire
			FROM clients 
			WHERE DAYOFYEAR(dob) IN (
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 1 DAY)), 
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 2 DAY)), 
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 3 DAY))
		)");

		$count_dob = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
			COUNT(*) AS count FROM clients 
			WHERE DAYOFYEAR(dob) IN (
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 1 DAY)), 
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 2 DAY)), 
			DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 3 DAY))
		)"));
		
		$reservations = mysqli_query($con,"SELECT * FROM `reservations` ORDER BY id DESC limit 10");

	?>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="dashboard.php" class="brand">
			<img src="img/logo.png" class="m-5 mb-4">
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="dashboard.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Tableau de bord</span>
				</a>
			</li>
			<li>
				<a href="clients.php">
					<i class='bx bx-id-card' ></i>
					<span class="text">Clients</span>
				</a>
			</li>
			<li>
				<a href="reservations.php">
					<i class='bx bx-restaurant' ></i>
					<span class="text">Réservations</span>
				</a>
			</li>
			<?php
				if($_SESSION['type'] == "utilisateur"){
					echo "";
				}else{
					echo "<li>
						<a href='plats_boissons.php'>
							<i class='bx bx-food-menu'></i>
							<span class='text'>Plats et Boissons</span>
						</a>
					</li>";
				}
			?>
			<li>
				<a href="packages.php">
					<i class='bx bxs-package'></i>
					<span class="text">Packages</span>
				</a>
			</li>
			<?php
				if($_SESSION['type'] == "utilisateur"){
					echo "";
				}else{
					echo "
					<li>
						<a href='rapports.php'>
							<i class='bx bx-spreadsheet' ></i>
							<span class='text'>Rapports</span>
						</a>
					</li>
					
					<li>
						<a href='utilisateurs.php'>
							<i class='bx bxs-group' ></i>
							<span class='text'>Utilisateurs</span>
						</a>
					</li>
					<li>
						<a href='session_utilisateurs.php'>
							<i class='bx bxs-time-five' ></i>
							<span class='text'>Journal des Connexions</span>
						</a>
					</li>";
				}
			?>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<form action="#" class="d-flex justify-content-center">
				<span id="current-time" class="time"></span>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num"><?= $count_dob['count'] ?></span>
			</a>
			<a href="#" class="profile">
				<?=$_SESSION['nom']?>
				<button class="btn btn-sm btn-danger">
					<a href="deconnecter.php" class="text-white">
						<span class="text">Deconnecter</span>
					</a>
				</button>
			</a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Tableau de bord</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="dashboard.php">Accueil</a>
						</li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<h3><?= $count_reservations['count'] ?></h3>
						<p>Reservations</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3><?= $count_clients['count'] ?></h3>
						<p>Clients</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
						<h3><?= $somme_montant['somme']?> HTG</h3>
						<p>Solde Total</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Nouveau Reservation</h3>
					</div><hr>
					<table>
						<thead>
							<tr>
								<th>Client</th>
								<th>Date Reservation</th>
								<th>Date Evenement</th>
								<th>Statut</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while ($reservation = mysqli_fetch_assoc($reservations)) {
									$datetime = date('d-m-Y', strtotime($reservation["datetime"]));
									$date_r = date('d-m-Y', strtotime($reservation["date_r"]));

									$statut="";
									if($reservation['statut'] === "En attente"){
										$statut = "<span class='status pending'>En attente</span>";
									}elseif($reservation['statut'] === "Terminer"){
										$statut = "<span class='status completed'>Terminer</span>";
									}elseif($reservation['statut'] === "En cours"){
										$statut = "<span class='status process'>En cours</span>";
									}

									echo "<tr>
										<td>$reservation[client]</td>
										<td>$datetime</td>
										<td>$date_r</td>
										<td>$statut</td>
									</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Anniverssaires</h3>
					</div>
					<hr>
					<ul class="todo-list">
						<?php
							while ($client = mysqli_fetch_assoc($client_dob)) {
								$nom = htmlspecialchars($client['nom'], ENT_QUOTES, 'UTF-8'); 
								$dob = date('d-m-Y', strtotime($client["dob"]));
								$anniverssaire = date('d-m-Y', strtotime($client["date_anniversaire"]));

								echo "
									<li class='completed'>
										<p class='text-center'>$nom</p>
										<P>Nee : $dob</P>
										<P>Anniverssaire : $anniverssaire</P>
									</li>
								";
							}
						?>
					</ul>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	
	  <!-- mot de passe reset Modal et code -->
	  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="recovery_form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-shield-lock fs-3 me-2"></i> Configurer un nouveau mot de passe </h5>
          </div>
          <div class="modal-body">
            <div class="mb-4">
              <labe class="form-label">nouveau Mot de passe</label>
              <input type="password" name="mdp" class="form-control shadow-none">
              <input type="hidden" name="email">
              <input type="hidden" name="token">
            </div>
            <div class="mb-2 text-end">
              <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">ANNULER</button>
              <button type="submit" class="btn btn-dark shadow-none">ENVOYER</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

	<script src="js/scripts.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<script>
		let recovery_form =document.getElementById('recovery_form');
  
		recovery_form.addEventListener('submit', (e)=>{
			e.preventDefault();

			let data = new FormData();

			data.append('email',recovery_form.elements['email'].value);
			data.append('token',recovery_form.elements['token'].value);
			data.append('mdp',recovery_form.elements['mdp'].value);
			data.append('recovery_utilisateurs', '');

			
			var myModal = document.getElementById('recoveryModal');
			var modal = bootstrap.Modal.getInstance(myModal);
			modal.hide();

			let xhr = new XMLHttpRequest();
			xhr.open("POST", "ajax/utilisateurs_crud.php", true);

			xhr.onload = function() {
			if (this.responseText == 'failed') {
				alert('error', 'Echec de la réinitialisation du compte !');
			}else {
				alert('success','Réinitialisation du compte réussie !');
				recovery_form.reset();
			}
			}

			xhr.send(data);
		});
	</script>

</body>
</html>
