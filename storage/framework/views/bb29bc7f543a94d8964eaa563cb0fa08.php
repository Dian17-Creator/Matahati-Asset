<?php $__env->startSection('title', 'HR Approval – Registrasi Wajah'); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-4">
        <div class="container-fluid px-4 px-lg-5"> 

            
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="fw-bold mb-2" style="color:#ff6f51; margin-top: -25px;">
                        HR Approval – Registrasi Wajah
                    </h2>
                    <p class="text-muted mb-0">
                        Tinjau dan setujui registrasi wajah karyawan sebelum dapat digunakan untuk absensi.
                    </p>
                </div>
            </div>

            
            <div class="row g-4">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-xl-10 mx-auto"> 
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h4 class="mb-1 fw-semibold"><?php echo e($u->cname); ?></h4>

                                        
                                        <?php if(!empty($u->department?->cname)): ?>
                                            <div class="text-muted small">
                                                <?php echo e($u->department->cname); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        Menunggu persetujuan
                                    </span>
                                </div>

                                
                                <div class="d-flex flex-wrap gap-3 mb-4">
                                    <?php $__currentLoopData = $u->faces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $face): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="rounded-4 overflow-hidden"
                                            style="width: 180px; height: 220px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                                            <img src="/faces/<?php echo e($face->cfilename); ?>" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                
                                <div class="d-flex justify-content-end gap-3">
                                    <form action="<?php echo e(route('hr.face_approval.approve', $u->nid)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success px-4">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <form action="<?php echo e(route('hr.face_approval.reject', $u->nid)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-danger px-4">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/faces/index.blade.php ENDPATH**/ ?>