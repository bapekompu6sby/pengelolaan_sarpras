$(document).ready(function () {
    "use strict";

    $("#datatable").DataTable();

    $("#datatable1").DataTable({
        order: [[1, "asc"]],
        columns: [
            { orderable: false }, // Kolom kedua tanpa sorting
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            { orderable: false }, // Kolom kedua tanpa sorting
        ],
    });

    $("#datatable2").DataTable({
        order: [[1, "asc"]],
    });

    $("#datatable3").DataTable({
        scrollX: true,
    });

    $("#datatable4 tfoot th").each(function () {
        var title = $(this).text();
        $(this).html(
            '<input type="text" class="form-control" placeholder="Search ' +
                title +
                '" />'
        );
    });

    // DataTable
    var table = $("#datatable4").DataTable({
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;

                    $("input", this.footer()).on(
                        "keyup change clear",
                        function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        }
                    );
                });
        },
    });
});

$(document).ready(function () {
    "use strict";

    // Tabel kegiatan
    $("#datatable-kegiatan").DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[2, "asc"]], 
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada hasil yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari total _MAX_ data)",
        },
        columnDefs: [
            { targets: [1], className: "fw-semibold" }, // kasih bold untuk kolom kegiatan
            { targets: [4], className: "text-nowrap" }, // biar no HP tidak kepotong
        ],
    });
});
