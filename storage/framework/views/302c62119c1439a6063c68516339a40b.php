<?php $__env->startSection('content'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .table-request {
            font-size: 0.95rem;
            align-items: center;
            justify-content: center;
        }

        @media (min-width: 992px) {
            .table-request img {
                width: 150px;
                height: 150px;
                border-radius: 6px;
                object-fit: cover;
                align-items: center;
                justify-content: center;
                display: flex;
            }
        }

        .btn-reject {
            background: #ff0000;
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            transition: transform .06s, box-shadow .08s, opacity .12s;
        }

        .btn-approve {
            background: #00be2c;
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            transition: transform .06s, box-shadow .08s, opacity .12s;
        }

        /* hover / focus */
        .btn-reject:hover,
        .btn-approve:hover,
        .btn-reject:focus,
        .btn-approve:focus {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            outline: none;
        }

        /* disabled look */
        .btn-reject:disabled,
        .btn-approve:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 991px) {

            /* hide standard table header */
            .table-request thead {
                display: none;
            }

            /* each row becomes a card */
            .table-request tbody tr {
                display: block;
                background: #ffffff;
                border-radius: 12px;
                padding: 18px;
                margin-bottom: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                border: 1px solid #eee;
            }

            /* hide ID cell entirely on mobile */
            .table-request tbody tr td:nth-child(1) {
                display: none;
            }

            /* make each td a block and center content */
            .table-request tbody tr td {
                display: flex;
                width: 100%;
                padding: 6px 0;
                border: none !important;
                text-align: center !important;
                vertical-align: middle;
                align-items: stretch;
                justify-content: center;
                color: #333;
                font-size: 1rem;
                flex-direction: column;
            }

            /* FOTO */
            .table-request tbody tr td img {
                width: 220px !important;
                height: 220px !important;
                object-fit: cover;
                border-radius: 10px;
                margin: 0 auto 10px auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /* tanggal / lokasi / alasan style -- center & comfortable spacing */
            .table-request .meta {
                font-weight: 600;
                color: #444;
                margin-bottom: 8px;
            }

            .table-request .submeta {
                color: #666;
                font-weight: 500;
                margin-bottom: 6px;
            }

            .approve-area .btn-reject,
            .approve-area .btn-approve {
                min-height: 52px;
                font-size: 1.05rem;
                padding: 14px 12px;
                border-radius: 10px;
                flex: 1;
            }

            .approve-area {
                width: 100%;
                display: flex;
                justify-content: stretch;
                /* penting */
            }

            .approve-area form {
                width: 100%;
                display: flex;
                flex-direction: row;
                gap: 12px;
            }

            .approve-area form button {
                flex: 1 1 50%;
                min-height: 52px;
                font-size: 1.05rem;
                padding: 14px 0;
                border-radius: 10px;
            }


            /* Pending badge centered */
            .table-request .badge {
                display: inline-block;
                margin-top: 8px;
                font-size: 1rem;
                padding: .5rem .75rem;
                border-radius: 8px;
            }
        }

        @media (min-width: 992px) {

            .table-request tbody td {
                text-align: center;
                vertical-align: middle;
            }

            .table-request tbody td>* {
                margin-left: auto;
                margin-right: auto;
            }

            .table-request .approve-area,
            .table-request form.approve-form {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
            }

            .table-request tbody td img {
                display: block;
                margin: 0 auto;
            }
        }
    </style>

    <div class="container py-4">
        <h3 class="text-center mb-4">REQUEST IZIN - <?php echo e($user->cname); ?></h3>

        <div class="d-flex justify-content-end mb-3">
            <a href="<?php echo e(route('export-request', [
                'user_id' => $user->nid,
                'start_date' => request('start_date'),
                'end_date' => request('end_date'),
            ])); ?>"
                class="btn btn-success fw-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel - <?php echo e($user->cname); ?>

            </a>
        </div>

        <div class="table-responsive table-scroll">
            <table class="table table-bordered align-middle table-request">
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
                        <tr>
                            
                            <td><?php echo e($req->nid); ?></td>

                            
                            <td>
                                <div class="meta"><?php echo e(\Carbon\Carbon::parse($req->drequest)->format('d/m/Y')); ?></div>
                            </td>

                            
                            <td>
                                <div class="submeta"><?php echo e($req->creason); ?></div>
                            </td>

                            
                            <td>
                                <div class="submeta">
                                    <?php if(!empty($req->cplacename)): ?>
                                        <?php echo e($req->cplacename); ?>

                                    <?php else: ?>
                                        <?php echo e($req->nlat); ?>, <?php echo e($req->nlng); ?>

                                    <?php endif; ?>
                                </div>
                            </td>

                            
                            
                            <td>
                                <?php
                                    $isCaptain =
                                        auth()->user()->fadmin == 1 &&
                                        auth()->user()->fsuper == 0 &&
                                        auth()->user()->fhrd == 0;
                                    $isHrd = auth()->user()->fhrd == 1;

                                    $finalStatus = 'pending';
                                    $finalBy = '';
                                    $approvedAt = null;
                                    $approvedByName = '';

                                    // Prioritaskan status dari admin
                                    if ($req->fadmreq == 1) {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Admin';
                                        $approvedAt = $req->duphrd ?? ($req->dupdated ?? $req->dcreated);
                                        $approvedByName = 'Admin';
                                    }
                                    // Jika ditolak oleh captain
                                    elseif ($req->cstatus === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'Captain';
                                        $approvedAt = $req->dupdated;
                                        $approvedByName = 'Captain';
                                    }
                                    // Jika ditolak oleh HRD
                                    elseif ($req->chrdstat === 'rejected') {
                                        $finalStatus = 'rejected';
                                        $finalBy = 'HRD';
                                        $approvedAt = $req->duphrd;
                                        $approvedByName = 'HRD';
                                    }
                                    // Jika disetujui oleh keduanya
                                    elseif ($req->cstatus === 'approved' && $req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain & HRD';
                                        // Ambil waktu persetujuan HRD (yang terakhir)
                                        $approvedAt = $req->duphrd ?? $req->dupdated;
                                        $approvedByName = 'Captain & HRD';
                                    }
                                    // Jika disetujui captain saja
                                    elseif ($req->cstatus === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'Captain';
                                        $approvedAt = $req->dupdated;
                                        $approvedByName = 'Captain';
                                    }
                                    // Jika disetujui HRD saja
                                    elseif ($req->chrdstat === 'approved') {
                                        $finalStatus = 'approved';
                                        $finalBy = 'HRD';
                                        $approvedAt = $req->duphrd;
                                        $approvedByName = 'HRD';
                                    }
                                ?>

                                <?php if($req->fadmreq == 1): ?>
                                    <div class="d-flex justify-content-center">
                                        <span class="badge bg-success">Approved (By Admin)</span>
                                    </div>
                                <?php elseif($finalStatus !== 'pending'): ?>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="badge <?php echo e($finalStatus === 'approved' ? 'bg-success' : 'bg-danger'); ?>">
                                            <?php echo e(ucfirst($finalStatus)); ?> by <?php echo e($approvedByName); ?>

                                        </span>

                                        <?php if($approvedAt): ?>
                                            <small class="text-muted">
                                                <?php echo e(\Carbon\Carbon::parse($approvedAt)->timezone('Asia/Jakarta')->format('d/m/Y H:i')); ?>

                                                WIB
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <?php if($isCaptain): ?>
                                        
                                        <div class="approve-area">
                                            <form action="<?php echo e(route('mrequest.approve.captain', $req->nid)); ?>"
                                                method="POST" class="approve-form d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button name="status" value="rejected" type="submit"
                                                    class="btn btn-reject">Reject</button>
                                                <button name="status" value="approved" type="submit"
                                                    class="btn btn-approve">Approve</button>
                                            </form>
                                        </div>
                                    <?php elseif($isHrd): ?>
                                        <div class="approve-area">
                                            <form action="<?php echo e(route('mrequest.approve.hrd', $req->nid)); ?>" method="POST"
                                                class="approve-form d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button name="status" value="rejected" type="submit"
                                                    class="btn btn-reject">Reject</button>
                                                <button name="status" value="approved" type="submit"
                                                    class="btn btn-approve">Approve</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-secondary">Pending</span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <?php if(!empty($req->cphoto_path)): ?>
                                    <?php
                                        $photoUrl = 'https://absensi.matahati.my.id/' . ltrim($req->cphoto_path, '/');
                                    ?>
                                    <a href="<?php echo e($photoUrl); ?>" target="_blank" rel="noopener">
                                        <img src="<?php echo e($photoUrl); ?>" alt="Foto Request" />
                                    </a>
                                <?php else: ?>
                                    <small>-</small>
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
                    const clickedValue = clickedButton.value; // 'approved' atau 'rejected'
                    const formData = new FormData(this);
                    formData.append("status", clickedValue);

                    const url = this.action;
                    const statusCell = this.closest('td');
                    const buttons = this.querySelectorAll("button");
                    const row = this.closest('tr');
                    const isCaptainForm = url.includes('captain');
                    const isHrdForm = url.includes('hrd');

                    // Ambil data user role dari HTML
                    const isCaptainUser = <?php echo json_encode(auth()->user()->fadmin == 1 && auth()->user()->fsuper == 0 && auth()->user()->fhrd == 0, 15, 512) ?>;
                    const isHrdUser = <?php echo json_encode(auth()->user()->fhrd == 1, 15, 512) ?>;

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

                            // Simulasikan logika PHP Blade untuk menentukan status akhir
                            let finalStatus = clickedValue; // 'approved' atau 'rejected'
                            let approvedByName = '';
                            let approvedAt = '';
                            let isBothApproved = false;

                            // Tentukan siapa yang approve berdasarkan URL form
                            if (isCaptainForm) {
                                approvedByName = 'Captain';
                                // Jika Captain approve dan HRD sudah approve sebelumnya, maka statusnya "Captain & HRD"
                                // Ini perlu dicek dari data yang ada di row
                                const currentHrdStatus = row.querySelector('.hrd-status-data')
                                    ?.value || '';
                                if (clickedValue === 'approved' && currentHrdStatus ===
                                    'approved') {
                                    approvedByName = 'Captain & HRD';
                                    isBothApproved = true;
                                }
                            } else if (isHrdForm) {
                                approvedByName = 'HRD';
                                // Jika HRD approve dan Captain sudah approve sebelumnya
                                const currentCaptainStatus = row.querySelector(
                                    '.captain-status-data')?.value || '';
                                if (clickedValue === 'approved' && currentCaptainStatus ===
                                    'approved') {
                                    approvedByName = 'Captain & HRD';
                                    isBothApproved = true;
                                }
                            }

                            // Format waktu dengan WIB
                            const now = new Date();
                            const jakartaTime = new Date(now.toLocaleString('en-US', {
                                timeZone: 'Asia/Jakarta'
                            }));
                            const day = String(jakartaTime.getDate()).padStart(2, '0');
                            const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
                            const year = jakartaTime.getFullYear();
                            const hours = String(jakartaTime.getHours()).padStart(2, '0');
                            const minutes = String(jakartaTime.getMinutes()).padStart(2, '0');

                            approvedAt = `${day}/${month}/${year} ${hours}:${minutes}`;

                            // Tampilkan badge dengan format yang sama seperti Blade
                            let badgeHtml = '';

                            if (isBothApproved) {
                                // Jika kedua-duanya sudah approve
                                badgeHtml = `
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-success">
                                        Approved by ${approvedByName}
                                    </span>
                                    <small class="text-muted">
                                        ${approvedAt} WIB
                                    </small>
                                </div>
                            `;
                            } else if (clickedValue === 'rejected') {
                                // Jika direject
                                badgeHtml = `
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-danger">
                                        Rejected by ${approvedByName}
                                    </span>
                                    <small class="text-muted">
                                        ${approvedAt} WIB
                                    </small>
                                </div>
                            `;
                            } else {
                                // Jika hanya satu yang approve (masih pending dari yang lain)
                                badgeHtml = `
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-success">
                                        Approved by ${approvedByName}
                                    </span>
                                    <small class="text-muted">
                                        ${approvedAt} WIB
                                    </small>
                                </div>
                            `;
                            }

                            statusCell.innerHTML = badgeHtml;

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message ||
                                    'Terjadi kesalahan saat memperbarui status.'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/backoffice/requestcard.blade.php ENDPATH**/ ?>