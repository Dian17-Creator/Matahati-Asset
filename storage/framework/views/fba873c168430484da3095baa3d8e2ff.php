<?php $__env->startSection('content'); ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* [id^="map_"] {
                                                height: 200px;
                                                width: 200px;
                                                border-radius: 10px;
                                            } */

        th a {
            color: inherit;
            text-decoration: none;
            transition: 0.2s;
        }

        th a:hover {
            color: #FF6F51;
        }

        .active-sort {
            color: #FF6F51;
        }

        td>div[id^="map_"] {
            margin: 0 auto;
            display: block;
        }
    </style>

    <div class="container py-4">
        <h3 class="text-center">LOG ABSENSI - <?php echo e($user->cname); ?></h3>

        
        <form method="GET" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="<?php echo e(request('start_date', now()->subMonth()->format('Y-m-d'))); ?>">
                </div>

                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="<?php echo e(request('end_date', now()->format('Y-m-d'))); ?>">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                </div>

                <div class="col-md-4 d-flex justify-content-end">
                    <a href="<?php echo e(route('export-mscan', [
                        'user_id' => $user->nid,
                        'start_date' => request('start_date', now()->subMonth()->format('Y-m-d')),
                        'end_date' => request('end_date', now()->format('Y-m-d')),
                    ])); ?>"
                        class="btn btn-success fw-bold">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel - <?php echo e($user->cname); ?>

                    </a>
                </div>
            </div>
        </form>

        
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Alasan</th>
                    <th>Tipe Absen</th>
                    <th>Status Approval</th>
                    
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-center align-top">
                        <td><?php echo e($log->nid); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($log->dscanned)->format('d/m/Y H:i:s')); ?></td>

                        
                        <td>
                            <?php if(!empty($log->cplacename)): ?>
                                <?php echo e($log->cplacename); ?>

                            <?php elseif(!empty($log->nlat) && !empty($log->nlng)): ?>
                                <?php echo e($log->nlat); ?>, <?php echo e($log->nlng); ?>

                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>

                        <td><?php echo e($log->creason ?? '-'); ?></td>

                        
                        <td>
                            <?php if($log->source === 'face'): ?>
                                <span class="badge bg-info text-white">Face</span>
                            <?php elseif($log->source === 'forgot'): ?>
                                <span class="badge bg-danger text-white">Lupa Absen</span>
                            <?php elseif($log->source === 'manual' || (!empty($log->fmanual) && $log->fmanual == 1)): ?>
                                <span class="badge bg-warning text-dark">Manual</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Scan</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            
                            <?php if($log->source === 'face'): ?>
                                <span class="badge bg-success">Accepted</span>

                                
                            <?php elseif($log->source === 'forgot'): ?>
                                <?php if($log->cstatus === 'approved'): ?>
                                    <span class="badge bg-success">Approved by HRD</span>
                                <?php elseif($log->cstatus === 'rejected'): ?>
                                    <span class="badge bg-danger">Rejected by HRD</span>
                                <?php else: ?>
                                    <?php if(auth()->user()->fhrd == 1): ?>
                                        <form action="<?php echo e(route('forgot.approve', $log->nid)); ?>" method="POST"
                                            class="approve-form d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Waiting HRD</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                
                            <?php elseif($log->source === 'manual' || (!empty($log->fmanual) && $log->fmanual == 1)): ?>
                                <?php
                                    $isCaptain =
                                        auth()->user()->fadmin == 1 &&
                                        auth()->user()->fsuper == 0 &&
                                        auth()->user()->fhrd == 0;
                                    $isHrd = auth()->user()->fhrd == 1;

                                    $hasBeenProcessed =
                                        (isset($log->cstatus) && $log->cstatus !== 'pending') ||
                                        (isset($log->chrdstat) && $log->chrdstat !== 'pending');
                                ?>

                                <?php if($hasBeenProcessed): ?>
                                    <span
                                        class="badge
                                        <?php if($log->cstatus === 'approved' || $log->chrdstat === 'approved'): ?> bg-success
                                        <?php elseif($log->cstatus === 'rejected' || $log->chrdstat === 'rejected'): ?> bg-danger
                                        <?php else: ?> bg-secondary <?php endif; ?>">
                                        <?php echo e($log->cstatus === 'approved' ? 'Approved by Captain' : ''); ?>

                                        <?php echo e($log->cstatus === 'rejected' ? 'Rejected by Captain' : ''); ?>

                                        <?php echo e($log->chrdstat === 'approved' ? 'Approved by HRD' : ''); ?>

                                        <?php echo e($log->chrdstat === 'rejected' ? 'Rejected by HRD' : ''); ?>

                                    </span>
                                <?php else: ?>
                                    <?php if($isCaptain): ?>
                                        <form action="<?php echo e(route('mscan.approve.captain', $log->nid)); ?>" method="POST"
                                            class="approve-form d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    <?php elseif($isHrd): ?>
                                        <form action="<?php echo e(route('mscan.approve.hrd', $log->nid)); ?>" method="POST"
                                            class="approve-form d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <small class="text-muted">Pending</small>
                                    <?php endif; ?>
                                <?php endif; ?>

                                
                            <?php else: ?>
                                <span class="badge bg-success">Accepted</span>
                            <?php endif; ?>
                        </td>

                        
                        

                        
                        <td>
                            <?php if(!empty($log->cphoto_path)): ?>
                                <?php
                                    $photoUrl = 'https://absensi.matahati.my.id/' . ltrim($log->cphoto_path, '/');
                                ?>
                                <a href="<?php echo e($photoUrl); ?>" target="_blank">
                                    <img src="<?php echo e($photoUrl); ?>" alt="Foto Absen"
                                        style="width:150px;height:150px;object-fit:cover;border-radius:6px;"
                                        onerror="this.onerror=null; this.replaceWith(document.createTextNode('-'));" />
                                </a>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            <?php echo e($logs->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll("form.approve-form");

            forms.forEach(form => {
                form.addEventListener("submit", async function(e) {
                    e.preventDefault();

                    const clickedButton = e.submitter;
                    const formData = new FormData(this);
                    formData.append("status", clickedButton.value);

                    const url = this.action;
                    const statusCell = this.closest('td');
                    const buttons = this.querySelectorAll("button");

                    buttons.forEach(btn => {
                        btn.disabled = true;
                        btn.innerText = "Processing...";
                    });

                    try {
                        const response = await fetch(url, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // ubah status langsung
                            statusCell.innerHTML = `
                                <span class="badge ${clickedButton.value === 'approved' ? 'bg-success' : 'bg-danger'}">
                                    ${clickedButton.value === 'approved' ? 'Approved' : 'Rejected'}
                                </span>`;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message ||
                                    'Terjadi kesalahan saat memperbarui status.'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memproses permintaan.'
                        });
                    } finally {
                        buttons.forEach(btn => btn.disabled = false);
                    }
                });
            });
        });
    </script>

    
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/logs.blade.php ENDPATH**/ ?>