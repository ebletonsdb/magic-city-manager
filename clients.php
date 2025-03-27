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

	<title>Magic City | Clients</title>
</head>

<body>
	<?php
		$count_clients = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `clients`"));
		
		$count_clients_simple = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `clients` WHERE `type`='Anniverssaire'"));
		
		$count_clients_org = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `clients` WHERE `type`='Autres'"));

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
			<li class="active">
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
					<h1>Clients</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="clients.php">Clients</a>
						</li>
					</ul>
				</div>
			</div>

 			<ul class="box-info">
				<li>
					<i class='bx bx-id-card'></i>
					<span class="text">
						<p>Total</p>
						<h3><?php echo $count_clients['count'] ?></h3>
					</span>
				</li>
				<li>
					<i class='bx bx-user'></i>
					<span class="text">
						<p>Anniversaires</p>
						<h3><?php echo $count_clients_simple['count'] ?></h3>
					</span>
				</li>
				<li>
					<i class='bx bx-buildings'></i>
					<span class="text">
						<p>Autres</p>
						<h3><?php echo $count_clients_org['count'] ?></h3>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<h3>Liste des Clients</h3><hr>
					<div class="head">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add_clients">
							<i class="bi bi-plus-square m-1"></i>AJOUTER
						</button>
						<input type="text" oninput="get_all_clients(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher Client ici...">
					</div>
					<div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover border text-center">
                                    <thead>
                                        <tr class="bg-dark">
                                            <th scope="col" class="bg-dark text-light">#</th>
                                            <th scope="col" class="bg-dark text-light">Nom commplet</th>
                                            <th scope="col" class="bg-dark text-light">Type</th>
                                            <th scope="col" class="bg-dark text-light">Addresse</th>
                                            <th scope="col" class="bg-dark text-light">Phone</th>
                                            <th scope="col" class="bg-dark text-light">Date Naissance</th>
                                            <th scope="col" class="bg-dark text-light">Date</th>
											<?php 
												if($_SESSION['type'] == "utilisateur"){
													
												}else{
													echo "<th scope='col' class='bg-dark text-light'>Action</th>";

												}
											?>
                                        </tr>
                                    </thead>
                                    <tbody id="clients-data">
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

	<!-- Add client Modal -->
	<div class="modal fade" id="add_clients" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="add_clients_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Ajouter Client</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="typeClient" class="form-label fw-bold">Type Client</label>
								<select required id="typeClient" name="type" class="form-select form-control shadow-none">
									<option value="" disabled selected>Choisir le type</option>
										<option value='Anniversaires'>Anniversaire</option>
										<option value='Général'>Général</option>
								</select>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Nom Complet</label>
								<input required type="text" name="nom" class="form-control shadow-none" placeholder="Entrer nom complet client ici...">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Addresse</label>
								<input required type="text" name="addresse" class="form-control shadow-none" placeholder="Entrer addresse client ici...">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Phone</label>
								<input required type="number" name="phone" class="form-control shadow-none" placeholder="Entrer phone client ici...">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Naissance</label>
								<input type="date" name="dob" class="form-control shadow-none">
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
 
	<!-- edit client Modal -->
	<div class="modal fade" id="edit_clients" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="edit_clients_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Modifier Client</h5>
					</div>
					<div class="modal-body"> 
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Type Client</label>
								<input required type="text" name="type" class="form-control shadow-none" readonly>
							</div> 
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Nom Complet</label>
								<input required type="text" name="nom" class="form-control shadow-none">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Addresse</label>
								<input required type="text" name="addresse" class="form-control shadow-none">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Phone</label>
								<input required type="number" name="phone" class="form-control shadow-none">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold">Naissance</label>
								<input type="date" name="dob" class="form-control shadow-none" readonly>
							</div>
							<input type="hidden" name="id">
						</div>
					</div>
					<div class="modal-footer">
						<button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
		</div>
	</div>	


	<script src="js/scripts.js"></script>
	<script src="scripts/clients.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</body>

</html>