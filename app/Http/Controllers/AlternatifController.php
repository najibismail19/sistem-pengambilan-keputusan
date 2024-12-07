<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Database\QueryException;
use PhpOffice\PhpSpreadsheet\IOFactory;



class AlternatifController extends Controller
{
    public function detail(Request $request, $id_pengambilan_keputusan, $id_alternatif)
{
    // Ambil data alternatif berdasarkan ID
    $alternatif = DB::table('alternatif')->where('id_alternatif', $id_alternatif)->first();
    if (!$alternatif) {
        return response()->json(['error' => 'Alternatif not found'], 404);
    }


    // Ambil data kriteria
    $dataKriteria = DB::table('kriteria')->where('id_pengambilan_keputusan', $id_pengambilan_keputusan)->get();
    
    $totalScore = 0;
    $rumusData = [];

    $scoreakhir = 0;
    foreach ($dataKriteria as $d) {
        $nilai = DB::table('nilai')->where('id_alternatif', $alternatif->id_alternatif)->where('id_kriteria', $d->id)->first();
        $nilaikValue = $nilai->nilai ?? 0;

        if ($d->jenis == "benefit") {   
            $type = "benefit";
            $maxValue = DB::table('nilai')->where('id_kriteria', $d->id)->max('nilai');
            $rumus = "$nilaikValue / $maxValue";
            $score = $nilaikValue != 0 ? ($nilaikValue / $maxValue) : 0;
            $scoreakhir += ($score *$d->bobot);
            $totalScore += $score; 
        } elseif ($d->jenis == "cost") {
            $type = "cost";
            $minValue = DB::table('nilai')->where('id_kriteria', $d->id)->min('nilai');
            $rumus = "$minValue / $nilaikValue";
            $minValue = ($minValue == 0) ? 1 : $minValue;
            $score = $nilaikValue != 0 ? ($minValue / $nilaikValue): 0;
            $totalScore += $score;
            $scoreakhir += ($score * $d->bobot);
        }

        $rumusData[] = [
            'kriteria' => $d->nama,
            'bobot' => $d->bobot,
            'nilai' => $nilaikValue,
            'type' => $type,   
            'std_rumus' => ($type == "benefit") ?  $maxValue : (($minValue = 0) ? 1 : $minValue),
            'rumus' => $rumus,
            'skor' => number_format($score, 2)
        ];
    }

    return response()->json([
        'alternatif' => $alternatif->nama_alternatif,
        'rumusData' => $rumusData,
        'totalScore' => number_format($scoreakhir, 3) * 100
    ]);
}

public function store(Request $request)
    {
        // Validasi server-side
        $requestClient = $request->validate([
            'pilihan.*' => 'required|string',
            'nama_alternatif' => 'required'
        ]);

        $newAlternatifId = DB::table('alternatif')->insertGetId([
            'nama_alternatif' => $request->nama_alternatif,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        foreach ($request->input('input_data') as $id_kriteria => $nilai) {
            DB::table('nilai')->insertGetId([
                'id_alternatif' => $newAlternatifId,
                'id_kriteria' => $id_kriteria,
                'nilai' => $nilai,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json(['status' => "success"]);
    }


    public function edit($id_pengambilan_keputusan, $alternatif_id)
{
    

    $result = DB::select("SELECT k.nama AS nama_kriteria, n.nilai AS nilai_kriteria, n.id_alternatif as id_alternatif, n.id_kriteria as id_kriteria 
                        FROM nilai AS n 
                        JOIN kriteria AS k 
                        ON k.id = n.id_kriteria 
                        WHERE n.id_alternatif = ?", [$alternatif_id]);

$data = [];

foreach ($result as $r) {
    // Ambil pilihan kriteria berdasarkan id_kriteria
    $pilihan = DB::select("SELECT * FROM pilihan_kriteria WHERE id_kriteria = ?", [$r->id_kriteria]);
    

        $data[] = [
            "nama_kriteria" => $r->nama_kriteria, // Perbaiki typo nama_kriteria
            "id_alternatif" => $r->id_alternatif,
            "id_kriteria" => $r->id_kriteria,
            "nilai_kriteria" => $r->nilai_kriteria,
            "pilihan" => $pilihan ?? []
        ];
}


    if (empty($data)) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    return response()->json([
        "nama_alternatif" =>  DB::select("SELECT nama_alternatif FROM alternatif WHERE id_alternatif = ?", [$alternatif_id])[0]->nama_alternatif,
        "data" => $data

    ]);
}


public function update(Request $request)
{
    // Validasi input yang diterima
    $validated = $request->validate([
        'id_alternatif' => 'required',  // Pastikan alternatif ada
        'nama_alternatif' => 'required|string|max:255',
        'kriteria' => 'required|array',  // Array kriteria yang harus ada
        'kriteria.*.id_kriteria' => 'required', // Validasi ID Kriteria
        'kriteria.*.nilai_kriteria' => 'nullable|array',  // Nilai bisa kosong jika berupa pilihan checkbox
    ]);

    try {
        // Update nama alternatif
        $updateAlternatifQuery = "
            UPDATE alternatif
            SET nama_alternatif = :nama_alternatif
            WHERE id_alternatif = :id_alternatif        
        ";

        DB::update($updateAlternatifQuery, [
            'id_alternatif' => $validated['id_alternatif'],
            'nama_alternatif' => $validated['nama_alternatif'],
        ]);

        // Update nilai kriteria
        foreach ($validated['kriteria'] as $kr) {
            $updateKriteriaQuery = "
                UPDATE nilai
                SET nilai = :nilai
                WHERE id_kriteria = :id_kriteria AND id_alternatif = :id_alternatif
            ";
            // $nilai = is_array($kr['nilai_kriteria']) ? json_encode($kr['nilai_kriteria']) : $kr['nilai_kriteria'];

            DB::update($updateKriteriaQuery, [
                'nilai' => $kr["nilai_kriteria"][0],
                'id_kriteria' => $kr['id_kriteria'],
                'id_alternatif' => $validated['id_alternatif']
            ]);
        }

        // Mengembalikan response sukses setelah semua proses update berhasil
        return response()->json([
            'success' => true,
            'message' => 'Alternatif dan nilai kriteria berhasil diperbarui!'
        ]);

    } catch (\Exception $e) {
        // Menangani error jika terjadi kesalahan saat update data
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

public function deleteMasal(Request $request) {
    // Ambil data "ids" dari input request
    try {
        // Misalkan Anda memiliki array dengan ID yang ingin dihapus
        $idsToDelete = $request->input("ids_alternatif"); // Contoh array dengan ID yang ingin dihapus

        // Mengubah array menjadi format yang diterima oleh query (string yang dipisahkan koma)
        $ids = implode(',', $idsToDelete);

        // Menggunakan raw query untuk menghapus data
        $deletedRows = DB::delete("DELETE FROM nilai WHERE id_alternatif IN ($ids)");

        // Mengecek apakah ada baris yang dihapus
        if ($deletedRows > 0) {
            return response()->json([
                'success' => true,
                'message' => "$deletedRows row(s) deleted successfully."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "No rows were deleted."
            ]);
        }
        
    } catch (QueryException $e) {
        // Menangani error jika query gagal
        return response()->json([
            'success' => false,
            'message' => 'Error occurred: ' . $e->getMessage()
        ]);
    }    
}

public function downloadTemlpateAlternatif(Request $request, $id) {
    // Nama tabel yang ingin digunakan
   $tableName = 'kriteria'; 
   
   // Mendapatkan nama kolom dari tabel menggunakan query
   $columns = DB::select("
       SELECT 
           k.nama AS Nama_Kriteria
       FROM 
           kriteria AS k 
       WHERE 
           k.id_pengambilan_keputusan = ?
   ", [$id]);
   
   // Membuat array pemetaan kolom (untuk header Excel)
   $columnMapping = ["Nama Alternatif"];
   
   // Loop untuk memasukkan nama kriteria ke dalam pemetaan
   foreach ($columns as $column) {
       $columnMapping[] = $column->Nama_Kriteria; // Menyimpan nama kriteria dalam array
   }
   
   // Membuat spreadsheet baru
   $spreadsheet = new Spreadsheet();
   $sheet = $spreadsheet->getActiveSheet();
   
   // Menambahkan nama kolom yang dipetakan ke baris pertama (header)
   $columnIndex = 1; // Kolom pertama di Excel adalah A
   foreach ($columnMapping as $header) {
       // Menggunakan alamat sel dalam format Excel, seperti 'A1', 'B1', 'C1', dst.
       $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex); // Convert index to column letter
       $sheet->setCellValue($columnLetter . '1', $header); // Menetapkan nilai ke baris pertama
       $columnIndex++; // Increment column index
   }
   
   // Membuat writer untuk menulis ke file Excel
   $writer = new Xlsx($spreadsheet);
   
   // Menentukan nama file Excel
   $filename = 'template upload alternatif.xlsx';
   
   // Mengirimkan file ke browser untuk diunduh
   return response()->stream(
       function () use ($writer) {
           $writer->save('php://output');
       },
       200,
       [
           'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
           'Content-Disposition' => 'attachment;filename="' . $filename . '"',
           'Cache-Control' => 'max-age=0',
       ]
   );
   }


   public function importAlternatif(Request $request)
{
    // Validate file
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048',  // 2MB
    ]);

    // Handle file upload
    if ($request->hasFile('excel_file')) {
        $file = $request->file('excel_file');
        $path = $file->getRealPath();

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($path);

        // Get the active sheet (the first sheet in the file)
        $sheet = $spreadsheet->getActiveSheet();

        // Convert sheet data to array
        $data = $sheet->toArray();

        // Process the rows and insert into the database
        $this->importData($data);
        
        return back()->with('success', 'Data imported successfully!');
    }

    return back()->with('error', 'Please select a valid Excel file.');
}

// Process the data and insert it into the database
private function importData($data)
{
    // Skip the first row (header)
    $header = array_shift($data);

    // Get the count of kriteria to loop over
    $kriteriaData = DB::table('kriteria')->where('id_pengambilan_keputusan', 1)->get();
    $row_kr_count = $kriteriaData->count();  // Count of kriteria
    // Loop over each row in the data (each row corresponds to one 'alternatif' entry)
    foreach ($data as $row) {
        // The first column is 'nama_alternatif'
        $alternatifId = DB::table('alternatif')->insertGetId([
            'nama_alternatif' => $row[0], // Nama alternatif yang diambil dari Excel
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Loop through the columns corresponding to 'kriteria'
        for ($i = 0; $i < $row_kr_count; $i++) {
            // Get the kriteria value (i-th column in the row)
            
            $nilai = $row[$i+1];

            // Insert the data into the 'nilai' table
            DB::table('nilai')->insert([
                'id_alternatif' => $alternatifId, // Assuming you have a relation to 'alternatif'
                'id_kriteria' => $kriteriaData[$i]->id,  // Assuming 'i' is the correct 'kriteria' ID. You may need to adjust this if you have specific logic to map column index to kriteria ID.
                'nilai' => $nilai, // Assuming the value to be inserted is 'nilai' (adjust accordingly)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }}


}
