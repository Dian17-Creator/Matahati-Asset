document.addEventListener("DOMContentLoaded", () => {
    /* =========================
     * ASSET FORM (QR / NON QR)
     * KHUSUS MODAL ASSET
     * ========================= */
    const modalAsset = document.getElementById("modalAsset");

    if (modalAsset) {
        const kategori = modalAsset.querySelector("#filterKategori");
        const subkat = modalAsset.querySelector("#filterSubKategori");
        const jenis = modalAsset.querySelector("#jenisAsset");
        const fieldQr = modalAsset.querySelector("#fieldQr");
        const fieldNonQr = modalAsset.querySelector("#fieldNonQr");

        if (kategori && subkat) {
            const filterSub = () => {
                const kat = kategori.value;

                [...subkat.options].forEach((opt) => {
                    opt.style.display =
                        opt.dataset.kat == kat ? "block" : "none";
                });

                // 🔥 FIX UTAMA: jangan override pilihan yang sudah ada
                if (!subkat.value) {
                    const firstVisible = [...subkat.options].find(
                        (o) => o.style.display === "block",
                    );
                    if (firstVisible) {
                        subkat.value = firstVisible.value;
                    }
                }

                setJenis();
            };

            const setJenis = () => {
                const selected = subkat.options[subkat.selectedIndex];
                if (!selected) return;

                const isQr = String(selected.dataset.fqr) === "1";

                jenis.value = isQr ? "QR" : "Non QR";
                fieldQr.style.display = isQr ? "block" : "none";
                fieldNonQr.style.display = isQr ? "none" : "block";
            };

            kategori.addEventListener("change", filterSub);
            subkat.addEventListener("change", setJenis);

            // init saat modal asset dibuka
            filterSub();
        }
    }

    /* =========================
     * MODAL SATUAN (TIDAK DIUBAH)
     * ========================= */
    const formSatuan = document.getElementById("formSatuan");
    const methodSatuan = document.getElementById("methodSatuan");
    const namaSatuan = document.getElementById("namaSatuan");
    const titleSatuan = document.getElementById("titleSatuan");
    const btnSatuan = document.getElementById("btnSatuan");

    window.openCreateSatuan = () => {
        formSatuan.action = window.routeMsatuanStore;
        methodSatuan.value = "POST";
        namaSatuan.value = "";
        titleSatuan.innerText = "Tambah Satuan";
        btnSatuan.innerText = "Simpan";
    };

    window.openEditSatuan = (id, nama) => {
        formSatuan.action = `/msatuan/${id}`;
        methodSatuan.value = "PUT";
        namaSatuan.value = nama;
        titleSatuan.innerText = "Edit Satuan";
        btnSatuan.innerText = "Update";
    };

    /* =========================
     * MODAL KATEGORI (TIDAK DIUBAH)
     * ========================= */
    window.openEditKategori = (id, kode, nama) => {
        const form = document.querySelector("#modalKategori form");

        form.action = `/asset/kategori/${id}`;
        document.getElementById("methodKategori").value = "PUT";
        form.querySelector('[name="ckode"]').value = kode;
        form.querySelector('[name="cnama"]').value = nama;
    };

    /* =========================
     * MODAL SUB KATEGORI (FIX)
     * ========================= */
    window.openCreateSubKategori = () => {
        const form = document.getElementById("formSubKategori");

        form.reset();
        form.action = "/asset/subkategori";
        document.getElementById("methodSubKategori").value = "POST";
        document.getElementById("titleSubKategori").innerText =
            "Tambah Sub Kategori";
    };

    window.openEditSubKategori = (id, nidkat, kode, nama, fqr) => {
        const form = document.getElementById("formSubKategori");

        form.reset();
        form.action = `/asset/subkategori/${id}`;
        document.getElementById("methodSubKategori").value = "PUT";
        document.getElementById("titleSubKategori").innerText =
            "Edit Sub Kategori";

        form.querySelector('[name="nidkat"]').value = nidkat;
        form.querySelector('[name="ckode"]').value = kode;
        form.querySelector('[name="cnama"]').value = nama;

        const selectFqr = form.querySelector('[name="fqr"]');
        selectFqr.value = String(fqr);
    };

    const modalSub = document.getElementById("modalSubKategori");
    if (modalSub) {
        modalSub.addEventListener("hidden.bs.modal", () => {
            const form = document.getElementById("formSubKategori");
            form.reset();

            form.action = "/asset/subkategori";
            document.getElementById("methodSubKategori").value = "POST";
            document.getElementById("titleSubKategori").innerText =
                "Tambah Sub Kategori";
        });
    }
});
