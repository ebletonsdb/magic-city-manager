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
	<title>Magic City | Utilisateurs</title>
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
					
					<li class='active'>
						<a href='utilisateurs.php'>
							<i class='bx bxs-group' ></i>
							<span class='text'>Utilisateurs</span>
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
					<h1>Utilisateurs</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="utilisateurs.php">Utilisateurs</a>
						</li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<?php
					$count = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        			FROM `utilisateurs`"));

					$count_admin = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
						COUNT(CASE WHEN type = 'admin' THEN 1 END) AS count_admin 
					FROM utilisateurs"));

					$count_utilisateur = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
						COUNT(CASE WHEN type = 'utilisateur' THEN 1 END) AS count_utilisateur 
					FROM utilisateurs"));
				?>
				<li>
					<i class='bx bx-user'></i>
					<span class="text">
						<h3><?= $count['count'] ?></h3>
						<p>Total</p>
					</span>
				</li>
				<li>
					<i class='bx bx-user-check'></i>
					<span class="text">
						<h3><?= $count_admin['count_admin'] ?></h3>
						<p>Admin</p>
					</span>
				</li>
				<li>
					<i class='bx bx-user-circle'></i>
					<span class="text">
						<h3><?= $count_utilisateur['count_utilisateur'] ?></h3>
						<p>Utilisateur</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<h3>Liste des Utilisateurs</h3><hr>
					<div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="head mb-4">
								<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add_utilisateurs">
									<i class="bi bi-plus-square m-1"></i>AJOUTER
								</button>
								<input type="text" oninput="get_all_utilisateurs(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher un utilisateur ici...">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead>
                                        <tr class="bg-dark">
                                            <th scope="col" class="bg-dark text-light">#</th>
                                            <th scope="col" class="bg-dark text-light">Nom d'utilisateurs</th>
                                            <th scope="col" class="bg-dark text-light">Type</th>
                                            <th scope="col" class="bg-dark text-light">Email</th>
                                            <th scope="col" class="bg-dark text-light">Addresse</th>
                                            <th scope="col" class="bg-dark text-light">Phone</th>
                                            <!-- <th scope="col" class="bg-dark text-light">verifier</th> -->
                                            <th scope="col" class="bg-dark text-light">Statut</th>
                                            <th scope="col" class="bg-dark text-light">Date</th> 
                                            <th scope="col" class="bg-dark text-light">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="utilisateurs-data">
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

	<!-- Add Utilisateurs Modal -->
	<div class="modal fade" id="add_utilisateurs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			<form id="add_utilisateurs_form">
				<div class="modal-header">
				<h5 class="modal-title d-flex align-items-center"><i class="bi bi-person-lines-fill fs-3 me-2"></i> Ajouter Utilisateur</h5>
				<button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
					<div class="col-md-6 mb-3">
						<label for="select-search" class="form-label">Type de Compte :</label>
						<select id="select-search" name="type" class="selectpicker form-select form-control shadow-none" data-live-search="true">
							<option value="" disabled selected>Choisir un Type</option>
							<option value="admin">Admin</option>
							<option value="utilisateur">Utilisateur</option>
						</select>
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Nom Utilisateur</label>
						<input name="nom" type="text" class="form-control shadow-none" placeholder="Saisir le nom ici..." required>
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Email</label>
						<input name="email" type="email" class="form-control shadow-none" placeholder="Saisir l'E-mail ici..." required required autocomplete="username">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Addresse</label>						
						<input name="addresse" type="text" class="form-control shadow-none" placeholder="Saisir l'adresse ici..." required autocomplete="username">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Phone</label>
						<input name="phone" type="number" class="form-control shadow-none" placeholder="Saisir le numero ici..." required>
					</div> 
					<div class="col-md-6 mb-3">
						<label class="form-label">Mot de Passe</label>
						<input name="mdp" type="password" class="form-control shadow-none" placeholder="Saisir le mot de passe ici..." required autocomplete="new-password">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Confirmer Mot de Passe</label>
						<input name="cmdp" type="password" class="form-control shadow-none" placeholder="Confirmer le mot de passe ici..." required autocomplete="new-password">
					</div>
					</div>
				</div>
				<div class="modal-footer">
						<button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>

	<!-- edit Utilisateurs Modal -->
	<div class="modal fade" id="edit_utilisateurs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			<form id="edit_utilisateurs_form">
				<div class="modal-header">
				<h5 class="modal-title d-flex align-items-center"><i class="bi bi-person-lines-fill fs-3 me-2"></i> Modifier Utilisateur</h5>
				<button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
					<div class="col-md-6 mb-3">
						<label for="select-search" class="form-label">Type de Compte :</label>
						<input required type="text" name="type" class="form-control shadow-none" readonly>
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Nom Utilisateur</label>
						<input name="nom" type="text" class="form-control shadow-none" placeholder="Saisir le nom ici..." required>
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Email</label>
						<input name="email" type="email" class="form-control shadow-none" placeholder="Saisir l'E-mail ici..." required required autocomplete="username">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Addresse</label>						
						<input name="addresse" type="text" class="form-control shadow-none" placeholder="Saisir l'adresse ici..." required autocomplete="username">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Phone</label>
						<input name="phone" type="number" class="form-control shadow-none" placeholder="Saisir le numero ici..." required>
					</div> 
					<div class="col-md-6 mb-3">
						<label class="form-label">Mot de Passe</label>
						<input name="mdp" type="password" class="form-control shadow-none" placeholder="Saisir le mot de passe ici..." required autocomplete="new-password">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">Confirmer Mot de Passe</label>
						<input name="cmdp" type="password" class="form-control shadow-none" placeholder="Confirmer le mot de passe ici..." required autocomplete="new-password">
					</div> 
					<input type="hidden" name="id">
					</div>
				</div>
				<div class="modal-footer">
						<button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>


	<script src="js/scripts.js"></script>
	<script src="scripts/utilisateurss.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous">
	</script>
		
	<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Select JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
			$('.bootstrap-select .bs-searchbox input').attr('style', 'outline: none !important; box-shadow: none !important; border: 1px solid #ccc;');
        });
    </script>

</body>

</html>