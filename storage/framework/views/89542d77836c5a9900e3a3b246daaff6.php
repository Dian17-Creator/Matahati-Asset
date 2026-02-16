<?php $__env->startSection('title', 'Face User'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        <h4 class="fw-bold mb-3">
            Face Terdaftar – <?php echo e($user->cname); ?>

        </h4>

        <div class="text-muted mb-4">
            <?php echo e($user->department?->cname); ?>

        </div>

        <div class="d-flex flex-wrap gap-3">
            <?php $__empty_1 = true; $__currentLoopData = $faces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $face): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    // 🔥 URL ABSOLUT (SAMA DENGAN LOG ABSENSI)
                    $faceUrl = 'https://absensi.matahati.my.id/faces/' . ltrim($face->cfilename, '/');
                ?>

                <div class="rounded-4 overflow-hidden" style="width:200px;height:240px;box-shadow:0 4px 12px rgba(0,0,0,.1)">
                    <a href="<?php echo e($faceUrl); ?>" target="_blank">
                        <img src="<?php echo e($faceUrl); ?>" alt="Face User" class="w-100 h-100" style="object-fit:cover"
                            onerror="this.onerror=null; this.replaceWith(document.createTextNode('-'));">
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-muted fst-italic">
                    Face belum tersedia
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <a href="<?php echo e(route('backoffice.index')); ?>" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/face_show.blade.php ENDPATH**/ ?>