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
	<title>Magic City | Reservations</title>
</head>

<body>
	<?php

		$count_dob = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
		COUNT(*) AS count FROM clients 
		WHERE DAYOFYEAR(dob) IN (
		DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 1 DAY)), 
		DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 2 DAY)), 
		DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 3 DAY))
		)"));

	?>

	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="dashboard.php" class="brand">
			<img src="img/logo.png" class="m-5 mb-4">
		</a>
		<ul class="side-menu top">
			<li>
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
			<li class="active">
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
					<h1>Reservations</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.html">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="reservations.html">Reservations</a>
						</li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<?php
					$count_reservations = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        			FROM `reservations`"));
					$datenow = date("Y-m-d");

					$count_atente = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count_atente FROM reservations WHERE date_r >= '$datenow' AND balance > 0"))['count_atente'];
					$count_terminer = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count_terminer FROM reservations WHERE date_r < '$datenow' AND balance = 0"))['count_terminer'];
					$count_encours = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count_encours FROM reservations WHERE date_r = '$datenow' AND balance = 0"))['count_encours'];
				?>
				<li>
					<i class='bx bx-calendar-check'></i>
					<span class="text">
						<h3><?= $count_reservations['count'] ?></h3>
						<p>Réservations</p>
					</span>
				</li>
				<li>
					<i class='bx bx-hourglass'></i>
					<span class="text">
						<h3><?= $count_atente ?></h3>
						<p>En Attente</p>
					</span>
				</li>
				<li>
					<i class='bx bx-time-five' style="background: var(--light-yellow); color: var(--orange);"></i>
					<span class="text">
						<h3><?= $count_encours ?></h3>
						<p>En cours</p>
					</span>
				</li>
				<li>
					<i class='bx bx-check-circle' style="background: var(--light-blue);color: var(--blue);"></i>
					<span class="text">
						<h3><?= $count_terminer ?></h3>
						<p>Terminées</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<h3>Liste des Reservations</h3><hr>
					<div class="head">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add_reservations">
							<i class="bi bi-plus-square m-1"></i>AJOUTER
						</button>
						<input type="text" oninput="get_all_reservations(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher un reservation ici...">
					</div>
					<div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover border text-center">
                                    <thead>
                                        <tr class="bg-dark ">
                                            <th scope="col" class="bg-dark text-light">#</th>
                                            <th scope="col" class="bg-dark text-light text-center">Client</th>
                                            <th scope="col" class="bg-dark text-light text-center">Package</th>
                                            <th scope="col" class="bg-dark text-light text-center">Statut</th>
                                            <th scope="col" class="bg-dark text-light text-center">Montant</th>
                                            <th scope="col" class="bg-dark text-light text-center">Versement</th>
                                            <th scope="col" class="bg-dark text-light text-center">Balance</th> 
                                            <th scope="col" class="bg-dark text-light text-center">Heure </th>
                                            <th scope="col" class="bg-dark text-light text-center">Date événement</th>
                                            <th scope="col" class="bg-dark text-light text-center">Date</th>
                                            <th scope="col" class="bg-dark text-light text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reservations-data">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<!-- add reservation Modal -->
	<div class="modal fade" id="add_reservations" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="add_reservations_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Ajouter Reservation</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="select-search" class="form-label fw-bold">Nom Client</label>
								<select required id="select-search" name="nomclient" class="selectpicker form-select form-control shadow-none" data-live-search="true">
									<option value="" disabled selected>Choisir le nom ici</option>
									<?php
										$res = mysqli_query($con, "SELECT * FROM clients ORDER BY id DESC");
										while($opt = mysqli_fetch_assoc($res)){
											echo"
												<option value='$opt[nom]'>$opt[nom]</option>
											";
										}
									?>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label for="select-search" class="form-label fw-bold">Package</label>
								<select required id="select-search" name="package" class="selectpicker form-select form-control shadow-none" data-live-search="true">
									<option value="" disabled selected>Choisir le package ici</option>
									<?php
										$res = mysqli_query($con, "SELECT * FROM packages ORDER BY id DESC");
										while($opt = mysqli_fetch_assoc($res)){
											echo"
												<option value='$opt[nom]' data-prix='$opt[prix]'>$opt[nom]</option>
											";
										}
									?>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Date événement</label>
								<input required type="date"  name="date_r" class="form-control shadow-none">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Heure de l'événement</label>
								<input required type="text" name="heure_r" class="form-control shadow-none" placeholder="12h-5h">
							</div>
							<div class="col-md-6 mb-3">
								<label for="montant" class="form-label fw-bold">Montant en gourde</label>
								<input required type="number" id="montant" name="montant" class="form-control shadow-none" readonly placeholder="000.00 gourde">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Frais reservation</label>
								<input required type="number" name="f_reservation" class="form-control shadow-none" placeholder="000.00 gourde">
							</div>
							<div class="col-md-12 mb-3">
								<label class="form-label fw-bold">Versement en gourde</label>
								<input required type="number" name="versement" class="form-control shadow-none" placeholder="000.00 gourde">
							</div>
						</div>
						
					</div>
					<div class="modal-footer">
						<button type="reset" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- edit reservation Modal -->
	<div class="modal fade" id="edit_reservations" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="edit_reservations_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Modifier Reservation</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Nom Client</label>
								<input required type="text"  name="client" class="form-control shadow-none" readonly>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Package</label>
								<input required type="text"  name="package" class="form-control shadow-none" readonly>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Date événement</label>
								<?php
									if($_SESSION["type"] == "utilisateur"){
										echo "<input required type='date'  name='date_r' class='form-control shadow-none' disabled>";
									}else{
										echo "<input required type='date'  name='date_r' class='form-control shadow-none'>";
									}
								?>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Heure de l'événement</label>
								<?php
									if($_SESSION["type"] == "utilisateur"){
										echo "<input required type='text' name='heure_r' class='form-control shadow-none' readonly>";
									}else{
										echo "<input required type='text' name='heure_r' class='form-control shadow-none'>";
									}
								?>
								
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Versement en gourde</label>
								<input required type="number" name="versement" class="form-control shadow-none">
							</div>
								<?php
									if($_SESSION["type"] == "utilisateur"){
										echo "<div class='col-md-12'>
											<label class='form-label fw-bold text-danger'>Vous pouvez modifier seulement le versement</label>
										</div>";
									}else{
										echo "";
									}
								?>
								
						</div>
						<input type="hidden" name="id">
					</div>
					<div class="modal-footer">
						<button type="reset" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
		</div>
	</div>


	<script src="js/scripts.js"></script>
	<script src="scripts/reservations.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
		
	<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Select JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>
	<!-- generer PDF -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
			$('.bootstrap-select .bs-searchbox input').attr('style', 'outline: none !important; box-shadow: none !important; border: 1px solid #ccc;');
			
			$('.selectpicker').on('changed.bs.select', function() {
				var selectedOption = $(this).find('option:selected');
                var selectedPrice = selectedOption.data('prix');
                $('#montant').val(selectedPrice ? parseFloat(selectedPrice).toFixed(2) : '');
            });
        });

    </script>
</body>

</html>
