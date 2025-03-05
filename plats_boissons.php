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
	<title>Magic City | Plats & Boissons</title>
</head>

<body>
	<?php
		$count_plats = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `plats`"));
		
		$count_boissons = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        FROM `boissons`"));
		
		$total_prix_plats = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(prix) AS `total` 
        FROM `plats`"));
		
		$total_prix_boissons = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(prix) AS `total` 
        FROM `boissons`"));

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
					echo "<li class='active'>
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
			<!-- <li>
				<a href="#">
					<i class='bx bx-spreadsheet' ></i>
					<span class="text">Rapports</span>
				</a>
			</li> -->
			<?php
				if($_SESSION['type'] == "utilisateur"){
					echo "";
				}else{
					echo "<li>
						<a href='utilisateurs.php'>
							<i class='bx bxs-group' ></i>
							<span class='text'>Utilisateurs</span>
						</a>
					</li>";
				}
			?>
		</ul>
		<!-- <ul class="side-menu">
			<li>
				<a href="deconnecter.php" class="logout">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Deconnecter</span>
				</a>
			</li>
		</ul> -->
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
					<h1>Clients</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="plats_boissons.php">Plats & Boissons</a>
						</li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bx-food-menu'></i>
					<span class="text">
						<h3><?php echo $count_boissons['count'] + $count_plats['count']?></h3>
						<p>Plas & Boissons</p>
					</span>
				</li>
				<li>
					<i class='bx bx-restaurant'></i>
					<span class="text">
						<h3><?php echo $count_plats['count'] ?></h3>
						<p>Plats</p>
					</span>
				</li>
				<li>
					<i class='bx bx-drink'></i>
					<span class="text">
						<h3><?php echo $count_boissons['count'] ?></h3>
						<p>Boissons</p>
					</span>
				</li>
			</ul>

			<hr><h2 class="card-title mb-3">Liste des Plats disponible</h2>
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#plats">
							<i class="bi bi-plus-square m-1"></i>AJOUTER
						</button>
						<input type="text" oninput="get_all_plats(this.value)" class="form-control col-md-12 shadow-none w-25 ms-auto" placeholder="Rechercher plat ici...">
					</div>
					<div class="table-responsive-md" style="height: 360px; overflow-y: scroll;">
						<table class="table table-hover border">
							<thead>
								<tr class="bg-dark">
									<th scope="col" class="bg-dark text-light">#</th>
									<th scope="col" class="bg-dark text-light">Nom</th>
									<th scope="col" class="bg-dark text-light">Prix en gourdes</th>
									<th scope="col" class="bg-dark text-light">Action</th>
								</tr>
							</thead>
							<tbody id="plats-data">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<h2 class="card-title mb-3">Liste des Boissons disponible</h2>
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#boissons">
							<i class="bi bi-plus-square m-1"></i>AJOUTER
						</button>
						<input type="text" oninput="get_all_boissons(this.value)" class="form-control col-md-12 shadow-none w-25 ms-auto" placeholder="Rechercher boisson ici...">
					</div>
					<div class="table-responsive-md" style="height: 360px; overflow-y: scroll;">
						<table class="table table-hover border">
							<thead>
								<tr class="bg-dark">
									<th scope="col" class="bg-dark text-light">#</th>
									<th scope="col" class="bg-dark text-light">Nom</th>
									<th scope="col" class="bg-dark text-light">Prix en gourdes</th>
									<th scope="col" class="bg-dark text-light">Action</th>
								</tr>
							</thead>
							<tbody id="boissons-data">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->


	<!-- add Plats Modal -->
	<div class="modal fade" id="plats" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog">
			<form id="plats_form">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">AJOUTER PLAT</h5>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label fw-bold">Nom</label>
							<input required type="text" name="nom" class="form-control shadow-none" placeholder="Entrer le nom du plat ici...">
						</div>
						<div class="mb-3">
							<label class="form-label fw-bold">Prix</label>
							<input required type="number" name="prix" class="form-control shadow-none" placeholder="Entrer le prix du plat ici...">
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

	<!-- edit Plats Modal -->
	<div class="modal fade" id="edit_plats" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog">
			<form id="edit_plats_form">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">MODIFIER PLAT</h5>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label fw-bold">Nom</label>
							<input required type="text" name="nom" class="form-control shadow-none">
						</div>
						<div class="mb-3">
							<label class="form-label fw-bold">Prix</label>
							<input required type="number" name="prix" class="form-control shadow-none">
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

	<!-- add Boissons Modal -->
	<div class="modal fade" id="boissons" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog">
			<form id="boissons_form">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">AJOUTER BOISSON</h5>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label fw-bold">Nom</label>
							<input required type="text" name="nom" class="form-control shadow-none" placeholder="Entrer le nom du boisson ici...">
						</div>
						<div class="mb-3">
							<label class="form-label fw-bold">Prix</label>
							<input required type="text" name="prix" class="form-control shadow-none" placeholder="Entrer le prix du boisson ici...">
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

	<!-- Edit Boissons Modal -->
	<div class="modal fade" id="edit_boissons" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog">
			<form id="edit_boissons_form">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">MODIFIER BOISSON</h5>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label fw-bold">Nom</label>
							<input required type="text" name="nom" class="form-control shadow-none" placeholder="Entrer le nom du boisson ici...">
						</div>
						<div class="mb-3">
							<label class="form-label fw-bold">Prix</label>
							<input required type="text" name="prix" class="form-control shadow-none" placeholder="Entrer le prix du boisson ici...">
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
	<script src="scripts/plats_boissons.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</body>

</html>