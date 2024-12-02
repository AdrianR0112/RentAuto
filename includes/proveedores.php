<?php
// Consultar las marcas de la base de datos
$sql = "SELECT id, ImagenMarca FROM tblbrands";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Marcas Start -->
<div class="container-fluid">
    <div class="container py-5">
        <h1 class="display-4 text-uppercase text-center mb-5">Marcas Disponibles</h1>
        <div class="owl-carousel vendor-carousel">
            <?php foreach ($brands as $brand): ?>
                <div class="bg-light p-4">
                    <img src="img/brands/<?php echo htmlspecialchars($brand['ImagenMarca']); ?>" alt="Marca">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Marcas End -->