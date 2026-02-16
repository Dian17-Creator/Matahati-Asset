<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #000;
        }

        h3 {
            text-align: center;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        img.face {
            width: 45px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #777;
        }

        /* ===== LEBAR KOLOM ===== */
        .c-username {
            width: 13%;
        }

        .c-gmail {
            width: 15%;
        }

        .c-phone {
            width: 9%;
        }

        .c-ktp {
            width: 9%;
        }

        .c-rekening {
            width: 11%;
        }

        .c-bank {
            width: 7%;
        }

        .c-nama {
            width: 8%;
        }

        .c-namalngkp {
            width: 14%;
        }

        .c-finger {
            width: 5%;
        }

        .c-tanggal {
            width: 7%;
        }

        .c-cabang {
            width: 9%;
        }

        .c-role {
            width: 7%;
        }

        .c-status {
            width: 6%;
        }

        .c-foto {
            width: 6%;
        }

        .footer {
            margin-top: 8px;
            font-size: 7px;
            text-align: right;
            color: #555;
        }
    </style>
</head>

<body>

    <h3>Daftar User</h3>

    <table>
        <thead>
            <tr>
                <th class="c-username">Username</th>
                <th class="c-gmail">Gmail</th>
                <th class="c-phone">No Telp</th>
                <th class="c-ktp">No KTP</th>
                <th class="c-rekening">No Rekening</th>
                <th class="c-bank">Bank</th>
                <th class="c-nama">Nama</th>
                <th class="c-namalngkp">Nama Lengkap</th>
                <th class="c-finger">Finger ID</th>
                <th class="c-tanggal">Tgl Masuk</th>
                <th class="c-cabang">Cabang</th>
                <th class="c-role">Role</th>
                <th class="c-status">Status</th>
                <th class="c-foto">Foto</th>
            </tr>
        </thead>

        <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    /* ===== ROLE ===== */
                    $role = $user->fhrd
                        ? 'HRD'
                        : ($user->fsuper
                            ? 'Supervisor'
                            : ($user->fadmin
                                ? 'Captain'
                                : ($user->fsenior
                                    ? 'Senior Crew'
                                    : 'Crew')));

                    /* ===== REKENING & BANK ===== */
                    $rekening = $user->caccnumber ?: $user->rekening->nomor_rekening ?? '-';
                    $bank = strtoupper($user->bank ?: $user->rekening->bank ?? '-');
                ?>

                <tr>
                    <td><?php echo e($user->cemail); ?></td>
                    <td><?php echo e($user->cmailaddress ?? '-'); ?></td>
                    <td><?php echo e($user->cphone ?? '-'); ?></td>
                    <td><?php echo e($user->cktp ?? '-'); ?></td>
                    <td><?php echo e($rekening); ?></td>
                    <td><?php echo e($bank); ?></td>
                    <td><?php echo e($user->cname); ?></td>
                    <td><?php echo e($user->cfullname ?? '-'); ?></td>
                    <td><?php echo e($user->finger_id ?? '-'); ?></td>
                    <td>
                        <?php echo e($user->dtanggalmasuk ? \Carbon\Carbon::parse($user->dtanggalmasuk)->format('d M Y') : '-'); ?>

                    </td>
                    <td><?php echo e($user->department->cname ?? '-'); ?></td>
                    <td><?php echo e($role); ?></td>
                    <td><?php echo e($user->factive ? 'Aktif' : 'Nonaktif'); ?></td>
                    <td>
                        <?php if(!empty($user->photos)): ?>
                            <?php $__currentLoopData = $user->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e($img); ?>" class="face" style="margin-bottom:4px;">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada <?php echo e(now()->format('d M Y H:i')); ?>

    </div>

</body>

</html>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/exports/user_pdf.blade.php ENDPATH**/ ?>