const driver = window.driver.js.driver;
const demoBtn = document.getElementById('demo-btn');

const driverObj = driver({
    allowClose: true,
    showProgress: true,
    steps: [
        { 
            element: '#dashboard',
            popover: { 
                title: 'Dashboard Menu', 
                description: 'Berisi rangkuman dari semua data yang terdapat dalam sistem menajemen ruangan', 
                side: "right", 
                align: 'start',
                onNextClick: () => {
                    document.getElementById('data-master').click();
                    driverObj.moveNext();
                },
            }
        },
        { 
            element: '#data-ruangan',
            popover: { 
                title: 'Data Ruangan', 
                description: 'Berisi data ruangan yang tersedia dan dapat digunakan untuk kegiatan', 
                side: "right", 
                align: 'start' 
            }
        },
        { 
            element: '#data-peminjaman',
            popover: { 
                title: 'Data Peminjaman Ruangan', 
                description: 'Berisi rekap data peminjaman ruangan yang telah dilakukan', 
                side: "right", 
                align: 'start' 
            }
        },
        { 
            element: '#data-asrama',
            popover: { 
                title: 'Data Asrama & Paviliun', 
                description: 'Berisi daftar penghuni asrama dan paviliun yang terdaftar dalam sistem', 
                side: "right", 
                align: 'start',
                onNextClick: () => {
                    document.getElementById('transaction').click();
                    driverObj.moveNext();
                }
            }
        },
        {
            element: '#trans-ruangan',
            popover: { 
                title: 'Penjadwalan Ruangan', 
                description: 'Fitur ini berguna untuk melakukan penjadwalan ruangan yang akan digunakan untuk kegiatan', 
                side: "right", 
                align: 'start' 
            }
        },
        {
            element: '#trans-asrama',
            popover: { 
                title: 'Booking Asrama & Paviliun', 
                description: 'Fitur ini berguna untuk melakukan booking asrama dan paviliun yang akan ditempati', 
                side: "right", 
                align: 'start'
            }
        },
        {
            element: '#user-profile',
            popover: { 
                title: 'Profile', 
                description: 'Untuk melakukan edit username, email, dan password', 
                side: "bottom", 
                align: 'start' 
            }
        },
        {
            element: '#btn-logout',
            popover: { 
                title: 'Logout', 
                description: 'Logout dari akun saat ini', 
                side: "bottom", 
                align: 'start' 
            }
        },
        { 
            element: '#kegiatan', 
            popover: { 
                title: 'Daftar Kegiatan',
                description: 'Berisi kegiatan yang akan mendatang dan sedang berlangsung.',
                side: "left",
                align: 'start' 
            }
        },
        { 
            element: '#carouselExample', 
            popover: { 
                title: 'Detail Kegiatan', 
                description: 'Slideshow yang berisi detail kegiatan yang akan datang dan sedanng berlangsung.',
                side: "right", 
                align: 'start' 
            }
        },
        { 
            element: '#paviliun-sum',
            popover: { 
                title: 'Ringkasan Paviliun', 
                description: 'Berisi ringkasan jumlah kamar pada paviliun yang terisi dan kosong.', 
                side: "bottom", 
                align: 'start' 
            }
        },
        { 
            element: '#asrama-sum',
            popover: { 
                title: 'Rigkasan Asrama', 
                description: 'Berisi ringkasan jumlah kamar asrama yang terisi dan kosong.', 
                side: "bottom", 
                align: 'start' 
            }
        },
        { 
            element: '#calendar', 
            popover: { 
                title: 'Kalender', 
                description: 'Berisi kalender yang menampilkan seluruh kegiatan baik yang akan datang dan yang sudah selesai dilaksanakan', 
                side: "left", 
                align: 'start' 
            }
        },
        { 
            element: '#more-btn',
            popover: {
            title: 'More Button', 
            description: 'Adalah sebuah tombol shortcut untuk mengakses data master dari masing - masing fitur' }
        },
        {
            element: '#more-btn2',
        },
        {
            element: '#more-btn3',
        }
    ]
});

demoBtn.addEventListener('click', () => {
    driverObj.drive();
});
