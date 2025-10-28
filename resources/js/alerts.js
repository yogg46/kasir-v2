import Swal from "sweetalert2";
window.Swal = Swal;

// 🔥 FUNGSI BANTUAN
const isDark = () => document.documentElement.classList.contains("dark");

/**
 * GLOBAL ALERT HANDLER UNTUK LIVEWIRE
 * -----------------------------------
 * - Mendukung Livewire.navigate lifecycle hooks
 * - Mendukung event global: alert & confirm
 */
function registerLivewireAlerts() {
    // 🔔 Event: alert (toast / modal)
    Livewire.on("alert", (...args) => {
        const options = Array.isArray(args[0]) ? args[0][0] : args[0] || {};

        // console.log("Alert ditampilkan:", options.title);

        Swal.fire({
            title: options.title ?? "Berhasil!",
            text: options.text ?? "",
            icon: options.icon ?? "success",
            timer: options.timer ?? 2000,
            showConfirmButton: options.showConfirmButton ?? false,
            position: options.position ?? "center",
            toast: options.toast ?? false,
            background: isDark() ? "#1f2937" : "#fff",
            color: isDark() ? "#f9fafb" : "#111827",
        });
    });
 // Alert Event (Toast Notification)
     Livewire.on("toast", (...args) => {
        const options = Array.isArray(args[0]) ? args[0][0] : args[0] || {};

        Swal.fire({
            title: options.title ?? "Berhasil!",
            text: options.text ?? "",
            icon: options.icon ?? "success",
            timer: options.timer ?? 2000,
            showConfirmButton: options.showConfirmButton ?? false,
            position: options.position ?? "top-end",
            toast: options.toast ?? true,
            background: isDark() ? "#1f2937" : "#fff",
            color: isDark() ? "#f9fafb" : "#111827",
            iconColor: options.icon === "success" ? "#10b981" : 
                       options.icon === "error" ? "#ef4444" : 
                       options.icon === "warning" ? "#f59e0b" : "#3b82f6",
            timerProgressBar: true,
        });
    });

    // ⚠️ Event: confirm (konfirmasi dengan callback)
      Livewire.on("confirm", (...args) => {
        const options = Array.isArray(args[0]) ? args[0][0] : args[0] || {};

        Swal.fire({
            title: options.title ?? "Apakah Anda yakin?",
            text: options.text ?? "Tindakan ini tidak dapat dibatalkan.",
            icon: options.icon ?? "warning",
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText ?? "Ya, lanjutkan!",
            cancelButtonText: options.cancelButtonText ?? "Batal",
            confirmButtonColor: "#3b82f6",
            cancelButtonColor: "#6b7280",
            background: isDark() ? "#1f2937" : "#fff",
            color: isDark() ? "#f9fafb" : "#111827",
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed && options.event) {
                // Dispatch event ke Livewire
                Livewire.dispatch(options.event);
            }
        });
    });
}

// 🧠 INITIAL LOAD
document.addEventListener("livewire:load", () => {
    registerLivewireAlerts();
});

// 🔁 RE-REGISTER PADA NAVIGASI BARU (Livewire 3 SPA Mode)
document.addEventListener("livewire:navigated", () => {
    registerLivewireAlerts();
});

// (Opsional) tampilkan indikator loading
document.addEventListener("livewire:navigating", () => {
    // Swal.fire({
    //     title: "Memuat halaman...",
    //     allowOutsideClick: false,
    //     showConfirmButton: false,
    //     didOpen: () => Swal.showLoading(),
    // });
});

// (Opsional) tutup loading setelah navigasi selesai
document.addEventListener("livewire:navigated", () => {
    Swal.close();
});
