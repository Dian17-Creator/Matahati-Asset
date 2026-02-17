<?php $__env->startSection('content'); ?>
    <div class="container">

        <h4 class="mb-3">Master Asset</h4>

        
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                + Kategori
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSubKategori">
                + Sub Kategori
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAsset">
                + Asset
            </button>
        </div>

        
        <div class="card mb-4">
            <div class="card-header">Kategori</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($kat->ckode); ?></td>
                                <td><?php echo e($kat->cnama); ?></td>
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
                                <td><?php echo e($nqr->csatuan); ?></td>
                                <td><?php echo e($nqr->ccatatan); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
    <div class="modal fade" id="modalKategori">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo e(route('asset.kategori.store')); ?>">
                <?php echo csrf_field(); ?>
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="modalSubKategori">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo e(route('asset.subkategori.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Sub Kategori</h5>
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                            <label>Lokasi / Department</label>
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

                        
                        <div class="mb-2">
                            <label>Catatan</label>
                            <textarea name="ccatatan" class="form-control"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-warning">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <script>
        const kategori = document.getElementById('filterKategori');
        const subkat = document.getElementById('filterSubKategori');
        const jenis = document.getElementById('jenisAsset');

        function filterSub() {
            const kat = kategori.value;
            [...subkat.options].forEach(opt => {
                opt.style.display = opt.dataset.kat == kat ? 'block' : 'none';
            });
            subkat.selectedIndex = [...subkat.options].findIndex(o => o.style.display === 'block');
            setJenis();
        }

        function setJenis() {
            const fqr = subkat.options[subkat.selectedIndex].dataset.fqr;
            jenis.value = fqr == 1 ? 'QR' : 'Non QR';
        }

        kategori.addEventListener('change', filterSub);
        subkat.addEventListener('change', setJenis);
        filterSub();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Matahati-Asset\resources\views/Asset/index.blade.php ENDPATH**/ ?>