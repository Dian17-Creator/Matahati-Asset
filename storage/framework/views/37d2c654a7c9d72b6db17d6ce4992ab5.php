<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Slip Gaji - <?php echo e($nama ?? '-'); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 20px;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            padding: 6px 0;
            border: 8px solid #000;
            background: #f2f2f2;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .info {
            width: 100%;
            margin-bottom: 12px;
        }

        .info .left,
        .info .right {
            display: inline-block;
            vertical-align: top;
        }

        .info .left {
            width: 58%;
        }

        .info .right {
            width: 100%;
            text-align: left;
            padding-left: 10px;
        }

        .info .row {
            display: flex;
            align-items: center;
            line-height: 1.3;
        }

        .label {
            flex: 0 0 400px;
            font-weight: bold;
            white-space: nowrap;
            /* Jangan pindah baris */
            position: relative;
            gap: 2px;
        }

        .label::after {
            content: ":";
            margin-left: 20px;
        }

        .value {
            margin-left: 7px;
            flex: 1;
            margin-bottom: -5px;
        }

        .row {
            line-height: 1.5em;
        }

        .value {
            display: inline-block;
        }

        .box {
            border: 2px solid #000;
            padding: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        th {
            background: #eee;
            font-weight: 700;
            text-align: left;
        }

        .right-align {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .section-title {
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        .notes {
            margin-top: 8px;
            font-size: 11px;
            color: #333;
        }

        .amount {
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">SLIP GAJI MATA HATI CAFÉ</div>
        <div class="subtitle">BULAN <?php echo e(strtoupper($bulan ?? date('F Y'))); ?></div>

        <div class="info">
            <div class="left">
                <div class="row"><span class="label">Nama </span><span class="value"><?php echo e($nama ?? '-'); ?></span></div>
                <div class="row"><span class="label">Jabatan </span><span
                        class="value"><?php echo e($jabatan ?? '-'); ?></span></div>
                <div class="row"><span class="label">Jumlah Hari Masuk </span><span
                        class="value"><?php echo e($hari_masuk ?? 0); ?></span></div>
            </div>
            <div class="right">
                <div class="row"><span class="label">Tanggal Cetak </span><span
                        class="value"><?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?></span></div>
            </div>
        </div>

        <div class="box">
            <div class="section-title">PENGHASILAN</div>
            <table>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="right-align amount"><?php echo e(rp($gaji_pokok ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Uang Makan</td>
                        <td class="right-align amount"><?php echo e(rp($tunjangan_makan ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="right-align amount"><?php echo e(rp($tunjangan_jabatan ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Transportasi</td>
                        <td class="right-align amount"><?php echo e(rp($tunjangan_transportasi ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Luar Kota</td>
                        <td class="right-align amount"><?php echo e(rp($tunjangan_luar_kota ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Masa Kerja</td>
                        <td class="right-align amount"><?php echo e(rp($tunjangan_masa_kerja ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Gaji Lembur</td>
                        <td class="right-align amount"><?php echo e(rp($lembur ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Tabungan Diambil</td>
                        <td class="right-align amount"><?php echo e(rp($tabungan_diambil ?? 0)); ?></td>
                    </tr>

                    <tr>
                        <td class="bold">Jumlah Penghasilan</td>
                        <td class="right-align bold amount">
                            <?php echo e(rp($jumlah_penghasilan ?? ($gaji_pokok ?? 0) + ($tunjangan_makan ?? 0) + ($lembur ?? 0))); ?>

                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title" style="margin-top:10px;">POTONGAN</div>
            <table>
                <tbody>
                    <tr>
                        <td>Potongan Keterlambatan</td>
                        <td class="right-align amount"><?php echo e(rp($potongan_keterlambatan ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Lain-Lain</td>
                        <td class="right-align amount"><?php echo e(rp($potongan_lain ?? 0)); ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Tabungan</td>
                        <td class="right-align amount"><?php echo e(rp($potongan_tabungan ?? 0)); ?></td>
                    </tr>

                    <tr>
                        <td class="bold">Jumlah Potongan</td>
                        <td class="right-align bold amount"><?php echo e(rp($jumlah_potongan ?? 0)); ?></td>
                    </tr>
                </tbody>
            </table>

            <table style="margin-top:10px;">
                <tbody>
                    <tr>
                        <td style="border:none;"></td>
                        <td class="right-align bold" style="border:none; font-size:16px;">
                            GAJI YANG DITERIMA:
                            <span style="margin-left:8px;"><?php echo e(rp($gaji_diterima ?? 0)); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php if(!empty($catatan)): ?>
                <div class="notes"><strong>Catatan:</strong> <?php echo nl2br(e($catatan)); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/pdfs/slip_gaji.blade.php ENDPATH**/ ?>