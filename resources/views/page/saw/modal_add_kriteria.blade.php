<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="addKriteriaModal" tabindex="-1" aria-labelledby="addKriteriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kriteria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="kriteriaForm">
                    <div class="form-group">
                        <label for="kriteriaNama">
                            <i class="fa fa-tag"></i> Kriteria
                        </label>
                        <input type="text" class="form-control" id="kriteria" placeholder="Masukkan nama kriteria" required>
                    </div>
                    <div class="form-group">
                        <label for="kriteriaDeskripsi">
                            <i class="fa fa-pencil-alt"></i> Nama
                        </label>
                        <input type="text" class="form-control" id="kriteriaNama" placeholder="Masukkan deskripsi kriteria" required>
                    </div>
                    <div class="form-group" id="">
                        <label>
                            <i class="fa fa-weight-hanging"></i> Pilih Tipe Bobot:
                        </label><br>
                        <input type="radio" id="nilai" name="tipeBobot" value="nilai" onclick="toggleInput()" required>
                        <label for="nilai">Nilai</label><br>
                        <input type="radio" id="pilihan" name="tipeBobot" value="pilihan" onclick="toggleInput()">
                        <label for="pilihan">Pilihan</label>
                    </div>
                
                    <div>
                        <label for="nilaiAlternatif">
                            <i class="fa fa-calculator"></i> Masukkan Nilai Bobot:
                        </label>
                        <input type="number" class="form-control" id="nilaiAlternatif" placeholder="Masukkan nilai alternatif" min="1" max="100" required>
                    </div>
                <br>
                    <div id="pilihanInput" style="display:none;">
                        <div id="qualityContainer">
                            <label for="tambahOption">
                                <i class="fa fa-plus-circle"></i> Buat Pilihan:
                            </label>
                            <input type="text" class="form-control d-inline-block w-50" id="tambahOption" placeholder="Masukkan nilai" required>
                            <div class="mt-2">
                                <button type="button" class="btn btn-primary mt-2" onclick="addQuality()">Tambah Kualitas</button>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="kriteriaJenis">
                            <i class="fa fa-cogs"></i> Jenis
                        </label>
                        <select class="form-control" id="kriteriaJenis" required>
                            <option value="" disabled selected>Pilih jenis</option>
                            <option value="benefit">Benefit</option>
                            <option value="cost">Cost</option>
                        </select>
                    </div>
                    <input type="hidden" name="id" id="id_hidden" value="{{ $id }}">
                </form>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveKriteria">Simpan Kriteria</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Konfirmasi -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="successModalLabel">
            <i class="fas fa-check-circle"></i> Sukses
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-center align-items-center">
            <!-- Ikon Sukses -->
            <div class="mr-3">
              <i class="fas fa-thumbs-up fa-3x text-success"></i>
            </div>
            <div>
              Data berhasil disimpan!
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">
            <i class="fas fa-check"></i> OK
          </button>
        </div>
      </div>
    </div>
  </div>
  
  @push('scripts')

<script>
     document.getElementById('addKriteriaButton').addEventListener('click', function() {
        $('#addKriteriaModal').modal('show');
    });

       
    
 

    function toggleInput() {
    // Ambil input yang dipilih berdasarkan nama "tipeBobot"
    const pilihan = document.querySelector('input[name="tipeBobot"]:checked');
    
    // Pastikan pilihan ada sebelum melanjutkan
    if (pilihan) {
        // Ambil elemen dengan ID 'nilaiInput' dan 'pilihanInput'
        // const nilaiInput = document.getElementById('nilaiAlternatif');
        const pilihanInput = document.getElementById('pilihanInput');
        
        // Periksa nilai dari input yang dipilih
        if (pilihan.value === 'nilai') {
            // Jika memilih 'nilai', tampilkan nilaiInput dan sembunyikan pilihanInput
            // nilaiInput.style.display = 'block';
            pilihanInput.style.display = 'none';
        } else {
            // Jika memilih selain 'nilai', tampilkan pilihanInput dan sembunyikan nilaiInput
            // nilaiInput.style.display = 'none';
            pilihanInput.style.display = 'block';
        }
    }
}

// Menambahkan listener untuk menutup modal dan me-refresh halaman
$('#successModal').on('hidden.bs.modal', function () {
    location.reload();  // Refresh halaman setelah modal ditutup
});




    let number = 1; // Declare number as a global variable

    function addQuality() { 
        const qualityContainer = document.getElementById('qualityContainer');
        const isi = document.getElementById('tambahOption');

        // Create a new list item
        const newQualityItem = document.createElement('div');
        newQualityItem.className = 'list-item mt-2'; // Optional: add a class for styling

        newQualityItem.innerHTML = `
            <span>${number}. ${isi.value}</span>
            <button type="button" class="btn btn-danger btn-sm ms-2" onclick="removeQuality(this)">Hapus</button>
        `;

        qualityContainer.appendChild(newQualityItem);
        number++; // Increment the global number variable
        isi.value = ''; // Clear the input after adding
    }

    function removeQuality(button) {
        button.parentElement.remove();
        number--; // Decrement the global number variable
        updateQualityNumbers(); // Update remaining numbers
    }

    function updateQualityNumbers() {
        const qualityItems = document.querySelectorAll('.list-item');
        qualityItems.forEach((item, index) => {
            item.querySelector('span').textContent = `${index + 1}. ${item.querySelector('span').textContent.split('. ')[1]}`;
        });
    }

      $('input[name="tipeBobot"]').change(function() {
        if ($(this).val() === 'nilai') {
            $('#nilaiInput').show(); // Show input for value
            $('#pilihanInput').hide(); // Hide input for choices
        } else if ($(this).val() === 'pilihan') {
            $('#pilihanInput').show(); // Show input for choices
            $('#nilaiInput').hide(); // Hide input for value
        }
    });


    function collectSpanValues() {
    let values = [];
    $('#qualityContainer span').each(function() {
        values.push($(this).text()); // Collecting text from each span
    });
    return values.length > 0 ? values : null; // Return values or null if none
}






$('#saveKriteria').click(function() {
    let pilihan = ""; // Declare pilihan variable

    // Check which radio button is selected
    if ($('input[name="tipeBobot"]:checked').val() === "pilihan") {
        pilihan = collectSpanValues(); // Call collectSpanValues() to get array of span values
    } 
    let formData = {
    kriteria: $('#kriteria').val(),
    kriteriaNama: $('#kriteriaNama').val(),
    pilihan: pilihan,  // pastikan ini berisi data yang benar
    kriteriaJenis: $('#kriteriaJenis').val(),
    bobot: $("#nilaiAlternatif").val()
};

id = $("#id_hidden").val();

$.ajax({
    url: "{{ route('kriteria.store', ['id' => $id]) }}",  // URL untuk mengirimkan data
    type: 'POST',  // Metode HTTP POST
    data: formData,  // Data yang akan dikirim (misalnya data formulir)
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF Token untuk Laravel
    },
    success: function(response) {
 // Periksa status dari response
 if (response.status === "error") {
            // Menampilkan SweetAlert dengan pesan error
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: response.message, // Pesan error
            });
        } else {
            // Menampilkan SweetAlert dengan pesan sukses
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Data berhasil disimpan.', // Pesan sukses
            }).then((result) => {
                // Jika SweetAlert ditutup (setelah notifikasi sukses)
                // Refresh halaman
                location.reload();
            });

            console.log(response);  // Menampilkan response jika berhasil

            // Menyembunyikan modal setelah data berhasil disimpan
            $('#addKriteriaModal').modal('hide');
        }

    },
    error: function(xhr) {
        console.error(xhr.responseText);  // Log error jika terjadi kesalahan
    }
});

});
</script>
@endpush
