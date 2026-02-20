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

    const modalSatuan = document.getElementById("modalSatuan");
    if (modalSatuan) {
        modalSatuan.addEventListener("hidden.bs.modal", () => {
            formSatuan.reset();
            formSatuan.action = window.routeMsatuanStore;
            methodSatuan.value = "POST";
            titleSatuan.innerText = "Tambah Satuan";
            btnSatuan.innerText = "Simpan";
        });
    }

    // MODAL KATEGORI (TIDAK DIUBAH)
    window.openEditKategori = (id, kode, nama) => {
        const modal = document.getElementById("modalKategori");
        const form = modal.querySelector("form");
        const titleKategori = modal.querySelector("#titleKategori");

        form.action = `/asset/kategori/${id}`;
        document.getElementById("methodKategori").value = "PUT";

        titleKategori.innerText = "Edit Kategori";

        form.querySelector('[name="ckode"]').value = kode;
        form.querySelector('[name="cnama"]').value = nama;
    };

    const modalKategori = document.getElementById("modalKategori");
    if (modalKategori) {
        modalKategori.addEventListener("hidden.bs.modal", () => {
            const form = modalKategori.querySelector("form");
            form.reset();

            form.action = "/asset/kategori";
            document.getElementById("methodKategori").value = "POST";
            modalKategori.querySelector("#titleKategori").innerText =
                "Tambah Kategori";
        });
    }

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
        // debug: log incoming values to help diagnose empty select
        console.debug("openEditSubKategori called", {
            id,
            nidkat,
            kode,
            nama,
            fqr,
        });
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
        if (selectFqr) {
            console.debug(
                "selectFqr options before set",
                [...selectFqr.options].map((o) => ({
                    value: o.value,
                    text: o.text,
                    selected: o.selected,
                })),
            );
            const val = String(fqr);
            let matched = false;
            [...selectFqr.options].forEach((opt) => {
                if (opt.value === val) {
                    opt.selected = true;
                    matched = true;
                }
            });
            // ensure selectedIndex reflects the chosen option (some browsers/skins need this)
            const idx = [...selectFqr.options].findIndex(
                (o) => o.value === val,
            );
            if (idx >= 0) selectFqr.selectedIndex = idx;
            // trigger change so any UI wrappers update
            try {
                selectFqr.dispatchEvent(new Event("change"));
            } catch (e) {}
            console.debug("selectFqr after set attempt", {
                val,
                matched,
                current: selectFqr.value,
            });
            if (!matched) {
                // fallback to direct assignment if no option matched
                selectFqr.value = val;
            }
        }
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

    // AJAX pagination - Master Satuan
    $(document).on("click", "#satuan-wrapper .pagination a", function (e) {
        e.preventDefault();

        let url = $(this).attr("href");

        $.get(url, function (data) {
            $("#satuan-wrapper").html(data);
        });
    });

    // AJAX pagination - Master Kategori
    $(document).on("click", "#kategori-wrapper .pagination a", function (e) {
        e.preventDefault();
        e.stopPropagation();

        let url = $(this).attr("href");

        $.get(url, function (data) {
            $("#kategori-wrapper").html(data);
        });
    });

    subkatSelect.addEventListener("change", async () => {
        const selected = subkatSelect.options[subkatSelect.selectedIndex];
        if (!selected) return;

        const isQr = selected.dataset.fqr === "1";

        if (isQr) {
            kodeAsset.readOnly = true;
            kodeHint.innerText = "Kode dibuat otomatis (QR)";
            kodeAsset.value = "Loading...";

            try {
                const res = await fetch(
                    `${window.routeGenerateKode}?ngrpid=${selected.value}`,
                    {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    },
                );

                if (!res.ok) throw new Error("HTTP error");

                const data = await res.json();
                kodeAsset.value = data.kode;
            } catch (err) {
                console.error(err);
                kodeAsset.value = "";
                kodeHint.innerText = "Gagal generate kode";
            }
        } else {
            kodeAsset.readOnly = false;
            kodeAsset.value = "";
            kodeHint.innerText = "Isi kode manual (Non-QR)";
        }
    });
});
