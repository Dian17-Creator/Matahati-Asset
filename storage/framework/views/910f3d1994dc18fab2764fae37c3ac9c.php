<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Notifikasi Sistem Absensi</title>
</head>

<body>
    <h3><?php echo e($subjectLine ?? 'Notifikasi Baru dari Sistem Absensi'); ?></h3>

    <?php
        // support beberapa nama variabel yang mungkin dikirim dari Mailable:
        $items = $bodyLines ?? ($notifications ?? ($notifications ?? []));
        // jika masih null, pastikan set ke array kosong
        if (!is_iterable($items)) {
            $items = [];
        }
    ?>

    <ul>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                // jika $i adalah array/objek, coba ambil message / property
                if (is_array($i)) {
                    // preferensi field 'message' atau '0' atau gabungkan semua
                    $text = $i['message'] ?? ($i[0] ?? json_encode($i));
                } elseif (is_object($i)) {
                    $text = $i->message ?? (property_exists($i, 'message') ? $i->message : json_encode($i));
                } else {
                    // jika string langsung, tampilkan
                    $text = (string) $i;
                }
            ?>

            <li><?php echo nl2br(e($text)); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</body>

</html>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/emails/notifikasi.blade.php ENDPATH**/ ?>