@extends('admin.layout')

@section('content')
<h1>Dashboard</h1>

<h2 class="mt-5">Data Alternatif</h2>

<!-- Tombol Tambah, Import, Export -->
<div class="mb-3">
    <button class="btn btn-success" id="addKeputusanButton"><i class="fas fa-plus"></i> Tambah Keputusan</button>
    <button class="btn btn-primary"><i class="fas fa-file-import"></i> Import</button>
    <button class="btn btn-info"><i class="fas fa-file-export"></i> Export</button>
</div>

<!-- Card untuk Tabel Data Alternatif -->
<h2 class="mt-5">Tabel Keputusan</h2>   
<div class="card">
    {{-- <div class="card-header">
        <h5>Keputusan</h5>
    </div> --}}
    <div class="card-body">
    <table class="table table-striped" id="keputusanTable">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox untuk select all -->
                <th>No</th>
                <th>Judul</th>
                <th>Tanggal Pembuatan</th>
                <th>Deskripsi</th>
                <th>Tujuan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan di-load lewat AJAX -->
        </tbody>
    </table>
    

    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="addKeputusanModal" tabindex="-1" aria-labelledby="addKriteriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKriteriaModalLabel">Tambah Kriteria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addKriteriaForm">
                    <div class="form-group">
                        <label for="judul">Judul</label>
                        <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan nama kriteria" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" cols="30" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tujuan">Tujuan</label>
                        <input type="text" class="form-control" id="tujuan" name="tujuan" placeholder="Masukkan tujuan" required>
                    </div>
                </form>               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveKriteriaButton">Simpan Kriteria</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    window.table = $('#keputusanTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
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
        "scrollX": true,
        "columnDefs": [
            {
                "targets": 0, // Kolom pertama (No)
                "className": "text-left"
            }
        ],
        "createdRow": function(row, data, dataIndex) {
            // Menambahkan checkbox di kolom pertama
            $('td:eq(0)', row).html(`
                <input type="checkbox" class="rowCheckbox" data-id="${data.id_pengambilan_keputusan}">
            `);

            // Kolom pertama adalah nomor urut yang akan diupdate setelah sorting
            $('td:eq(1)', row).html(dataIndex + 1); // Kolom pertama adalah nomor urut (index + 1)
        }
    });

    // Fungsi untuk memuat data
    function loadData() {
        $.ajax({
            type: 'GET',
            url: '/simple-additive-weighting', // Gantilah dengan URL endpoint yang sesuai
            success: function(data) {
                if(data != "empty") {
                    if (Array.isArray(data) && data.length > 0) {
                        // Clear the existing rows without destroying the DataTable
                        table.clear();
    
                        data.forEach(function(d, index) {
                            table.row.add([
                                '',
                                '', // Kosongkan kolom nomor urut di sini
                                d.title, 
                                d.date, 
                                d.description, 
                                d.tujuan,
                                `<button onclick="window.location.href='/simple-additive-weighting/${d.id_pengambilan_keputusan}'" class="btn btn-secondary btn-sm">
                                    Detail
                                </button>`
                            ]);
                        });
    
                        table.draw();
                    } else {
                        table.clear();
                        table.row.add([ '', '', '', '', '', 'Tidak ada data yang tersedia' ]);
                        table.draw();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading data: ", error);
                alert('Terjadi kesalahan saat memuat data.');
            }
        });
    }

    // Memuat data saat halaman dimuat
    loadData();

    // Memperbarui nomor urut setiap kali DataTable diurutkan atau disaring
    table.on('order.dt search.dt', function() {
        table.column(1, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1; // Menetapkan nomor urut berdasarkan urutan yang baru
        });
    }).draw(); 

    // Menambahkan fungsionalitas Select All checkbox
    $('#selectAll').on('change', function() {
        var checked = $(this).prop('checked');
        $('.rowCheckbox').prop('checked', checked);
    });

    // Fungsi untuk menghapus data yang terpilih
    $('#deleteSelectedButton').on('click', function() {
        var selectedIds = [];
        $('.rowCheckbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });

        if (selectedIds.length === 0) {
            alert('Pilih data yang ingin dihapus terlebih dahulu.');
            return;
        }

        if (confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')) {
            $.ajax({
                type: 'POST',
                url: '/keputusan/hapusMasal', // Endpoint untuk menghapus data masal
                data: {
                    ids: selectedIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        loadData(); // Reload data setelah penghapusan
                    } else {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Terjadi kesalahan saat menghapus data.');
                }
            });
        }
    });

    // CSRF setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#addKeputusanButton').on('click', function() {
        $('#addKeputusanModal').modal('show');
    });

    $('#saveKriteriaButton').on('click', function(e) {
        e.preventDefault(); // Prevent the default button action

        // Serialize the form data
        var formData = $('#addKriteriaForm').serialize();

        $.ajax({
            type: 'POST',
            url: '{{ route("saw.store") }}', // Make sure this route is correctly defined
            data: formData,
            success: function(response) {
                if (response.success) {
                    loadData(); // Reload or update your data display

                    // Close the modal and reset the form
                    $('#addKeputusanModal').modal('hide');
                    $('#addKriteriaForm')[0].reset();
                } else {
                    alert('Terjadi kesalahan saat menyimpan kriteria.');
                }
            },
            error: function(xhr) {
                // Handle errors
                console.error(xhr);
                alert('Terjadi kesalahan saat menambahkan kriteria.');
            }
        });
    });
});
</script>
@endpush
