<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangkuman Job {{ $job_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS tambahan untuk merapikan border tabel mirip Excel */
        table { border-collapse: collapse; }
        th, td { border: 1px solid #374151; /* gray-700 */ }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg p-6">
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-6 items-center">
                <div class="border border-blue-600 rounded px-4 py-2 flex items-center gap-2">
                    <span class="font-bold text-gray-700">JOB :</span>
                    <span class="font-bold text-blue-700 text-lg">{{ $job_id }}</span>
                </div>
                {{-- <div class="border border-blue-600 rounded px-4 py-2 flex items-center gap-2">
                    <span class="font-bold text-gray-700">DOCKET :</span>
                    <span class="font-bold text-blue-700 text-lg">{{ $docket }}</span>
                </div> --}}
            </div>
            
            <div class="text-center w-full absolute left-0 right-0 pointer-events-none">
                <h1 class="text-3xl font-black text-black tracking-widest">RANGKUMAN</h1>
            </div>

            <a href="{{ route('proses-produksi.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md font-semibold text-sm shadow z-10 relative">
                Kembali
            </a>
        </div>

        <div class="overflow-x-auto mt-8">
            <table class="w-full text-xs font-semibold text-center text-gray-800">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="px-2 py-3 w-40">PROCESS</th>
                        <th class="px-2 py-3">JAM</th>
                        <th class="px-2 py-3">JT DRIK</th>
                        <th class="px-2 py-3">JT PCS</th>
                        <th class="px-2 py-3">OUTPUT/DRIK</th>
                        <th class="px-2 py-3">OUTPUT/PCS</th>
                        <th class="px-2 py-3">TOTAL PENGERJAAN<br>(DRIK)</th>
                        <th class="px-2 py-3">SELISIH<br>DRIK</th>
                        <th class="px-2 py-3">TOTAL PENGERJAAN<br>(PCS)</th>
                        <th class="px-2 py-3">SELISIH<br>PCS</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($rangkuman as $row)
                        <tr class="hover:bg-yellow-50">
                            <td class="px-2 py-2 bg-gray-100 text-left font-bold uppercase">{{ $row['proses'] }}</td>
                            
                            <td class="px-2 py-2">{{ $row['jam'] == 0 ? '0' : $row['jam'] }}</td>
                            <td class="px-2 py-2">{{ $row['jt_drik'] == 0 ? '0' : number_format($row['jt_drik'], 0, ',', '.') }}</td>
                            <td class="px-2 py-2">{{ $row['jt_pcs'] == 0 ? '0' : number_format($row['jt_pcs'], 0, ',', '.') }}</td>
                            <td class="px-2 py-2">{{ $row['output_drik'] == 0 ? '0' : number_format($row['output_drik'], 0, ',', '.') }}</td>
                            <td class="px-2 py-2">{{ $row['output_pcs'] == 0 ? '0' : number_format($row['output_pcs'], 0, ',', '.') }}</td>
                            
                            <td class="px-2 py-2 bg-gray-50">{{ $row['total_pengerjaan_drik'] == 0 ? '0' : number_format($row['total_pengerjaan_drik'], 0, ',', '.') }}</td>
                            
                            <td class="px-2 py-2 bg-orange-100">{{ $row['selisih_drik'] == 0 ? '0' : number_format($row['selisih_drik'], 0, ',', '.') }}</td>
                            
                            <td class="px-2 py-2 bg-gray-50">{{ $row['total_pengerjaan_pcs'] == 0 ? '0' : number_format($row['total_pengerjaan_pcs'], 0, ',', '.') }}</td>
                            
                            <td class="px-2 py-2 bg-blue-100">{{ $row['selisih_pcs'] == 0 ? '0' : number_format($row['selisih_pcs'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="overflow-x-auto mt-8"> 
        <table class="w-full text-sm text-left text-gray-600 border border-gray-200">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 border-b text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3 border-b">Tanggal</th>
                        <th scope="col" class="px-4 py-3 border-b">Job</th>
                        <th scope="col" class="px-4 py-3 border-b">Proses</th>
                        <th scope="col" class="px-4 py-3 border-b">Product</th>
                        <th scope="col" class="px-4 py-3 border-b">Designno</th>
                        <th scope="col" class="px-4 py-3 border-b">Operator</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">Jam</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">Shift</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">PO</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">Input</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">jtpcs</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">jtdrik</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">upspk</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">outputpcs</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">outputdrik</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">total pengerjaan drik</th>
                        <th scope="col" class="px-4 py-3 border-b text-center">total pengerjaan pcs</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Menggunakan forelse agar jika data kosong, ada pesan yang tampil --}}
                    {{-- PERBAIKAN: Gunakan variabel $detailProses dari Controller --}}
                    @forelse ($detailProses as $index => $data)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-medium">{{ $data->id }}</td>
                            <td class="px-4 py-3">{{ $data->tanggal ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">
                                @if($data->job)
                                    <a href="{{ route('proses-produksi.show', $data->job) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $data->job }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">
                                    {{ $data->proses ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $data->product ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $data->designno ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $data->operator ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->jam_kalkulasi ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->shift ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->po ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->input ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->jtpcs ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->jtdrik ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->upspk ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->outputpcs ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->outputdrik ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->total_pengerjaan_drik ?? '0' }}</td>
                            <td class="px-4 py-3 text-center">{{ $data->total_pengerjaan_pcs ?? '0' }}</td>
                        </tr>
                    @empty
                        <tr>
                            {{-- PERBAIKAN: colspan diubah menjadi 17 menyesuaikan jumlah kolom --}}
                            <td colspan="17" class="px-4 py-8 text-center text-gray-500 italic">
                                Belum ada riwayat detail pengerjaan untuk Job ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
              </div>
    </div>
</body>
</html>