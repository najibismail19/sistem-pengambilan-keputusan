<div class="modal fade" id="editAlternatifModal" tabindex="-1" aria-labelledby="editAlternatifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">  <!-- Menambahkan modal-lg untuk memperbesar lebar modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Alternatif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">  <!-- Menambahkan max-height dan scroll jika diperlukan -->
                <input type="hidden" id="id_keputusan_alternatif_edit" value="{{ $id }}">
                <form id="editAlternatifForm">
                    <div class="form-group">
                        <input type="hidden" id="edit_id_alternatif">
                        <label for="editNamaAlternatif">Nama Alternatif</label>
                        <input type="text" class="form-control" id="editNamaAlternatif" name="nama_alternatif" placeholder="Masukkan nama alternatif" required>
                    </div>
                    <!-- Kriteria dinamis berdasarkan jumlah kriteria yang ada -->
                    <div id="kriteriaEditContainer"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times-circle"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="saveEditAlternatif">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')

<script>
    // Fungsi untuk membuka modal edit dan mengisi data
$(document).on('click', '#editAlternatifButton', function() {
    const alternatifId = $(this).data('id_alternatif');  // Ambil ID alternatif yang ingin diedit
    $.ajax({
        url: '/simple-additive-weighting/' + $("#id_keputusan_alternatif_edit").val() + "/alternatif/" + alternatifId, // Ganti dengan route yang sesuai
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF Token untuk Laravel
        },
        success: function(response) {
            console.log(response);

            // Pastikan response dan data valid
            if (!response || !response.data || response.data.length === 0) {
                console.log('Tidak ada data yang ditemukan.');
                return;
            }

            // Kosongkan kriteriaContainer sebelum menambahkan input baru
            $('#kriteriaEditContainer').empty();

            // Loop melalui data kriteria dan nilai yang diterima dari server
            $.each(response.data, function(index, item) {


                // Handle the case when 'pilihan' is empty (text input field)
                if (item.pilihan.length === 0) {
                    const inputHtml = `
                        <div class="form-group">
                            <label for="nilai_${item.nama_kriteria}">${item.nama_kriteria}</label>
                            <input type="text" class="form-control" id="nilai_${item.nama_kriteria}" name="nilai_${item.nama_kriteria}" value="${item.nilai_kriteria}" required>
                            <input type="hidden" name="idk_${item.id_kriteria}" value="${item.id_kriteria}">
                        </div>
                    `;
                    $('#kriteriaEditContainer').append(inputHtml);
                }

                // Handle the case when 'pilihan' has values (checkbox options)
                if (item.pilihan.length > 0) {
                    let pilihanHtml = `
                        <div class="form-group">
                            <label>Pilihan Kriteria (${item.nama_kriteria}):</label>
                    `;
                    
                    // Loop pilihan untuk checkbox
                    $.each(item.pilihan, function(i, pilihan) {
                        // Memastikan nilai_kriteria adalah array
                        let nilaiKriteria = Array.isArray(item.nilai_kriteria) ? item.nilai_kriteria : [item.nilai_kriteria];

                        // Memeriksa apakah nilai pilihan sudah ada dalam item.nilai_kriteria
                        const isChecked = nilaiKriteria.includes(pilihan.value) ? 'checked' : '';

                        pilihanHtml += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="${pilihan.value}" id="pilihan_${pilihan.id}" name="pilihan_${item.name}[]" ${isChecked}>
                                <input type="hidden" name="idk_${item.id_kriteria}" value="${item.id_kriteria}">
                                <label class="form-check-label" for="pilihan_${pilihan.id}">
                                    ${pilihan.name}
                                </label>
                            </div>
                        `;
                    });


                    pilihanHtml += '</div>';
                    $('#kriteriaEditContainer').append(pilihanHtml);
                }

                console.log(item.id_kriteria);
            });

            // Set the alternative's ID and name
            $("#edit_id_alternatif").val(response.data[0].id_alternatif);
            $("#editNamaAlternatif").val(response.nama_alternatif);

            // Buka modal setelah data ditambahkan
            $('#editAlternatifModal').modal('show');
        },
        error: function() {
            alert('Terjadi kesalahan, coba lagi!');
        }
    });
});

// Kirim data ke server dengan AJAX (gunakan method PUT untuk update)
$(document).on('click', '#saveEditAlternatif', function() {
    var formData = {
        id_alternatif: $('#edit_id_alternatif').val(),
        nama_alternatif: $('#editNamaAlternatif').val(),
        kriteria: [] // Kriteria yang akan diambil dari form
    };

    // Ambil data kriteria dari form (sesuaikan dengan ID dan struktur input)
    $('#kriteriaEditContainer .form-group').each(function() {
        var idKriteria = $(this).find('input[name^="idk_"]').attr('name').split('_')[1];
        var nilaiKriteria = [];

        // Periksa apakah ada checkbox yang terpilih
        $(this).find('input[name^="pilihan_"]:checked').each(function() {
            nilaiKriteria.push($(this).val());
        });

        // Jika inputan tipe teks (misalnya untuk kriteria tanpa pilihan)
        if (nilaiKriteria.length === 0) {
            nilaiKriteria.push($(this).find('input[name^="nilai_"]').val());
        }

        formData.kriteria.push({
            id_kriteria: idKriteria,
            nilai_kriteria: nilaiKriteria  // Pastikan ini adalah array
        });
    });
    const alternatifId = $(this).data('id_alternatif');  // Ambil ID alternatif yang ingin diedit


    // Kirim data menggunakan AJAX
    $.ajax({
        url: '/simple-additive-weighting/' + $("#id_keputusan_alternatif_edit").val() + "/alternatif/" + alternatifId,  // Sesuaikan dengan URL untuk update alternatif
        method: 'PUT',  // Metode PUT untuk update data
        data: JSON.stringify(formData),  // Mengirim data dalam format JSON
        contentType: 'application/json',  // Menyatakan bahwa kita mengirimkan data JSON
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF Token untuk Laravel
        },
        success: function(response) {
          // Memanggil fungsi loadAlternatives() untuk memuat ulang alternatif jika diperlukan

            // Menutup modal setelah data berhasil disimpan
            $('#editAlternatifModal').modal('hide');

            // Menampilkan SweetAlert setelah berhasil update data
            Swal.fire({
                title: 'Sukses!',
                text: 'Alternatif berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                // Jika SweetAlert ditutup (setelah notifikasi sukses)
                // Refresh halaman
                location.reload();
            });
        },
        error: function(xhr, status, error) {
            // Jika gagal, tampilkan pesan error dari server
            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan, coba lagi!';
            alert(errorMsg);
        }
    });
});


</script>
@endpush
