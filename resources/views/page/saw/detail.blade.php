@extends('admin.layout')

@section('content')

<h2 class="mt-5">Data Alternatif</h2>

<!-- Button Actions -->
<div class="mb-3">
    <button onclick="window.location.href='{{ url('/keputusan') }}'" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</button>
</div>

<!-- Criteria Cards -->
<div class="text-end mb-3">
    <button id="addKriteriaButton" class="btn btn-success" style="float: right; margin-bottom: 10px;">
        <i class="fa fa-plus"></i> Tambah Kriteria
    </button>
    <button class="btn btn-info" style="float: right; margin: 0 10px 10px 0;" onclick="showRincianBobot({{ $id }})">
        <i class="fa fa-list"></i> Rincian Bobot
    </button>
</div>

<div class="mt-5">
    <h2 class="mb-4 text-center"><i class="fa fa-tasks"></i> Kriteria</h2>

    <div class="container-fluid">
        <!-- Horizontal Scrollable Container for Criteria Cards -->
        <div class="row" id="criteriaCards">
            @foreach($data as $index => $d)
                <!-- Tampilkan 6 card pertama saja -->
                <div class="col-md-4 mb-4 criteria-card {{ $index >= 6 ? 'd-none' : '' }}" id="card-{{ $index }}">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-dark">{{ $d->kriteria }}</h5>
                            <ul class="list-unstyled">
                                <li><strong>Nama Kriteria:</strong> <span class="text-muted">{{ $d->nama }}</span></li>
                                <li><strong>Bobot Kriteria:</strong> <span class="text-muted">{{ $d->bobot }} / {{ number_format($d->bobot * 100, 0) }}%</span></li>
                                <li><strong>Jenis Kriteria:</strong> <span class="text-muted">{{ $d->jenis }}</span></li>
                            </ul>
                            <!-- Tombol Edit dan Hapus -->
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-outline-primary" onclick="editKriteria({{ $d->id }}, '{{ addslashes($d->kriteria) }}', '{{ addslashes($d->nama) }}', '{{ addslashes($d->bobot) }}', '{{ addslashes($id) }}')">Edit Kriteria</button>
                                <button class="btn btn-outline-danger" onclick="confirmDelete({{ $id }},{{$d->id}})">Hapus Kriteria</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

<!-- Tombol Show More -->
@if (count($data) > 6)
    <div class="d-flex justify-content-center mt-3" id="showMoreContainer">
        <button id="showMoreBtn" class="btn btn-info" onclick="showMoreCards()">Show More</button>
    </div>
@endif

    </div>
</div>

<!-- Action Buttons -->
<div class="card mb-4 border-danger bg-light">
    <div class="card-body">
        <div class="row justify-content-end">
            <div style="padding: 0 1rem 0 0;">
                <button class="btn btn-danger" id="deleteSelectedButton" style="display: none;">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
                <button class="btn" style="background-color: #00796b; color: white;" id="importButton">
                    <i class="fa fa-file-import me-2"></i> Import
                </button>
                <button class="btn" style="background-color: #003366; color: white;">
                    <i class="fa fa-file-export me-2"></i> Export
                </button>
                <button class="btn" style="background-color: #6a5acd; color: white;" id="filter">
                    <i class="fa fa-filter me-2"></i> Filter
                </button>
                <button id="addAlternatifButton" class="btn" style="background-color: #388e3c; color: white;">
                    <i class="fa fa-plus me-2"></i> Tambah Alternatif
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Keputusan -->
<div class="mb-3">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="alternatifTable" cellspacing="0" width="100%">
            <thead class="thead-light">
                <tr>
                    <th rowspan="2" class="text-center"><input type="checkbox" id="selectAll" class="select-all"></th>
                    <th rowspan="2" class="text-center" style="vertical-align: middle;">Nama Alternatif</th>
                    @if (count($data) > 0)
                        <th colspan="{{ count($data) }}" class="text-center">Kriteria</th>
                    @endif
                    <th colspan="3" class="text-center">Other Information</th>
                </tr>
                <tr>
                    @foreach ($data as $item)
                        <th class="text-center">{{ $item->nama }}</th>
                    @endforeach
                    <th class="text-center"><i class="fas fa-trophy"></i> Score</th>
                    <th class="text-center"><i class="fas fa-percent"></i> Persentase</th>
                    <th class="text-center"><i class="fas fa-cogs"></i> Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Your data rows go here -->
            </tbody>
        </table>
        
    </div>
</div>


<div id="loadingSpinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>


@endsection

@push('scripts')
<script>

      // Initialize DataTable with horizontal scrolling enabled
      const table = $('#alternatifTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "scrollX": true, 
        "language": {
            "lengthMenu": "Tampilkan _MENU_ entri per halaman",
            "search": "Cari:",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Tidak ada data yang tersedia",
            "zeroRecords": "Tidak ada entri yang cocok",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Berikutnya"
            }
        },
        "columnDefs": [
            {
                "targets": 1, // Kolom kedua
                "width": "300px" // Lebar yang diinginkan
            }
        ],
        "createdRow": function(row, data, dataIndex) {
            // Menambahkan checkbox di kolom pertama
            $('td:eq(0)', row).html(`
                <input type="checkbox" class="rowCheckbox" data-id="${data[0]}">
            `);
        }
    });

    // Function to load alternatives
    function loadAlternatives() {
        // Show the loading row
        // $('#loadingRow').show();

        $.ajax({
            url: "{{ url('/simple-additive-weighting/' . $id) }}",  // URL untuk mengambil data alternatif secara AJAX
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);

                // Check if the response contains valid data
                    const alternatives = response.data;
                    const scores = response.scores;

                    // Clear table before adding new rows
                    table.clear();

                    // Loop through alternatives and add rows to the table
                    alternatives.forEach((alternatif, index) => {
                        let row = [
                            alternatif.id_alternatif ?? 'Tidak Ditemukan',  // ID Alternatif
                            alternatif.nama_alternatif ?? 'Tidak Ditemukan',  // Nama Alternatif
                        ];

                        response.nilai[index].forEach(function(score) {
                            row.push(score ?? 'Tidak Ditemukan');  // Skor per kriteria
                        });

                        row.push(scores[index] ?? 'Tidak Ditemukan');  // Skor total
                        row.push((scores[index] * 100).toFixed(2) + '%');  // Persentase
                        row.push(`
                            <td>
                                <div class="tombol-container d-flex justify-content-start">
                                    <!-- Tombol Lihat Detail -->
                                    <button class="btn btn-info btn-sm mx-2" id="lihatDetailButton" data-toggle="modal" data-id_keputusan="${response.id}" onclick="loadRumusData(${response.id}, ${alternatif.id_alternatif})" data-target="#detailModal-${alternatif.id_alternatif}">
                                        <i class="fa fa-info-circle"></i> Lihat Detail
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button class="btn btn-primary btn-sm button-action" id="editAlternatifButton" data-id_alternatif="${alternatif.id_alternatif}">
                                        <i class="fa     fa-edit"></i> Edit
                                    </button>
                                </div>
                            </td>
                        `);

                        // Add row to DataTable
                        table.row.add(row).draw();
                    });
               

                // Hide the loading row
                // $('#loadingRow').hide();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
                alert("Failed to load data. Please try again later.");
                // $('#loadingRow').hide();
            }
        });
    }
    $(document).ready(function() {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  

    // let load_data_interval = setInterval(() => {
        loadAlternatives();  // Load alternatives on page load
    // }, 2000);    

    let currentVisibleCards = 6;  // Initially show 6 cards
    const totalCards = {{ count($data) }};  // Total number of cards
    const cards = document.querySelectorAll('.criteria-card');
    const showMoreButton = document.getElementById('showMoreBtn');
    
    // Function to show more cards
    function showMoreCards() {
        // Show all cards
        cards.forEach((card, index) => {
            if (index >= currentVisibleCards) {
                card.classList.remove('d-none'); // Remove d-none to show the card
            }
        });
        
        // Update currentVisibleCards count
        currentVisibleCards = totalCards;

        // Change the Show More button text to "Show Less"
        showMoreButton.textContent = 'Lihat Lebih Sedikit';
        showMoreButton.setAttribute('onclick', 'showLessCards()');  // Change the function to show less
    }

    // Function to show less cards
    function showLessCards() {
        // Hide cards after the first 6
        cards.forEach((card, index) => {
            if (index >= 6) {
                card.classList.add('d-none'); // Add d-none to hide the card
            }
        });

        // Update currentVisibleCards count
        currentVisibleCards = 6;

        // Change the Show More button text back to "Show More"
        showMoreButton.textContent = 'Show More';
        showMoreButton.setAttribute('onclick', 'showMoreCards()');  // Revert the function to show more
    }

    // Initialize the "Show More" button functionality
    if (totalCards > 6) {
        showMoreButton.style.display = 'block';  // Ensure the button is visible if there are more than 6 cards
    } else {
        showMoreButton.style.display = 'none';  // Hide the button if there are no more cards to show
    }

    // Initially, hide cards beyond the first 6
    cards.forEach((card, index) => {
        if (index >= currentVisibleCards) {
            card.classList.add('d-none');  // Hide cards beyond the 6th one
        }
    });

    // Expose the functions globally so that the onclick attribute works
    window.showMoreCards = showMoreCards;
    window.showLessCards = showLessCards;
});


        // Perubahan pada checkbox "select all"
        $('#selectAll').on('change', function() {
            var checked = $(this).prop('checked'); // Menyimpan status checkbox "select all"
            // Toggle tombol delete jika select all dicentang
            $("#deleteSelectedButton").toggle(checked);
            // Tandai semua checkbox lainnya sesuai status "select all"
            $('.rowCheckbox').prop('checked', checked);
        });
        // Perubahan pada checkbox per baris
        $(document).on('change', '.rowCheckbox', function() {
            // Jika tidak ada checkbox yang dicentang, hapus status "select all"
            if ($('.rowCheckbox:checked').length === 0) {
                $('#selectAll').prop('checked', false); // Uncheck "select all"
                $('#deleteSelectedButton').hide(); // Sembunyikan tombol delete
            } else {
                // Jika ada checkbox yang dicentang, update status "select all"
                var totalRows = $('.rowCheckbox').length;
                var totalChecked = $('.rowCheckbox:checked').length;
                $('#selectAll').prop('checked', totalRows === totalChecked); // Cek apakah semua baris dicentang
    
                // Menampilkan tombol delete jika ada checkbox yang dicentang
                $('#deleteSelectedButton').show();
            }
        });
    
    
        // Fungsi untuk menghapus data terpilih
        $('#deleteSelectedButton').on('click', function() {
    
            var selectedIds = [];
            $('.rowCheckbox:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });
            if (selectedIds.length === 0) {
            Swal.fire({
                    title: 'Tidak ada data yang dipilih!',
                    text: 'Pilih data yang ingin dihapus terlebih dahulu.',
                    icon: 'warning',  // Ikon peringatan
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',  // Warna tombol "OK"
                });
                return;
            }
    
    
            
            Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data yang dipilih akan dihapus secara permanen!',
            icon: 'warning',  // Tipe ikon (warning untuk peringatan)
            showCancelButton: true,  // Menampilkan tombol batal
            confirmButtonText: 'Ya, hapus!',  // Teks tombol konfirmasi
            cancelButtonText: 'Batal',  // Teks tombol batal
            confirmButtonColor: '#d33',  // Warna tombol konfirmasi (merah)
            cancelButtonColor: '#3085d6',  // Warna tombol batal (biru)
            reverseButtons: true  // Membalik urutan tombol
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/delete-alternatif',
                    data: {
                        ids_alternatif: selectedIds,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        loadAlternatives(); // Reload data setelah penghapusan

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',  // Tipe ikon (success, error, warning, info, question)
                                title: 'Berhasil Dihapus',
                                text: 'Data yang Anda pilih berhasil dihapus.',
                                confirmButtonText: 'Tutup',  // Tombol untuk menutup modal
                                confirmButtonColor: '#4CAF50',  // Warna tombol
                                background: '#fff',  // Latar belakang modal
                                customClass: {
                                    title: 'modal-title',  // Kelas CSS untuk judul
                                    content: 'modal-content'  // Kelas CSS untuk konten
                                }
                            });
                            setTimeout(() => {
                                $('#selectAll').prop('checked', false); // Uncheck "select all"
                                $('.rowCheckbox').prop('checked', false);

                            }, 1000);
                        } else {
                            alert('Terjadi kesalahan saat menghapus data.');
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                });
            } else {
                // Jika pengguna memilih "Batal", tidak ada yang terjadi
                Swal.fire('Dibatalkan', 'Data tidak dihapus', 'info');
            }
        });
    
               
        });


        $(document).ready(function() {
            $('#importButton').on('click', function() {
    // Trigger SweetAlert with a file input and a button to download template
    Swal.fire({
        title: 'Import File',
        text: 'Please choose a file to import or download the template to import alternatives.',
        icon: 'info',
        input: 'file',  // This creates a file input
        inputAttributes: {
            accept: '.csv, .xlsx, .xls',  // Optional: specify allowed file types
            'aria-label': 'Upload your file'
        },
        showCancelButton: true,
        confirmButtonText: 'Import',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        showDenyButton: true, // Add deny button for downloading template
        denyButtonText: 'Download Template', // Text for the deny button (to download template)
        preConfirm: (file) => {
            if (file) {
                let formData = new FormData();
                formData.append('excel_file', file);

                return $.ajax({
                    url: '/import-data', // URL of the route handling the upload
                    type: 'POST',
                    data: formData,
                    contentType: false,  // Don't set content type, jQuery will do that automatically
                    processData: false,  // Don't process data, let FormData handle it
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF Token
                    },
                    success: function(response) {
                        Swal.fire('Success!', 'File imported successfully!', 'success');
                    },
                    error: function(xhr, status, error) {
                        // Log detailed error information in the console
                        console.log('XHR:', xhr);  // Logs the entire XHR object
                        console.log('Status:', status);  // Logs the status of the request
                        console.log('Error:', error);  // Logs the error message

                        // Check if the error response contains more information
                        if (xhr.responseJSON) {
                            console.log('Error Response:', xhr.responseJSON);  // Logs the error response from server
                        }

                        // Show a validation message with the error
                        Swal.showValidationMessage('Failed to upload file: ' + error);
                    }
                });
            } else {
                Swal.showValidationMessage('You must select a file');
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        // Check if user clicked the deny button (to download the template)
        if (result.isDenied) {
            const url = window.location.pathname;

            // Pisahkan berdasarkan '/'
            const segments = url.split('/');

            // Ambil parameter terakhir (id)
            const id = segments[segments.length - 1]
            // Trigger the download of the template
            window.location.href = '/download-template-alternatif/' +  id; // Replace this with the correct route for your template download
        }
    });
});
});

function showRincianBobot(id) {
        // Mengambil rincian bobot dari server menggunakan AJAX
        $.ajax({
            url: '/getRincianBobot/' + id,  // Endpoint di controller untuk mengambil rincian bobot
            method: 'GET',
            success: function(response) {
                // Menyiapkan isi tabel dengan rincian bobot
                var tableContent = `
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
    
                var totalBobot = response.total_bobot;
    
                // Menambahkan baris ke dalam tabel berdasarkan rincian bobot
                response.rincian_bobot.forEach(function(item, index) {
                    tableContent += `
                        <tr>
                            <td>${index + 1}</td>
                            <td style="text-align: left;">${item.kriteria}</td>
                            <td>${item.bobot}</td>
                            <td>${(item.bobot * 100).toFixed(0)}%</td>
                        </tr>
                    `;
                });
    
                // Menambahkan baris total bobot
                tableContent += `
                                <tr>
                                    <td colspan="2"><strong>Total Bobot:</strong></td>
                                    <td><strong>${totalBobot.toFixed(2)}</strong></td>
                                    <td><strong>${(totalBobot * 100).toFixed(2)}%</strong></td>
                                </tr>
    
                `;
    
                tableContent += `</tbody></table>`;
    
                // Menampilkan SweetAlert dengan rincian bobot dan total bobot
                Swal.fire({
                    title: 'Rincian Bobot Kriteria',
                    html: tableContent,
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    width: '80%',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Tidak dapat mengambil rincian bobot.',
                });
            }
        });
    }

</script>

@endpush

@include('page.saw.modal_add_kriteria')
@include('page.saw.modal_add_alternatif')
@include('page.saw.delete_kritera')
@include('page.saw.show_detail')
@include('page.saw.modal_edit_kriteria')
@include('page.saw.modal_edit_alternatif')
