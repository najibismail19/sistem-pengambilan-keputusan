<script>
        function confirmDelete(id_keputusan_kriteria, id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Kriteria ini akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Lakukan penghapusan jika pengguna mengonfirmasi
                $.ajax({
                    url: '/simple-additive-weighting/' + id_keputusan_kriteria + '/kriteria/' + id,  // Sesuaikan URL dengan rute penghapusan yang sesuai
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')  // Pastikan CSRF token dikirim
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Dihapus!',
                                text: 'Kriteria telah berhasil dihapus.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload halaman setelah pengguna menekan tombol OK
                                    location.reload();
                                }
                            });
    
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus kriteria.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus kriteria.', 'error');
                    }
                });
            } else {
                Swal.fire('Dibatalkan', 'Penghapusan dibatalkan.', 'info');
            }
        });
    }
    
</script>