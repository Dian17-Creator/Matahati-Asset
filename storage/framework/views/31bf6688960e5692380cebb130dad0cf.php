<?php $__env->startSection('content'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
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

        .badge {
            font-size: 0.85rem;
        }
    </style>

    <div class="container py-4">
        <h3 class="text-center mb-4">REQUEST IZIN - <?php echo e($user->cname); ?></h3>

        <div class="d-flex justify-content-end mb-3">
            <a href="<?php echo e(route('export-request', [
                'user_id' => $user->nid,
                'start_date' => request('start_date', now()->subMonth()->format('Y-m-d')),
                'end_date' => request('end_date', now()->format('Y-m-d')),
            ])); ?>"
                class="btn btn-success fw-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel - <?php echo e($user->cname); ?>

            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center align-top">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Lokasi</th>
                        <th>Status Approval</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="text-center align-top">
                            <td><?php echo e($req->nid); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($req->drequest)->format('d/m/Y')); ?></td>
                            <td><?php echo e($req->creason); ?></td>

                            
                            <td>
                                <?php if(!empty($req->cplacename)): ?>
                                    <?php echo e($req->cplacename); ?>

                                <?php else: ?>
                                    <?php echo e($req->nlat); ?>, <?php echo e($req->nlng); ?>

                                <?php endif; ?>
                            </td>

                            
                            
                            <td>
                                <?php
                                    $isCaptain =
                                        auth()->user()->fadmin == 1 &&
                                        auth()->user()->fsuper == 0 &&
                                        auth()->user()->fhrd == 0;
                                    $isHrd = auth()->user()->fhrd == 1;

                                    // Tentukan status akhir gabungan
                                    $finalStatus = 'pending';
                                    $finalBy = '';

                                    if ($req->fadmreq == 1) {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Admin';
                                    } elseif ($req->cstatus === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'Captain';
                                    } elseif ($req->chrdstat === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'HRD';
                                    } elseif ($req->cstatus === 'approved' && $req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain & HRD';
                                    } elseif ($req->cstatus === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain';
                                    } elseif ($req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'HRD';
                                    }
                                ?>

                                
                                <?php if($req->fadmreq == 1): ?>
                                    <span class="badge bg-success">Approved (By Admin)</span>

                                    
                                <?php elseif($finalStatus !== 'pending'): ?>
                                    <span class="badge <?php echo e($finalStatus === 'approved' ? 'bg-success' : 'bg-danger'); ?>">
                                        <?php echo e(ucfirst($finalStatus)); ?> by <?php echo e($finalBy); ?>

                                    </span>

                                    
                                <?php else: ?>
                                    <?php if($isCaptain): ?>
                                        <form action="<?php echo e(route('mrequest.approve.captain', $req->nid)); ?>" method="POST"
                                            class="approve-form d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    <?php elseif($isHrd): ?>
                                        <form action="<?php echo e(route('mrequest.approve.hrd', $req->nid)); ?>" method="POST"
                                            class="approve-form d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button name="status" value="approved"
                                                class="btn btn-success btn-sm">Approve</button>
                                            <button name="status" value="rejected"
                                                class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pending</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>


                            
                            <td>
                                <?php if(!empty($req->cphoto_path)): ?>
                                    <?php
                                        $photoUrl = 'https://absensi.matahati.my.id/' . ltrim($req->cphoto_path, '/');
                                    ?>
                                    <a href="<?php echo e($photoUrl); ?>" target="_blank">
                                        <img src="<?php echo e($photoUrl); ?>" alt="Foto Request"
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
        </div>

        <div class="d-flex justify-content-center mt-4">
            <?php echo e($requests->links('pagination::bootstrap-5')); ?>

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

                            // Ganti tombol dengan badge status
                            statusCell.innerHTML = `
                                <span class="badge ${clickedButton.value === 'approved' ? 'bg-success' : 'bg-danger'}">
                                    ${clickedButton.value === 'approved' ? 'Approved' : 'Rejected'}
                                </span>
                            `;
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/requests.blade.php ENDPATH**/ ?>