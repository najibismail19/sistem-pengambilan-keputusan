<script>
     function editKriteria(id, kriteria, nama, bobot, id_keputusan_kriteria) {
        // Menampilkan SweetAlert dengan form untuk mengedit
        Swal.fire({
            title: 'Edit Kriteria',
            html: `
                <input type="text" id="kriteria_title" class="swal2-input" placeholder="Kriteria (Title)" value="${kriteria}">
                <input type="text" id="editNama" class="swal2-input" placeholder="Nama Kriteria" value="${nama}">
                <div class="input-container">
                    <input type="number" id="editBobot" class="swal2-input" placeholder="Bobot Kriteria" min="0" max="100" value="${(bobot * 100)}">
                </div>
    
                <select id="editJenis" class="swal2-input">
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            `,
            confirmButtonText: 'Simpan',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const kriteria = document.getElementById('kriteria_title').value;
                const nama = document.getElementById('editNama').value;
                const bobot = document.getElementById('editBobot').value;
                const jenis = document.getElementById('editJenis').value;
    
                // Validasi input
                if (!nama || !bobot || !jenis) {
                    Swal.showValidationMessage('Semua field harus diisi!');
                    return false; // Mencegah submit jika ada field yang kosong
                }
    
                // Mengembalikan data yang akan dikirim
                return { kriteria, nama, bobot, jenis };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                $.ajax({
                    type: 'POST',
                    url: '/simple-additive-weighting/' + id_keputusan_kriteria + '/kriteria/' + id, 
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        kriteria: data.kriteria,
                        nama: data.nama,
                        bobot: data.bobot,
                        jenis: data.jenis
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: 'Kriteria berhasil diperbarui.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengedit data.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            } else {
                // Jika dibatalkan, Anda bisa memberikan pemberitahuan atau aksi lain
                console.log("Edit dibatalkan");
            }
        });
    
    }
</script>