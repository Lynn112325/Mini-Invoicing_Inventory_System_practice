<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Product: <?= htmlspecialchars($product['name'] ?? '') ?></h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php
                    $is_edit = true;
                    include '_form.php';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>