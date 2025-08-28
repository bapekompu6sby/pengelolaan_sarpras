'use strict';

$(document).ready(function() {
    // ==== DataTable: Daftar Kegiatan ====
    console.log("kegiatan")
    $('#datatable-kegiatan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[2, 'asc']],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            zeroRecords: 'Tidak ada hasil yang cocok',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data tersedia',
            infoFiltered: '(disaring dari total _MAX_ data)'
        },
        columnDefs: [
            { targets: [1], className: 'fw-semibold' },
            { targets: [4], className: 'text-nowrap' }
        ]
    });

    // ==== Slider: Auto-rotate 10 detik, pause on hover, swipe, keyboard ====
    const slider = document.getElementById('kegiatanSlider');
    if (!slider) return; // tidak ada event hari ini

    const track = document.getElementById('sliderTrack');
    const dotsWrap = document.getElementById('sliderDots');
    const dots = Array.from(dotsWrap.querySelectorAll('.dot'));
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const progress = document.getElementById('sliderProgress');

    const SLIDE_MS = 10000; // 10 detik
    const total = dots.length;
    let index = 0;
    let timerId = null;
    let isPaused = false;

    function updateUI() {
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === index));
        progress.style.transition = 'none';
        progress.style.width = '0%';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                progress.style.transition = `width ${SLIDE_MS}ms linear`;
                progress.style.width = '100%';
            });
        });
    }

    function next() {
        index = (index + 1) % total;
        updateUI();
    }

    function prev() {
        index = (index - 1 + total) % total;
        updateUI();
    }

    function play() {
        clearInterval(timerId);
        timerId = setInterval(() => {
            if (!isPaused) next();
        }, SLIDE_MS);
        progress.style.transition = `width ${SLIDE_MS}ms linear`;
        progress.style.width = '100%';
    }

    function pause() {
        isPaused = true;
        progress.style.transition = 'none';
    }

    function resume() {
        isPaused = false;
        updateUI();
    }

    // Init
    updateUI();
    play();

    // Events
    btnNext.addEventListener('click', () => {
        next();
        play();
    });
    btnPrev.addEventListener('click', () => {
        prev();
        play();
    });
    dots.forEach((d) => d.addEventListener('click', (e) => {
        index = parseInt(e.currentTarget.dataset.index, 10);
        updateUI();
        play();
    }));
    slider.addEventListener('mouseenter', pause);
    slider.addEventListener('mouseleave', resume);
    slider.addEventListener('focusin', pause);
    slider.addEventListener('focusout', resume);
    slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            btnNext.click();
        }
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            btnPrev.click();
        }
    });

    // Touch swipe (basic)
    let startX = 0,
        dx = 0;
    slider.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        dx = 0;
        pause();
    }, {
        passive: true
    });
    slider.addEventListener('touchmove', (e) => {
        dx = e.touches[0].clientX - startX;
    }, {
        passive: true
    });
    slider.addEventListener('touchend', () => {
        if (Math.abs(dx) > 40) {
            dx < 0 ? next() : prev();
        }
        resume();
        play();
    });

    // ==== Smooth scroll ke baris tabel + highlight ====
    $(document).on('click', 'a[href^="#row-"]', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        target.classList.add('table-active');
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        setTimeout(() => target.classList.remove('table-active'), 1600);
    });
});
