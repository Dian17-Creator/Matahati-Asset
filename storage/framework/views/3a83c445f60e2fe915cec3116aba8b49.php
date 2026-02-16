<?php $__env->startSection('content'); ?>

    <div class="container mt-4">
        <?php if($authUser->fsuper == 1 || $authUser->fhrd == 1): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Manajemen Jadwal & Shift</h2>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    + Tambah Shift Baru
                </button>
            </div>
        <?php endif; ?>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($authUser->fsuper == 1 || $authUser->fhrd == 1): ?>
            
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Daftar Shift</div>
                <div class="card-body">
                    <table class="table table-bordered align-middle text-center">
                        <thead>
                            <tr>
                                <th>Nama Shift</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Mulai (Split)</th>
                                <th>Selesai (Split)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $masters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($shift->cname); ?></td>
                                    <td><?php echo e(substr($shift->dstart, 0, 5)); ?></td>
                                    <td><?php echo e(substr($shift->dend, 0, 5)); ?></td>
                                    <td>
                                        <?php echo e($shift->dstart2 ? substr($shift->dstart2, 0, 5) : '-'); ?>

                                    </td>
                                    <td>
                                        <?php echo e($shift->dend2 ? substr($shift->dend2, 0, 5) : '-'); ?>

                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editShiftModal<?php echo e($shift->nid); ?>">
                                                Edit
                                            </button>

                                            
                                            <form action="<?php echo e(url('/schedule/' . $shift->nid)); ?>" method="POST"
                                                onsubmit="return confirm('Hapus shift ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>



                                
                                <div class="modal fade" id="editShiftModal<?php echo e($shift->nid); ?>" tabindex="-1"
                                    aria-labelledby="editShiftModalLabel<?php echo e($shift->nid); ?>" aria-hidden="true">

                                    <?php if($errors->has('split') && session('edit_shift_id') == $shift->nid): ?>
                                        <div class="alert alert-danger">
                                            <?php echo e($errors->first('split')); ?>

                                        </div>
                                    <?php endif; ?>

                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="<?php echo e(url('/schedule/' . $shift->nid)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary text-white">
                                                    <h5 class="modal-title" id="editShiftModalLabel<?php echo e($shift->nid); ?>">
                                                        Edit
                                                        Shift</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Nama Shift</label>
                                                        <input type="text" name="cname" class="form-control"
                                                            value="<?php echo e($shift->cname); ?>" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Mulai</label>
                                                            <input type="time" name="dstart" class="form-control"
                                                                value="<?php echo e(substr($shift->dstart, 0, 5)); ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Selesai</label>
                                                            <input type="time" name="dend" class="form-control"
                                                                value="<?php echo e(substr($shift->dend, 0, 5)); ?>" required>
                                                        </div>
                                                        <div class="d-flex align-items-center my-3">
                                                            <div class="flex-grow-1 border-top border-secondary"></div>
                                                            <span class="mx-3 fw-semibold text-secondary">SPLIT</span>
                                                            <div class="flex-grow-1 border-top border-secondary"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Mulai</label>
                                                            <input type="time" name="dstart2" class="form-control"
                                                                value="<?php echo e(substr($shift->dstart2, 0, 5)); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Jam Selesai</label>
                                                            <input type="time" name="dend2" class="form-control"
                                                                value="<?php echo e(substr($shift->dend2, 0, 5)); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success text-white">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-muted">Belum ada data shift</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>Atur Shift Karyawan</span>
                <!-- Tombol Import jadi buka modal -->
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importScheduleModal">
                    + Import Jadwal User
                </button>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('schedule.generate')); ?>" method="POST" id="scheduleForm">
                    <?php echo csrf_field(); ?>
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-4">
                            <label>Pilih Karyawan</label>
                            <select name="nuserid" class="form-select" required>
                                <option value="">-- pilih karyawan --</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->nid); ?>"><?php echo e($user->cname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label>Filter Kalender</label>
                            <select id="filterPeriode" class="form-select">
                                <option value="">-- Pilih Periode --</option>
                                <option value="this_week">Minggu Ini</option>
                                <option value="last_week">Minggu Lalu</option>
                                <option value="next_week">Minggu Depan</option>
                                <option value="this_month">Bulan Ini</option>
                                <option value="next_month">Bulan Depan</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Atur</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <?php if(auth()->user()->fhrd == 1): ?>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Daftar Kontrak Kerja Karyawan</span>
                    <?php if(auth()->user()->fhrd == 1): ?>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addContractModal">
                            + Tambah Kontrak
                        </button>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pegawai</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Akhir</th>
                                <th>Tipe Kontrak</th>
                                <th>Status</th>
                                <th>Sisa Hari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    
                                    <td><?php echo e($contract->user->cname ?? '-'); ?></td>

                                    
                                    <td>
                                        <?php echo e($contract->dstart ? \Carbon\Carbon::parse($contract->dstart)->format('d/m/Y') : '-'); ?>

                                    </td>

                                    
                                    <td>
                                        <?php echo e($contract->dend ? \Carbon\Carbon::parse($contract->dend)->format('d/m/Y') : '-'); ?>

                                    </td>

                                    
                                    <td>
                                        <?php if($contract->ctermtype === 'probation'): ?>
                                            <span class="badge bg-warning text-dark">Probation</span>
                                        <?php elseif($contract->ctermtype === 'promotion'): ?>
                                            <span class="badge bg-info text-dark">Promotion</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Evaluation</span>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <?php if($contract->cstatus === 'active'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Dihentikan</span>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <?php
                                            // pastikan remaining_days ada; kalau tidak, hitung dari dend
                                            if (isset($contract->remaining_days)) {
                                                $remaining = floor($contract->remaining_days);
                                            } else {
                                                $remaining = null;
                                                if ($contract->dend) {
                                                    $remaining = \Carbon\Carbon::parse($contract->dend)->diffInDays(
                                                        \Carbon\Carbon::now(),
                                                        false,
                                                    );
                                                }
                                            }
                                        ?>

                                        <?php if(!is_null($remaining) && $remaining > 0): ?>
                                            <span class="text-success fw-bold"><?php echo e($remaining); ?> hari lagi</span>
                                        <?php elseif($remaining === 0): ?>
                                            <span class="text-warning fw-bold">Habis hari ini</span>
                                        <?php else: ?>
                                            <span class="text-danger fw-bold">Sudah Habis</span>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <?php if(auth()->user()->fhrd == 1): ?>
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                data-bs-target="#editContractModal<?php echo e($contract->nid); ?>">
                                                Edit
                                            </button>

                                            <form action="<?php echo e(route('schedule.contract.destroy', $contract->nid)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus kontrak ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                
                                <div class="modal fade" id="editContractModal<?php echo e($contract->nid); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="<?php echo e(route('schedule.contract.update', $contract->nid)); ?>"
                                            method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">Edit Kontrak</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Pegawai</label>
                                                        <select name="nuserid" class="form-select" required>
                                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($user->nid); ?>"
                                                                    <?php echo e($contract->nuserid == $user->nid ? 'selected' : ''); ?>>
                                                                    <?php echo e($user->cname); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tanggal Mulai</label>
                                                            <input type="date" name="dstart" class="form-control"
                                                                value="<?php echo e(\Carbon\Carbon::parse($contract->dstart)->format('Y-m-d')); ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tanggal Akhir</label>
                                                            <input type="date" name="dend" class="form-control"
                                                                value="<?php echo e(\Carbon\Carbon::parse($contract->dend)->format('Y-m-d')); ?>"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Durasi</label>
                                                            <select name="nterm" class="form-select">
                                                                <option value="3"
                                                                    <?php echo e($contract->nterm == 3 ? 'selected' : ''); ?>>3 bulan
                                                                </option>
                                                                <option value="6"
                                                                    <?php echo e($contract->nterm == 6 ? 'selected' : ''); ?>>6 bulan
                                                                </option>
                                                                <option value="12"
                                                                    <?php echo e($contract->nterm == 12 ? 'selected' : ''); ?>>12 bulan
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tipe Kontrak</label>
                                                            <select name="ctermtype" class="form-select">
                                                                <option value="probation"
                                                                    <?php echo e($contract->ctermtype == 'probation' ? 'selected' : ''); ?>>
                                                                    Probation</option>
                                                                <option value="promotion"
                                                                    <?php echo e($contract->ctermtype == 'promotion' ? 'selected' : ''); ?>>
                                                                    Promotion</option>
                                                                <option value="evaluation"
                                                                    <?php echo e($contract->ctermtype == 'evaluation' ? 'selected' : ''); ?>>
                                                                    Evaluation</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Status</label>
                                                        <select name="cstatus" class="form-select">
                                                            <option value="active"
                                                                <?php echo e($contract->cstatus == 'active' ? 'selected' : ''); ?>>
                                                                Aktif
                                                            </option>
                                                            <option value="terminated"
                                                                <?php echo e($contract->cstatus == 'terminated' ? 'selected' : ''); ?>>
                                                                Dihentikan</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success text-white">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-muted">Belum ada data kontrak kerja</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    

    <div class="modal fade" id="importScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form id="importScheduleForm" action="<?php echo e(route('file-import-schedule')); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-upload me-2"></i>Import Jadwal Kerja
                        </h5>
                    </div>

                    <div class="modal-body">
                        
                        <?php if($errors->has('file')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo e($errors->first('file')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if($sukses = Session::get('userschedulesuccess')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <strong><?php echo e($sukses); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if($warnings = Session::get('userschedule_warning')): ?>
                            <div class="alert alert-warning alert-dismissible fade show">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $warnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($warning); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="text-center">
                            <h5 class="mb-3">Pilih File Jadwal (format .xlsx)</h5>
                            <div class="form-group" style="max-width: 500px; margin: 0 auto;">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile"
                                        accept=".xlsx" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?php echo e(url('/schedule')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="addShiftModalLabel">Tambah Shift Baru</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Shift</label>
                            <input type="text" name="cname" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jam Mulai</label>
                                <input type="time" name="dstart" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Selesai</label>
                                <input type="time" name="dend" class="form-control" required>
                            </div>
                            <div class="d-flex align-items-center my-3">
                                <div class="flex-grow-1 border-top border-secondary"></div>
                                <span class="mx-3 fw-semibold text-secondary">SPLIT</span>
                                <div class="flex-grow-1 border-top border-secondary"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Mulai</label>
                                <input type="time" name="dstart2" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Selesai</label>
                                <input type="time" name="dend2" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 🟢 Modal Tambah Kontrak -->
    <div class="modal fade" id="addContractModal" tabindex="-1" aria-labelledby="addContractModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?php echo e(route('schedule.contract.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="addContractModalLabel">Tambah Kontrak Baru</h5>
                    </div>

                    <div class="modal-body">

                        
                        <div class="mb-3">
                            <label>Pegawai</label>
                            <select name="nuserid" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->nid); ?>"><?php echo e($user->cname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Mulai</label>
                                <input type="date" id="contract_start" name="dstart" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Akhir</label>
                                <input type="date" id="contract_end" name="dend" class="form-control" required>
                            </div>
                        </div>

                        
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label>Durasi Kontrak</label>
                                <select id="contract_duration" name="nterm" class="form-select" required>
                                    <option value="">-- Pilih Durasi --</option>
                                    <option value="3">3 Bulan</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="12">12 Bulan</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tipe Kontrak</label>
                                <select name="ctermtype" class="form-select" required>
                                    <option value="probation">Probation</option>
                                    <option value="promotion">Promotion</option>
                                    <option value="evaluation">Evaluation</option>
                                </select>
                            </div>
                        </div>

                    </div> 

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/schedule.js')); ?>?v=<?php echo e(time()); ?>"></script>

        <?php if(session('edit_shift_id')): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const modalId = "editShiftModal<?php echo e(session('edit_shift_id')); ?>";
                    const modalEl = document.getElementById(modalId);
                    if (modalEl) {
                        new bootstrap.Modal(modalEl).show();
                    }
                });
            </script>
        <?php endif; ?>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/schedule/index.blade.php ENDPATH**/ ?>