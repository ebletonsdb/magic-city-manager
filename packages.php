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
	<title>Magic City | Packages</title>
</head>
<style>
	.scale {
		transition: transform 0.3s ease-in-out;
	}
	.scale:hover{
		transform: scale(1.05); 
	}
	/* .btn-custom {
		background-color: #28a745;
		color: white;
		border-radius: 25px;
		padding: 10px 20px;
		transition: background 0.3s;
	} */
	.badge-custom {
		background-color: #28a745;
		color: #fff;
		font-size: 14px;
		padding: 5px 10px;
		border-radius: 20px;
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
			<li class="active">
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
					<h1>Packages</h1>
					<ul class="breadcrumb">
						<li>
							<a href="dashboard.php">Tableau de bord</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="packages.php">Packages</a>
						</li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<?php
					$count = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` 
        			FROM `packages`"));

					// $count_atente = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
					// 	COUNT(CASE WHEN statut = 'En attente' THEN 1 END) AS count_attente 
					// FROM reservations"));

					// $count_terminer = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
					// 	COUNT(CASE WHEN statut = 'Terminer' THEN 1 END) AS count_terminer 
					// FROM reservations"));
				?>
				<li>
					<i class='bx bx-package'></i>
					<span class="text">
						<h3><?= $count['count'] ?></h3>
						<p>Packages</p>
					</span>
				</li>
				<li>
					<i class='bx bx-time-five'></i>
					<span class="text">
						<h3>1</h3>
						<p>En attente</p>
					</span>
				</li>
				<li>
					<i class='bx bx-check-circle'></i>
					<span class="text">
						<h3>2</h3>
						<p>Terminer</p>
					</span>
				</li>
			</ul>

			<div class="table-data">
				<div class="order">
					<h3>Liste des Packages Disponible</h3><hr>
					<div class="head mb-5">
						<button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add_package">
							<i class="bi bi-plus-square m-1"></i>CREER
						</button>
						<input type="text" oninput="get_all_packages(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Rechercher package ici...">
					</div>
					<div class="container">
						<div class="row" id="packages-data">
						</div>
					</div>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<!-- add packages Modal -->
	<div class="modal fade" id="add_package" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="add_packages_form" autocomplete="off">
				<div class="modal-content">
					<?php 
						$id = rand(100000, 999999);
					?>
					<div class="modal-header d-flex justify-content-between">
						<h5 class="modal-title">Creer un nouveau Package</h5>
						<h5 class="modal-title">ID (#<?php echo "P-".$id; ?>)</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							
							<div class="col-12 mb-3">
                                    <h3 class="form-label fw-bold">Plats</h3>
                                    <div class="row">
                                        <?php
                                            $res = selectAll('plats');
                                            while($opt = mysqli_fetch_assoc($res)){
                                                echo"
                                                    <div class='col-md-4 mb-1'>
                                                        <label>
                                                            <input type='checkbox' name='id_plats' value='$opt[id]' class='form-check-input shadow-none'>
                                                            <input type='number' name='prix_plats' value='$opt[prix]' hidden>
                                                            $opt[nom] -> $opt[prix]gdes
                                                        </label>
                                                    </div>
                                                ";
                                            }
                                        ?>
                                    </div>
                                </div>
                            <div class="col-12 mb-3">
								<h3 class="form-label fw-bold">Boissons</h3>
								<div class="row">
									<?php
										$res = selectAll('boissons');
										while($opt = mysqli_fetch_assoc($res)){
											echo"
												<div class='col-md-4 mb-1'>
													<label>
														<input type='checkbox' name='id_boissons' value='$opt[id]' class='form-check-input shadow-none'>
                                                        <input type='number' name='prix_boissons' value='$opt[prix]' hidden>
														$opt[nom] -> $opt[prix]gdes
													</label>
												</div>
											";
										}
									?>
								</div>
							</div>
						</div>
						<input type="hidden" name="nom" value="<?=$id?>">
					</div>
					<div class="modal-footer">
						<button type="reset" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- add quantite plats et boissons Modal -->
	<div class="modal fade" id="add_qte" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<?php
				$id_package = $_GET['id'];
				echo "ID package en session: $id_package";
			?>
			<form id="add_qte_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header d-flex justify-content-between">
						<h5 class="modal-title">Ajouter les Quantitées</h5>
						<input type="hidden" name="id_package" value="<?= $id_package ?>" readonly>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-12 mb-3">
									<?php
										$qte_pers = 0; 

										if (isset($id_package) && is_numeric($id_package) && $id_package > 0) {
											$result = mysqli_query($con, "SELECT qte_pers FROM `packages` WHERE id = $id_package");
										
											if ($result && mysqli_num_rows($result) > 0) {
												$data = mysqli_fetch_assoc($result);
												$qte_pers = isset($data['qte_pers']) ? intval($data['qte_pers']) : 0;
											}
										}
									?>
									<div class="col-md-12 mb-3">
										<label class="form-label fw-bold">Quantité de personnes pour le package</label>
										<input required type="number" name="qte_pers" class="form-control shadow-none" 
											placeholder="Entrer la quantité de personnes ici" 
											value="<?= htmlspecialchars($qte_pers) ?>">
									</div>
									<hr>
                                    <h3 class="form-label fw-bold">Plats</h3>
                                    <div class="row">
										<?php
											$res2 = select("SELECT pl.nom AS nom_plats, pl.prix AS prix_plats, pp.id_plats AS id_plats, pp.qte_plats FROM packages_plats pp 
											JOIN plats pl ON pp.id_plats = pl.id 
											WHERE pp.id_packages = ?", 
											[$id_package], "i");
											if (!empty($res2)) {
												echo "<div class='col-12 mb-3'>";
												echo "<label class='d-flex'>";
												
												foreach ($res2 as $row) {
													$nom_plats = htmlspecialchars($row['nom_plats'], ENT_QUOTES, 'UTF-8');
													$prix_plats = htmlspecialchars($row['prix_plats'], ENT_QUOTES, 'UTF-8');
													$qte_plats = htmlspecialchars($row['qte_plats'], ENT_QUOTES, 'UTF-8');
													$id_plats = htmlspecialchars($row['id_plats'], ENT_QUOTES, 'UTF-8');
													echo "<div class='d-flex align-items-center mb-2'>
														<p class='m-0' name='nom_plats[]'>$nom_plats</p>";
       												echo "<input type='hidden' name='id_plats[]' value='$id_plats'>";
       												echo "<input type='hidden' name='prix_plats[]' value='$prix_plats'>";
													echo "<input type='number' name='qte_plats[]' value='$qte_plats' required class='form-control' style='width: 100px; margin-right: 10px;'>";
													echo "</div>";
												}
												echo "</label>";
												echo "</div>";
											}
										?>
									</div>
                                </div><hr>
                            <div class="col-12 mb-3">
								<h3 class="form-label fw-bold">Boissons</h3>
								<div class="row">
								<?php
									$res3 = select("SELECT b.nom AS nom_boissons, b.prix AS prix_boissons, pb.id_boissons AS id_boissons,  pb.qte_boissons FROM packages_boissons pb 
									JOIN boissons b ON pb.id_boissons = b.id 
									WHERE pb.id_packages = ?", 
									[$id_package], "i");
									if (!empty($res3)) {
										echo "<div class='col-12 mb-3'>";
										echo "<label class='d-flex'>";
										
										foreach ($res3 as $row) {
											$nom_boissons = htmlspecialchars($row['nom_boissons'], ENT_QUOTES, 'UTF-8');
											$qte_boissons = htmlspecialchars($row['qte_boissons'], ENT_QUOTES, 'UTF-8');
											$id_boissons = htmlspecialchars($row['id_boissons'], ENT_QUOTES, 'UTF-8');
											$prix_boissons = htmlspecialchars($row['prix_boissons'], ENT_QUOTES, 'UTF-8');
											echo "<div class='d-flex align-items-center mb-2'>
												<p class='m-0' name='nom_boissons[]'>$nom_boissons</p>";
											echo "<input type='hidden' name='id_boissons[]' value='$id_boissons'>";
											echo "<input type='hidden' name='prix_boissons[]' value='$prix_boissons'>";
											echo "<input type='number' required name='qte_boissons[]' value='$qte_boissons' class='form-control' style='width: 100px; margin-right: 10px;'>";
											echo "</div>";
										}
										echo "</label>";
										echo "</div>";
									}
								?>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="reset" onclick="refreshpage()" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">ANNULER</button>
						<button type="submit" class="btn btn-primary shadow-none">ENVOYER</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- edit packages Modal -->
	<div class="modal fade" id="edit_package" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="edit_packages_form" autocomplete="off">
				<div class="modal-content">
					<div class="modal-header d-flex justify-content-between">
						<h5 class="modal-title">Creer un nouveau Package</h5>
						<h5 class="modal-title" name="nom"></h5>
					</div>
					<div class="modal-body">
						<div class="row">
							
							<div class="col-12 mb-3">
                                    <h3 class="form-label fw-bold">Plats</h3>
                                    <div class="row">
                                        <?php
                                            $res = selectAll('plats');
                                            while($opt = mysqli_fetch_assoc($res)){
                                                echo"
                                                    <div class='col-md-4 mb-1'>
                                                        <label>
                                                            <input type='checkbox' name='id_plats' value='$opt[id]' class='form-check-input shadow-none'>
                                                            <input type='number' name='prix_plats' value='$opt[prix]' hidden>
                                                            $opt[nom] -> $opt[prix]gdes
                                                        </label>
                                                    </div>
                                                ";
                                            }
                                        ?>
                                    </div>
                                </div>
                            <div class="col-12 mb-3">
								<h3 class="form-label fw-bold">Boissons</h3>
								<div class="row">
									<?php
										$res = selectAll('boissons');
										while($opt = mysqli_fetch_assoc($res)){
											echo"
												<div class='col-md-4 mb-1'>
													<label>
														<input type='checkbox' name='id_boissons' value='$opt[id]' class='form-check-input shadow-none'>
                                                        <input type='number' name='prix_boissons' value='$opt[prix]' hidden>
														$opt[nom] -> $opt[prix]gdes
													</label>
												</div>
											";
										}
									?>
								</div>
							</div>
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
	<script src="scripts/packagess.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous">
	</script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script>

		
	</script>

</body>

</html>