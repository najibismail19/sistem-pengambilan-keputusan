

<div class="modal fade" id="addAlternatifModal" tabindex="-1" aria-labelledby="addAlternatifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Menambahkan modal-lg untuk memperbesar modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Alternatif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;"> <!-- Menambahkan max-height dan scroll jika diperlukan -->
                <form id="alternatifForm" method="POST">
                    <div class="form-group">
                        <label for="namaAlternatif">Nama Alternatif</label>
                        <input type="text" class="form-control" id="namaAlternatif1" name="nama_alternatif" placeholder="Masukkan nama alternatif" required>
                    </div>
                    <input type="hidden" id="id_keputusan_alternatif_add" value="{{ $id }}">
                    @foreach ($data as $d)
                        <div class="form-group">
                            <label for="kriteria_{{ $d->id }}">{{ $d->nama }}</label>
                            <?php 
                            $pilihan = DB::select("SELECT * FROM pilihan_kriteria WHERE id_kriteria = ?", [$d->id]);
                            ?>
                            
                            @if ($pilihan && count($pilihan) > 0)
                                @foreach ($pilihan as $p)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="nilai_pilihan" name="{{$d->id}}" value="{{ $p->value }}">
                                        <label class="form-check-label" for="pilihan_{{ $p->name }}">
                                            {{ $p->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <input type="text" class="form-control" id="nilai_inputan" name="{{ $d->id }}" placeholder="Masukkan nilai untuk {{ $d->nama }}" required>
                            @endif
                            
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times-circle"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="saveAlternatif">
                    <i class="fas fa-save"></i> Simpan Alternatif
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
     document.getElementById('addAlternatifButton').addEventListener('click', function() {
        $('#addAlternatifModal').modal('show');
    });

    

    $("#saveAlternatif").click(function () {
        // Ambil data checkbox yang tercentang
        var pilihan = $("input[id^='nilai_pilihan']:checked").map(function () {
            return {
                name: $(this).attr('name'),  // Ambil name dari checkbox
                value: $(this).val()         // Ambil value dari checkbox yang tercentang
            };
        }).get();

        // Ambil semua data input teks
        var dataInputan = $("input[id^='nilai_inputan']").map(function () {
            return {
                name: $(this).attr('name'),  // Ambil name dari input teks
                value: $(this).val()         // Ambil value dari input teks
            };
        }).get();

        // Gabungkan pilihan dan inputan teks menjadi satu objek
        var data = {};

        // Gabungkan data pilihan (checkbox tercentang)
        if (pilihan.length > 0) {
            data[pilihan[0].name] = pilihan[0].value;
        }

        // Gabungkan data inputan teks
        dataInputan.forEach(function (input) {
            data[input.name] = input.value;
        });

        // Validasi inputan: Pastikan tidak ada input yang kosong
        for (var key in data) {
            if (data[key] === '') {
                alert('Field ' + key + ' tidak boleh kosong.');
                return false;  // Menghentikan proses jika ada input yang kosong
            }
        }

        let  iw =  {
        input_data: data,  // Pastikan 'data' sudah terisi dengan nilai yang benar
        nama_alternatif: $("#namaAlternatif1").val()  // Ambil nilai dari input nama alternatif
    };
    console.log(iw);
    

        // Kirim data via AJAX
        $.ajax({
    url: "/simple-additive-weighting/" + $("#id_keputusan_alternatif_add").val() + "/alternatif", // Ganti dengan route yang sesuai di Laravel
    method: 'POST',
    data: {
        input_data: data,  // Pastikan 'data' sudah terisi dengan nilai yang benar
        nama_alternatif: $("#namaAlternatif1").val()  // Ambil nilai dari input nama alternatif
    },
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF token untuk Laravel
    },
    success: function(response) {
        console.log(response);  // Cek response dari server
        $('#addAlternatifModal').modal('hide');


        // Menampilkan alert sukses jika response status menunjukkan keberhasilan
        if (response.status === 'success') {  // Misalnya, response.status dari server
            showAlert('success', 'Alternatif berhasil disimpan!');
                loadAlternatives();  // Load alternatives on page load

        } else {
            showAlert('danger', 'Gagal menyimpan alternatif. Coba lagi.');
        }

        // Reload DataTable agar data terbaru ditampilkanX`X`

    },
    error: function(xhr, status, error) {
        console.log(error);  // Cek error dari server atau jaringan

        // Tampilkan alert kesalahan
        showAlert('danger', 'Terjadi kesalahan saat menyimpan alternatif. Coba lagi.');
    }
});

    });
    // Function untuk menampilkan alert
function showAlert(type, message) {
    Swal.fire({
        icon: type,  // Tipe ikon (success, error, warning, info, question)
        title: message,
        confirmButtonText: 'Tutup',  // Tombol untuk menutup modal
        confirmButtonColor: '#4CAF50',  // Warna tombol
        background: '#fff',  // Latar belakang modal
        customClass: {
            title: 'modal-title',  // Kelas CSS untuk judul
            content: 'modal-content'  // Kelas CSS untuk konten
        }
    });
}

</script>
@endpush
