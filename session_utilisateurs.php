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
	<title>Magic City | Session Utilisateurs</title>
</head>
<style>
	.text-s {
		font-size: 15px;
	}
</style>
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
			<li>
				<a href="reservations.php">
					<i class='bx bx-restaurant' ></i>
					<span class="text">Réservations</span>
				</a>
			</li>
			<li>
				<a href="plats_boissons.php">
					<i class='bx bx-food-menu'></i>
					<span class="text">Plats et Boissons</span>
				</a>
			</li>
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
					<li class='active'>
						<a href='session_utilisateurs.php'>
							<i class='bx bxs-time-five' ></i>
							<span class='text'>Journal des Connexions</span>
						</a>
					</li>";
				}
			?>
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
					<h1>Journal des Connexions</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="session_utilisateurs.php">Journal des Connexions</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="table-data" style="min-height: 470px;">
				<div class="order">
					<h3>Liste des Connexions</h3><hr>
					<div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="head mb-4">
								<input type="text" oninput="get_all_session_utilisateurs(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher une Connexion ici...">
								<div class="col-md-1.5">
									<select id="filter-search" name="filter" class="selectpicker z-index-1 form-select form-control shadow-none" onchange="applyFilter()">
										<option value="jour">Aujourd'hui</option>
										<option value="semaine">Cette semaine</option>
										<option value="mois">Ce mois-ci</option>
										<option value="annee">Cette année</option>
										<option value="admin">Admin</option>
										<option value="utilisateur">Utilisateur</option>
										<option value="tous">Tous</option>
									</select>
								</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead>
                                        <tr class="bg-dark text-center text-s">
                                            <th scope="col" class="bg-dark text-light">#</th>
                                            <th scope="col" class="bg-dark text-light">Nom d'utilisateurs</th>
                                            <th scope="col" class="bg-dark text-light">Heure de Connexion</th>
                                            <th scope="col" class="bg-dark text-light">Heure de Deconnexion</th>
                                            <th scope="col" class="bg-dark text-light">Statut</th>
                                            <th scope="col" class="bg-dark text-light">Role</th>
                                        </tr>
                                    </thead>
                                    <tbody id="session_utilisateurs-data">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div></div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<script src="js/scripts.js"></script>
	<script src="scripts/session_utilisateurs.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous">
	</script>
		
	<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Select JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>

</body>

</html>