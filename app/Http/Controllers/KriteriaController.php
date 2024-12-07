<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function store(Request $request, int | string $id)
    {
        $validated = $request->validate([
            'kriteria' => 'required|string|max:255',
            'kriteriaNama' => 'required|string|max:255',
            'bobot' => 'required|numeric',
            'kriteriaJenis' => 'required|string|max:50',
            'pilihan' => 'array|nullable',
        ]);

        // Query Raw untuk menghitung total bobot
        $totalBobot = DB::table('kriteria') // Ganti 'kriteria' dengan nama tabel yang sesuai
            ->where('id_pengambilan_keputusan', $id)
            ->sum('bobot') + ($request->bobot / 100); // Hitung total bobot

        // Periksa apakah total bobot lebih dari 1
        if ($totalBobot > 1) {
        // Jika bobot lebih dari 1, kembalikan error
            return response()->json([
                'status' => 'error',
                'message' => 'Bobot sudah tidak cukup'
            ]);
        }

    
        // // Jika validasi berhasil, lanjutkan dengan logika Anda
        // // Misalnya menyimpan data ke database
    
        // return response()->json([
        //     'message' => 'Data berhasil disimpan!',
        //     'data' => $validated, // Kembalikan data yang sudah tervalidasi
        // ], 200);

        // Memeriksa keberadaan id_pengambilan_keputusan
        // $data = DB::select("SELECT * FROM kriteria WHERE id_pengambilan_keputusan = ?", [$request->id]);
        // if (!$data) {
        //     return response()->json([
        //         "error" => "Gagal insert! ID pengambilan keputusan tidak valid."
        //     ], 400);
        // }

        // Menyimpan ke tabel kriteria
        $kriteriaId = DB::table('kriteria')->insertGetId([
            'id_pengambilan_keputusan' => $id,
            'kriteria' => $request->kriteria,
            'nama' => $request->kriteriaNama,
            'bobot' => ($request->bobot) / 100,
            'jenis' => $request->kriteriaJenis  
        ]);

        // Mengambil alternatif berdasarkan keputusan
        $keputusanByAlternatif = DB::select("SELECT DISTINCT nilai.id_alternatif AS id_alternatif, kriteria.id_pengambilan_keputusan AS id_keputusan FROM nilai JOIN kriteria ON kriteria.id = nilai.id_kriteria WHERE kriteria.id_pengambilan_keputusan = ?", [$id]);

        // Menyimpan nilai untuk setiap alternatif
        foreach ($keputusanByAlternatif as $n) {
            DB::table('nilai')->insert([
                'id_alternatif' => $n->id_alternatif,
                'id_kriteria' => $kriteriaId,
                'nilai' => 0
            ]);
        }

        // Menyimpan pilihan jika ada
        if (isset($request->pilihan) && is_array($request->pilihan)) {
            foreach ($request->pilihan as $p) {
                $parts = explode('.', $p);
                $value = $parts[0]; // Ambil angka sebelum titik
                $name = $parts[1] ?? ''; // Ambil nama setelah titik, jika ada

                // Insert pilihan_kriteria
                DB::insert("INSERT INTO pilihan_kriteria (value, name, id_kriteria) VALUES (?, ?, ?)", [
                    $value,
                    $name,
                    $kriteriaId
                ]);
            }
        }

        return response()->json([
            "message" => "Data berhasil disimpan.",
            "data" => [
                "id_kriteria" => $kriteriaId,
                "input" => $request->input()
            ]
        ], 201);
    }


    public function edit(Request $request, $id_keputusan_kriteria, $id){
        try {
            // Validasi input yang diterima dari form
            $validated = $request->validate([
                'kriteria'  => 'required|string|max:255',
                'nama'  => 'required|string|max:255',
                'bobot' => 'required|numeric|min:0|max:100',
                'jenis' => 'required|string|max:255',
            ]);
    
            // Menyiapkan query raw untuk memperbarui data kriteria
            $query = 'UPDATE kriteria SET kriteria = ?,nama = ?, bobot = ?, jenis = ? WHERE id = ?';
    
            // Menjalankan query menggunakan parameter yang valid
            $updated = DB::update($query, [
                $validated['kriteria'],
                $validated['nama'],
                $validated['bobot'] / 100,
                $validated['jenis'],
                $id
            ]);
    
            // Cek apakah update berhasil
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kriteria berhasil diperbarui.'
                ]);
            } else {
                // Jika data tidak ditemukan atau tidak ada yang berubah
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau tidak ada perubahan.'
                ], 404);
            }
    
        } catch (\Exception $e) {
            // Tangani jika ada kesalahan, misalnya kesalahan SQL
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request,$id_keputusan_kriteria, $id)
    {
        DB::beginTransaction();  // Mulai transaksi database

        try {
            // Cek apakah ID kriteria ada di tabel 'nilai'
            $nilaiExists = DB::table('nilai')->where('id_kriteria', $id)->exists();
    
            // Jika ada data yang terkait di tabel 'nilai', hapus dulu data tersebut
            if ($nilaiExists) {
                $deleted_kriteria_nilai = DB::delete('DELETE FROM nilai WHERE id_kriteria = ?', [$id]);
            } else {
                $deleted_kriteria_nilai = true;  // Set true jika tidak ada data di tabel nilai, tidak perlu hapus
            }
    
            // Menghapus data dari tabel 'kriteria'
            $deleted_pilihan_kriteria = DB::delete('DELETE FROM pilihan_kriteria WHERE id_kriteria = ?', [$id]);
            $deleted_kriteria = DB::delete('DELETE FROM kriteria WHERE id = ?', [$id]);
    
            // Memeriksa apakah kedua penghapusan berhasil
            if ($deleted_kriteria_nilai && $deleted_kriteria || $deleted_pilihan_kriteria || $deleted_kriteria || $deleted_pilihan_kriteria) {
                DB::commit();  // Jika semua penghapusan berhasil, commit transaksi
                return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
            } else {
                DB::rollBack();  // Jika ada penghapusan yang gagal, rollback transaksi
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau gagal dihapus.'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();  // Jika terjadi error, rollback transaksi
            // Tangani error jika terjadi kesalahan saat menghapus
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
 
    public function getRincianBobot($id)
    {
        // Mengambil rincian bobot menggunakan raw query
        $rincianBobot = DB::select("
            SELECT kriteria, bobot
            FROM kriteria
            WHERE id_pengambilan_keputusan = :id_kriteria
        ", ['id_kriteria' => $id]);

        // Menghitung total bobot menggunakan raw query
        $totalBobot = DB::select("
            SELECT SUM(bobot) as total_bobot
            FROM kriteria
            WHERE id_pengambilan_keputusan = :id_kriteria
        ", ['id_kriteria' => $id]);

        // Mendapatkan nilai total bobot dari hasil query
        $totalBobot = $totalBobot[0]->total_bobot;

        return response()->json([
            'rincian_bobot' => $rincianBobot,
            'total_bobot' => $totalBobot
        ]);
    }
     

}
