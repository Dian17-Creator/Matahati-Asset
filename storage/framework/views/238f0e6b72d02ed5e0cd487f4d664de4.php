<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Kategori</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalKategori">
            + Tambah Kategori
        </button>
    </div>

    <div class="card-body">
        
        <div id="kategori-wrapper">
            <?php echo $__env->make('Asset.components.partials.kategori_table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/master_kategori.blade.php ENDPATH**/ ?>