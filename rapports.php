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

	<title>Magic City | rapports</title>
</head>

<body>
	<?php
		// $count_clients = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        // FROM `clients`"));
		
		// $count_clients_simple = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        // FROM `clients` WHERE `type`='Anniverssaire'"));
		
		// $count_clients_org = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        // FROM `clients` WHERE `type`='Autres'"));

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
					echo "<li class='active'>
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
			<form action="#">
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
					<h1>Rapports</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="rapports.php">Rapports</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="table-data">
				<div class="order">
					<h3>Liste des Rapports</h3><hr>
					<div class="head">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add_rapports">
							<i class="bi bi-plus-square m-1"></i>Generer Rapport
						</button>
						<input type="text" oninput="get_all_rapports(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher rapport ici...">
					</div>
					<div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover border text-center">
                                    <thead>
                                        <tr class="bg-dark">
											<th scope="col" class="bg-dark text-light">#</th>
											<th scope="col" class="bg-dark text-light">Date de début</th>
											<th scope="col" class="bg-dark text-light">Date de fin</th>
											<th scope="col" class="bg-dark text-light">Date du rapport</th>
											<th scope="col" class="bg-dark text-light">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rapports-data">
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

	<!-- Add rapport Modal -->
	<div class="modal fade" id="add_rapports" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg ">
			<form id="add_rapports_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Generer rapport</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 d-flex">
								<label class="form-label fw-bold me-3">DU</label>
								<input type="date" name="date_debut" class="form-control shadow-none">
							</div>
							<div class="col-md-6 d-flex">
								<label class="form-label fw-bold me-3">AU</label>
								<input type="date" name="date_fin" class="form-control shadow-none">
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


	<script src="js/scripts.js"></script>
	<script src="scripts/rapportss.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</body>

</html>