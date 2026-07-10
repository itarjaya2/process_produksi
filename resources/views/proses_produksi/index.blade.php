<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Proses Produksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-blue-800">DATA PROSES PRODUKSI</h1>
            <a href="{{ route('proses-produksi.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold text-sm shadow">
                + Tambah Data
            </a>
        </div>
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
<div class="bg-white p-4 mb-6 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('proses-produksi.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            
            <div class="flex-1 min-w-[200px]">
                <label for="proses" class="block text-xs font-bold text-gray-700 mb-1 uppercase">Filter Proses</label>
                <select name="proses" id="proses" class="w-full border border-gray-300 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- SEMUA PROSES --</option>
                    @foreach($masterProses as $prosesName)
                        <option value="{{ $prosesName }}" {{ request('proses') == $prosesName ? 'selected' : '' }}>
                            {{ $prosesName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label for="id" class="block text-xs font-bold text-gray-700 mb-1 uppercase">Cari ID</label>
                <input type="number" name="id" id="id" value="{{ request('id') }}" placeholder="Contoh: 15" 
                       class="w-full border border-gray-300 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white font-semibold text-sm px-5 py-2 rounded-md hover:bg-blue-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('proses-produksi.index') }}" class="bg-gray-200 text-gray-800 font-semibold text-sm px-5 py-2 rounded-md hover:bg-gray-300 transition flex items-center">
                    Reset
                </a>
            </div>
        </form>
    </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 border border-gray-200">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 border-b text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3 border-b">Tanggal</th>
                        <th scope="col" class="px-4 py-3 border-b">Job</th>
                        <th scope="col" class="px-4 py-3 border-b">Proses</th>
                        <th scope="col" class="px-4 py-3 border-b">Product</th>
                        <th scope="col" class="px-4 py-3 border-b">Docket</th>
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
                    @forelse ($prosesProduksi as $index => $data)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-medium">{{ $data->id }}</td>
                            <td class="px-4 py-3">{{ $data->tanggal ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">
                            @if($data->job)
                                <a href="{{ route('proses-produksi.show', $data->job) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $data->job }}
                                </a>
                            @else
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">
                                Belum ada data proses produksi yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-600 border border-gray-200">
        </table>
        </div>

        <div class="mt-4">
            {{ $prosesProduksi->links() }}
        </div>
        </div>

    </div>
</body>
</html>