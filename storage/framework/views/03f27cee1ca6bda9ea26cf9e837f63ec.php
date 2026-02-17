<?php $__env->startSection('content'); ?>
    <div class="container">

        <h4 class="mb-3">Master Asset</h4>

        
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                + Kategori
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori"
                onclick="openCreateSubKategori()">
                + Sub Kategori
            </button>

            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAsset">
                + Asset
            </button>
        </div>

        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Master Satuan</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSatuan"
                    onclick="openCreateSatuan()">
                    + Tambah Satuan
                </button>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i + 1); ?></td>
                                <td><?php echo e($s->nama); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalSatuan"
                                        onclick="openEditSatuan(<?php echo e($s->id); ?>, '<?php echo e($s->nama); ?>')">
                                        Edit
                                    </button>

                                    <form method="POST" action="<?php echo e(route('msatuan.destroy', $s->id)); ?>" class="d-inline"
                                        onsubmit="return confirm('Hapus satuan ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>


        
        <div class="card mb-4">
            <div class="card-header">Kategori</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($kat->ckode); ?></td>
                                <td><?php echo e($kat->cnama); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalKategori"
                                        onclick="openEditKategori(<?php echo e($kat->nid); ?>, '<?php echo e($kat->ckode); ?>', '<?php echo e($kat->cnama); ?>')">
                                        Edit
                                    </button>

                                    <form method="POST" action="<?php echo e(route('asset.kategori.destroy', $kat->nid)); ?>"
                                        class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>
        </div>

        
        <div class="card mb-4">
            <div class="card-header">Sub Kategori</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th width="200">Aksi</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $subkategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sub->kategori->cnama); ?></td>
                                <td><?php echo e($sub->ckode); ?></td>
                                <td><?php echo e($sub->cnama); ?></td>
                                <td>
                                    <?php if($sub->fqr): ?>
                                        <span class="badge bg-primary">QR</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Non QR</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalSubKategori"
                                        onclick="openEditSubKategori(
                                            <?php echo e($sub->nid); ?>,
                                            <?php echo e($sub->nidkat); ?>,
                                            '<?php echo e($sub->ckode); ?>',
                                            '<?php echo e($sub->cnama); ?>',
                                            <?php echo e($sub->fqr); ?>

                                        )">
                                        Edit
                                    </button>

                                    <form method="POST" action="<?php echo e(route('asset.subkategori.destroy', $sub->nid)); ?>"
                                        class="d-inline" onsubmit="return confirm('Hapus sub kategori ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="card mb-4">
            <div class="card-header">Asset QR</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>
                            <th>Counter</th>
                            <th>QR Code</th>
                            <th>Tgl Beli</th>
                            <th>Harga Beli</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $assetQr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($qr->department->cname); ?></td>
                                <td><?php echo e($qr->subKategori->kategori->cnama); ?></td>
                                <td><?php echo e($qr->subKategori->cnama); ?></td>
                                <td><?php echo e($qr->nurut); ?></td>
                                <td><?php echo e($qr->cqr); ?></td>

                                
                                <td>
                                    <?php echo e($qr->dbeli ? \Carbon\Carbon::parse($qr->dbeli)->format('d-m-Y') : '-'); ?>

                                </td>

                                
                                <td>
                                    <?php echo e($qr->nbeli ? 'Rp ' . number_format($qr->nbeli, 0, ',', '.') : '-'); ?>

                                </td>

                                <td><?php echo e($qr->cstatus); ?></td>
                                <td><?php echo e($qr->ccatatan); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>
        </div>

        
        <div class="card mb-4">
            <div class="card-header">Asset Non QR</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>
                            <th>Qty</th>
                            <th>Min Stok</th>
                            <th>Satuan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $assetNoQr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nqr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($nqr->department->cname); ?></td>
                                <td><?php echo e($nqr->subKategori->kategori->cnama); ?></td>
                                <td><?php echo e($nqr->subKategori->cnama); ?></td>
                                <td><?php echo e($nqr->nqty); ?></td>
                                <td><?php echo e($nqr->nminstok); ?></td>
                                <td><?php echo e($nqr->satuan?->nama ?? '-'); ?></td>
                                <td><?php echo e($nqr->ccatatan); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
    <div class="modal fade" id="modalSatuan">
        <div class="modal-dialog">
            <form method="POST" id="formSatuan">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="methodSatuan" value="POST">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="titleSatuan">Tambah Satuan</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama Satuan</label>
                            <input type="text" name="nama" id="namaSatuan" class="form-control"
                                placeholder="Pcs / Unit / Set" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary" id="btnSatuan">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    
    <div class="modal fade" id="modalKategori">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo e(route('asset.kategori.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="methodKategori" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Kategori</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Kode</label>
                            <input type="text" name="ckode" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="cnama" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="modalSubKategori">
        <div class="modal-dialog">
            <form method="POST" id="formSubKategori" action="<?php echo e(route('asset.subkategori.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="methodSubKategori" value="POST">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="titleSubKategori">Tambah Sub Kategori</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Kategori</label>
                            <select name="nidkat" class="form-control" required>
                                <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kat->nid); ?>"><?php echo e($kat->cnama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Kode</label>
                            <input type="text" name="ckode" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="cnama" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Jenis Asset</label>
                            <select name="fqr" class="form-control">
                                <option value="1">QR</option>
                                <option value="0">Non QR</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    
    <div class="modal fade" id="modalAsset">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo e(route('asset.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Asset</h5>
                    </div>

                    <div class="modal-body">

                        
                        <div class="mb-2">
                            <label>Lokasi</label>
                            <select name="niddept" class="form-control" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->nid); ?>"><?php echo e($dept->cname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-2">
                            <label>Kategori</label>
                            <select id="filterKategori" class="form-control">
                                <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kat->nid); ?>"><?php echo e($kat->cnama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-2">
                            <label>Sub Kategori</label>
                            <select name="nidsubkat" id="filterSubKategori" class="form-control" required>
                                <?php $__currentLoopData = $subkategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sub->nid); ?>" data-kat="<?php echo e($sub->nidkat); ?>"
                                        data-fqr="<?php echo e($sub->fqr); ?>">
                                        <?php echo e($sub->cnama); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-2">
                            <label>Jenis Asset</label>
                            <input type="text" id="jenisAsset" class="form-control" readonly>
                        </div>

                        
                        <div id="fieldQr" style="display:none">
                            <div class="mb-2">
                                <label>Tanggal Beli</label>
                                <input type="date" name="dbeli" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Harga Beli</label>
                                <input type="number" name="nbeli" class="form-control" min="0">
                            </div>
                        </div>

                        
                        <div id="fieldNonQr" style="display:none">
                            <div class="mb-2">
                                <label>Qty</label>
                                <input type="number" name="nqty" class="form-control" min="1">
                            </div>

                            <div class="mb-2">
                                <label>Min Stok</label>
                                <input type="number" name="nminstok" class="form-control" min="0">
                            </div>

                            <div class="mb-2">
                                <label>Satuan</label>
                                <select name="msatuan_id" class="form-control">
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>"><?php echo e($s->nama); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        
                        <div class="mb-2">
                            <label>Catatan</label>
                            <textarea name="ccatatan" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-warning">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.routeMsatuanStore = "<?php echo e(route('msatuan.store')); ?>";
    </script>

    <script src="<?php echo e(asset('js/asset.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Matahati-Asset\resources\views/Asset/index.blade.php ENDPATH**/ ?>