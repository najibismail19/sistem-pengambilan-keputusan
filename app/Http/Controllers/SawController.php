<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SawController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {

            $data = DB::select("SELECT * FROM pengambilan_keputusan");

            return (count($data) > 0) ? response()->json($data) : "empty";
        }
    
        return view("page.saw.list_data_saw");
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'tujuan' => 'required|string|max:255',
        ]);
    
        // Insert into the database using a raw SQL statement
        DB::insert('INSERT INTO pengambilan_keputusan (title, date, description, tujuan,user_create, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
            $request->judul,
            $request->tanggal,
            $request->deskripsi,
            $request->tujuan,
            1,
            now(), // for created_at
            now()  // for updated_at
        ]);
    
        return response()->json(['success' => true]);
    }

    public function detail(Request $request, string|int $id)
    {
        // Ambil data kriteria yang sesuai dengan id_pengambilan_keputusan
        $data = DB::select("SELECT * FROM kriteria WHERE id_pengambilan_keputusan = ?", [$id]);
    
        // Ambil id kriteria untuk query nilai
        $id_criteria = array_column($data, 'id');
    
        // Ambil nilai berdasarkan kriteria yang ada
        $nilai = !empty($id_criteria)
            ? DB::select("SELECT DISTINCT id_alternatif FROM nilai WHERE id_kriteria IN (" . implode(',', array_fill(0, count($id_criteria), '?')) . ")", $id_criteria)
            : [];
    
        // Menyiapkan data untuk dikirim ke view
        $alternatifs = [];
        $scores = [];
        $isi = [];
    
        foreach ($nilai as $n) {
            // Ambil data alternatif
            $alternatif = DB::select("SELECT * FROM alternatif WHERE id_alternatif = ?", [$n->id_alternatif])[0] ?? null;
    
            // Ambil nilai untuk setiap alternatif berdasarkan kriteria
            $nilaik = DB::select("SELECT * FROM nilai WHERE id_alternatif = ?", [$n->id_alternatif]);
    
            // Inisialisasi skor dan nilai per kriteria
            $score = 0;
            $s = [];  // Reset nilai per kriteria untuk setiap alternatif
    
            foreach ($data as $index => $d) {
                $nilaikValue = collect($nilaik)->firstWhere('id_kriteria', $d->id)->nilai ?? null;
    
                // Jika tidak ada nilai untuk kriteria ini, lewati
                if ($nilaikValue === null) {
                    continue;
                }
    
                $s[] = $nilaikValue; // Simpan nilai untuk kriteria ini
    
                // Hitung skor berdasarkan jenis kriteria (benefit atau cost)
                if ($d->jenis == "benefit") {
                    // Ambil nilai maksimum untuk kriteria benefit
                    $totalResult = DB::select("SELECT max(nilai) as max_nilai FROM nilai WHERE id_kriteria = ?", [$d->id]);
                    $total = $totalResult[0]->max_nilai ?? 0;
    
                    if ($total > 0) {
                        $hasil = $nilaikValue / $total; // Normalisasi nilai alternatif terhadap nilai maksimum
                        $score += ($hasil * $d->bobot); // Tambahkan hasil ke skor total
                    }
                } elseif ($d->jenis == "cost") {
                    // Ambil nilai minimum untuk kriteria cost
                    $totalResult = DB::select("SELECT min(nilai) as min_nilai FROM nilai WHERE id_kriteria = ?", [$d->id]);
                    $total = $totalResult[0]->min_nilai ?? 0;
    
                    if ($nilaikValue != 0) {
                        $hasil = ($total == 0 ? 1 : $total) / $nilaikValue; // Normalisasi nilai alternatif terhadap nilai minimum
                        $score += ($hasil * $d->bobot); // Tambahkan hasil ke skor total
                    }
                }
            }
    
            // Simpan alternatif, skor, dan nilai untuk setiap kriteria
            $alternatifs[] = $alternatif;
            $scores[] = round(number_format($score, 3), 3);
            $isi[] = $s;
        }
    
        // Kirim data ke view
        if ($request->ajax()) {
            return response()->json([
                'data' => $alternatifs,
                'scores' => $scores,
                'nilai' => $isi,
                'id' => $id
            ]);
        } else {
            return view('page.saw.detail', [
                'data' => $data,
                'alternatifs' => $alternatifs,
                'scores' => $scores,
                'id' => $id
            ]);
        }
    }
    

}
