import './bootstrap';
import Alpine from 'alpinejs';
import './charts/slaChart'

window.Alpine = Alpine;
Alpine.start();

// 🎯 Event saat Livewire selesai navigasi (SPA-like)
document.addEventListener('livewire:navigated', () => {
    console.log("✅ Livewire navigated — halaman baru dimuat tanpa reload.");

    // Reinit semua plugin atau JS custom di sini
    // Misalnya: inisialisasi SweetAlert, Chart, atau plugin lain

    // Contoh: jika kamu pakai dropdown custom
    const dropdowns = document.querySelectorAll('[data-dropdown]');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', () => {
            dropdown.classList.toggle('open');
        });
    });

    // Contoh: jika pakai SweetAlert otomatis
    if (window.Swal) {
        console.log("SweetAlert sudah tersedia siap digunakan di halaman baru.");
    }
});
