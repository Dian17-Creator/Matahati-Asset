<?php $__env->startSection('content'); ?>
    <div class="container">

        <div class="d-flex justify-content-center align-items-center mb-4">
            <h4 class="mb-0">DASHBOARD ASSET</h4>
        </div>


        <div class="row">

            
            <div class="col-md-6 mb-4">
                <?php echo $__env->make('Asset.components.master_satuan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            
            <div class="col-md-6 mb-4">
                <?php echo $__env->make('Asset.components.master_kategori', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

        </div>


        

        <?php echo $__env->make('Asset.components.master_sub_kategori', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('Asset.components.master_asset_qr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('Asset.components.master_asset_nonqr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>

    <?php echo $__env->make('Asset.modal.modal_satuan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('Asset.modal.modal_kategori', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('Asset.modal.modal_sub_kategori', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('Asset.modal.modal_asset', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('Asset.modal.modal_asset_qr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('Asset.modal.modal_asset_nonqr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        window.routeMsatuanStore = "<?php echo e(route('msatuan.store')); ?>";
    </script>

    <script src="<?php echo e(asset('js/asset.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Matahati-Asset\resources\views/Asset/index.blade.php ENDPATH**/ ?>