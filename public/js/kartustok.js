// ==============================
// FORMAT DATE
// ==============================
function formatDateLocal(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
}

// ==============================
// PRESET RANGE
// ==============================
function setRange(type) {
    let start, end;
    const today = new Date();

    switch (type) {
        case "today":
            start = end = new Date();
            break;

        case "yesterday":
            const y = new Date();
            y.setDate(y.getDate() - 1);
            start = end = y;
            break;

        case "7days":
            end = new Date();
            start = new Date();
            start.setDate(end.getDate() - 6);
            break;

        case "30days":
            end = new Date();
            start = new Date();
            start.setDate(end.getDate() - 29);
            break;

        case "thisMonth":
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0); // ✅ FIX
            break;

        case "lastMonth":
            start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            end = new Date(today.getFullYear(), today.getMonth(), 0);
            break;

        default:
            return;
    }

    const startEl = document.getElementById("modal_start_date");
    const endEl = document.getElementById("modal_end_date");

    if (!startEl || !endEl) return;

    startEl.value = formatDateLocal(start);
    endEl.value = formatDateLocal(end);
}

// ==============================
// APPLY FILTER
// ==============================
function applyDate() {
    const modalStart = document.getElementById("modal_start_date");
    const modalEnd = document.getElementById("modal_end_date");

    const startInput = document.getElementById("start_date");
    const endInput = document.getElementById("end_date");

    if (!modalStart || !modalEnd || !startInput || !endInput) return;

    startInput.value = modalStart.value;
    endInput.value = modalEnd.value;

    document.getElementById("filterForm").submit();
}

// ==============================
// SYNC SAAT MODAL DIBUKA
// ==============================
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("dateFilterModal");

    if (!modal) return;

    modal.addEventListener("shown.bs.modal", function () {
        const modalStart = document.getElementById("modal_start_date");
        const modalEnd = document.getElementById("modal_end_date");

        const startInput = document.getElementById("start_date");
        const endInput = document.getElementById("end_date");

        if (!modalStart || !modalEnd || !startInput || !endInput) return;

        modalStart.value = startInput.value;
        modalEnd.value = endInput.value;
    });
});

//Searching Kartu Stok
let searchTimeout = null;

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("searchInput");

    if (!input) return;

    input.addEventListener("input", function () {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            document.getElementById("filterForm").submit();
        }, 500); // delay 500ms biar gak spam query
    });
});
