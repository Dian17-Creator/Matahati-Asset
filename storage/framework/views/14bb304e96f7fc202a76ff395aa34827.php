<!-- Modal Tambah Gaji -->
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    /* cosmetic for formatted input */
    #nominal_gaji_fmt {
        text-align: right;
    }
</style>

<div class="modal fade" id="modalTambahGaji" tabindex="-1" aria-labelledby="modalTambahGajiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo e(route('tunjangan.store')); ?>" method="POST" id="formTambahGaji">
                <?php echo csrf_field(); ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahGajiLabel">Tambah Data Gaji & Tunjangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-4">

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Pilih Karyawan</label>
                            <select name="nid" id="nid" class="form-select" required>
                                <option value="">— Pilih Karyawan —</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->nid); ?>"><?php echo e($u->cname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_berlaku" class="form-control" required>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Jenis Gaji</label>
                            <select name="jenis_gaji" id="jenis_gaji" class="form-select" required>
                                <option value="pokok">Gaji Pokok (Bulanan)</option>
                                <option value="harian">Gaji Harian</option>
                            </select>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Nominal Gaji</label>

                            <!-- visible formatted field for UX -->
                            <input type="text" id="nominal_gaji_fmt" class="form-control text-end" placeholder=""
                                autocomplete="off">

                            <!-- actual value sent to server -->
                            <input type="hidden" id="nominal_gaji" name="nominal_gaji" value="">
                        </div>

                        <h6 class="text-primary fw-bold mt-3">Tunjangan</h6>

                        
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Makan</label>
                            <input type="number" id="tunjangan_makan" name="tunjangan_makan"
                                class="form-control text-end" value="0">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <input type="number" id="tunjangan_jabatan" name="tunjangan_jabatan"
                                class="form-control text-end" value="0">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Transport</label>
                            <input type="number" id="tunjangan_transport" name="tunjangan_transport"
                                class="form-control text-end" value="0">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Luar Kota</label>
                            <input type="number" id="tunjangan_luar_kota" name="tunjangan_luar_kota"
                                class="form-control text-end" value="0">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Masa Kerja</label>
                            <input type="number" id="tunjangan_masa_kerja" name="tunjangan_masa_kerja"
                                class="form-control text-end" value="0">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light border" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary px-4" type="submit">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const userSelect = document.getElementById('nid');
        const latestUrlBase = "<?php echo e(url('penggajian/tunjangan/latest')); ?>";

        const fmt = document.getElementById('nominal_gaji_fmt');
        const hiddenNominal = document.getElementById('nominal_gaji');

        // helper: keep only digits (no separators)
        function onlyDigits(str) {
            return (str || '').toString().replace(/[^\d]/g, '');
        }

        // helper: thousand separator (dot)
        function formatThousands(n) {
            if (!n) return '';
            return n.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // set both visible and hidden values
        function setNominalValue(rawNumber) {
            if (rawNumber === null || typeof rawNumber === 'undefined' || rawNumber === '') {
                fmt.value = '';
                hiddenNominal.value = '';
                return;
            }

            const rawStr = String(rawNumber).trim();

            // try parse numeric first (handles "2400000.00", "2,400,000.00", "2400000")
            // normalize comma thousand separators if any -> remove commas
            const normalized = rawStr.replace(/,/g, '');

            // parse float (will accept "2400000.00")
            const parsed = parseFloat(normalized);

            let digits;
            if (!isNaN(parsed) && isFinite(parsed)) {
                // round to integer, then stringify
                digits = String(Math.round(parsed));
            } else {
                // fallback: strip any non-digit characters (handles "2.470.800")
                digits = onlyDigits(rawStr);
            }

            // finally format and set
            fmt.value = digits ? formatThousands(digits) : '';
            hiddenNominal.value = digits ? digits : '';
        }

        // initialize empty
        setNominalValue('');

        // formatting while typing
        fmt.addEventListener('input', function(e) {
            const cursor = fmt.selectionStart;
            const digits = onlyDigits(fmt.value);
            fmt.value = digits ? formatThousands(digits) : '';
            hiddenNominal.value = digits ? digits : '';
            setNominalValue(fmt.value);
            // caret restore is omitted for simplicity (works ok on most cases)
        });

        // when user changes selection, fetch latest and fill fields
        userSelect.addEventListener('change', function() {
            let nid = this.value;
            if (!nid) return resetFields();

            fetch(`${latestUrlBase}/${nid}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.found) {
                        resetFields();
                        return;
                    }

                    document.getElementById('jenis_gaji').value = data.jenis_gaji || 'pokok';

                    // data.nominal_gaji could be null or numeric string
                    // setNominalValue expects raw numeric string (e.g. "2470800" or 2470800)
                    setNominalValue(data.nominal_gaji ?? '');

                    document.getElementById('tunjangan_makan').value = data.t_makan ?? 0;
                    document.getElementById('tunjangan_jabatan').value = data.t_jabatan ?? 0;
                    document.getElementById('tunjangan_transport').value = data.t_transport ?? 0;
                    document.getElementById('tunjangan_luar_kota').value = data.t_luarkota ?? 0;
                    document.getElementById('tunjangan_masa_kerja').value = data.t_masakerja ?? 0;
                })
                .catch(err => {
                    console.error(err);
                    resetFields();
                });
        });

        function resetFields() {
            setNominalValue('');
            document.getElementById('tunjangan_makan').value = 0;
            document.getElementById('tunjangan_jabatan').value = 0;
            document.getElementById('tunjangan_transport').value = 0;
            document.getElementById('tunjangan_luar_kota').value = 0;
            document.getElementById('tunjangan_masa_kerja').value = 0;
        }

        // ensure hidden nominal is set before submit (extra safety)
        document.getElementById('formTambahGaji').addEventListener('submit', function() {
            hiddenNominal.value = onlyDigits(fmt.value);
        });
    });
</script>
<?php /**PATH /home/matahati/domains/absensi.matahati.my.id/public_html/laravel/resources/views/penggajian/modal_tambah_gaji.blade.php ENDPATH**/ ?>