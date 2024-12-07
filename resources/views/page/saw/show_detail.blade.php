
<!-- Modal for displaying Rumus Details -->
<div class="modal fade" id="rumusModal" tabindex="-1" role="dialog" aria-labelledby="rumusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rumusModalLabel">Detail Rumus Perhitungan untuk Alternatif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Jenis</th>
                            <th>Max/Min</th>
                            <th>Nilai</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-center">Skor Bobot</th>
                            <th class="text-center">Skor Bobot Akhir = Bobot * Skor Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be appended dynamically via JS -->
                    </tbody>
                </table>
                <hr>
                <!-- The total score will be added dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function loadRumusData(id_keputusan_alternatif, id_alternatif) {
    
    $.ajax({
        url: '/simple-additive-weighting/' + id_keputusan_alternatif + '/detail-alternatif/' + id_alternatif,
        method: 'GET',
        success: function(response) {
            console.log(response);
    
            if (response.error) {
                alert(response.error);
                return;
            }
    
            // Update modal dengan data dari server
            var modal = $('#rumusModal');
            modal.find('.modal-title').text('Rincian Nilai Alternatif ' + response.alternatif);
    
            var rumusTableBody = modal.find('.modal-body tbody');
            rumusTableBody.empty();  // Bersihkan daftar rumus sebelumnya

            response.rumusData.forEach(function(rumus) {

                let hasilPerhitungan = (rumus.type === "cost") 
                        ? (((rumus.std_rumus == 0) ? 1 : rumus.std_rumus) / rumus.nilai).toFixed(2) 
                        : (rumus.nilai / rumus.std_rumus).toFixed(2);
                                    rumusTableBody.append(`
                                        <tr>
                                        <td>${rumus.kriteria}</td>
                    <td>${rumus.type}</td>
                    <td class="text-center">${(rumus.std_rumus == 0) ? 1 : rumus.std_rumus}</td>
                    <td class="text-center">${rumus.nilai}</td>
                    <td class="text-center">${rumus.bobot.toFixed(2)}</td>
                    <td class="text-center">
                        ${hasilPerhitungan}
                    </td>
                    <td class="text-center">${(rumus.bobot * hasilPerhitungan).toFixed(2)}</td>

                    </tr>
                `);
            });
            modal.find('.modal-body tfoot').empty();
            modal.find('.modal-body table').append(`
            <tfoot> 
                <tr>
                    <td colspan="6"><strong>Total Skor:</strong></td>
                    <td class="text-center">${response.totalScore.toFixed(2)}</td>
                </tr>
            </tfoot>
            `);
    
            $("#rumusModal").modal("show");
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan saat memuat data rumus.');
        }
    });
    }
</script>