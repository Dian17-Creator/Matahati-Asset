<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color: #B63352; color: white;">

        <span>Master Satuan</span>

        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSatuan">
            + Tambah Satuan
        </button>
    </div>


    <div class="card-body">
        
        <div id="satuan-wrapper">
            <?php echo $__env->make('Asset.components.partials.satuan_table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Matahati-Asset\resources\views/Asset/components/master_satuan.blade.php ENDPATH**/ ?>