<x-app-layout>
    {{-- @dd() --}}
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Details') }}
            </h2>
            <x-button-group-init>
                {{-- <x-button-group-content-start :href="route('dataLengkapExport')">
                    <i class="bi bi-file-excel-fill text-green-500 text-2xl" aria-hidden="true"></i>
                </x-button-group-content-start> --}}
                <x-button-group-content-start :href="route('dataLengkapCreate')">
                    <i class="fa fa-plus text-blue-600 text-2xl"></i>
                </x-button-group-content-start>
                {{-- <x-button-group-content-end href="/storage/dataLengkap.xlsx">
                    <i class="fa fa-download text-green-500 text-2xl"></i>
                </x-button-group-content-end> --}}
                <x-button-group-content-end href="{{ route('spatie') }}">
                    <i class="fa fa-download text-green-500 text-2xl"></i>
                </x-button-group-content-end>
            </x-button-group-init>
        </div>
    </x-slot>

    <div class="max-w-sm sm:max-w-7xl mx-auto my-10">
        <table id="detailTable" class="display">
            <thead>
                <tr>
                    <th>Pengirim</th>
                    <th>Kecamatan</th>
                    <th>Kelurahan</th>
                    <th>RT</th>
                    <th>RW</th>
                    <th>TPS</th>
                    <th>DPT</th>
                    <th>Surat Sah</th>
                    <th>Surat Tidak Sah</th>
                    <th>Surat Rusak</th>
                    <th>Pemilih Hadir</th>
                    <th>Pemilih Tidak Hadir</th>
                    <th>Partai</th>
                    <th>Gambar</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>
                    <th>Caleg1</th>
                    <th>Suara1</th>
                    <th>Caleg2</th>
                    <th>Suara2</th>
                    <th>Caleg3</th>
                    <th>Suara3</th>
                    <th>Caleg4</th>
                    <th>Suara4</th>
                    <th>Caleg5</th>
                    <th>Suara5</th>
                    <th>Caleg6</th>
                    <th>Suara6</th>
                    <th>Caleg7</th>
                    <th>Suara7</th>
                    <th>Caleg8</th>
                    <th>Suara8</th>
                    <th>Caleg9</th>
                    <th>Suara9</th>
                    <th>Caleg10</th>
                    <th>Suara10</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</x-app-layout>

<script>
    $(document).ready( function () {
        $('#detailTable').DataTable({
            "searching": true,
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            "ajax": "{{ route('ApiDataLengkap') }}",
            "columns" : [
                {"data" : "pengirim", "name" : "users.name"},
                {"data" : "kecamatan", "name" : "master_kecamatans.name"},
                {"data" : "kelurahan", "name" : "master_kelurahans.name"},
                {"data" : "rt"},
                {"data" : "rw"},
                {"data" : "no_tps"},
                {"data" : "total_dpt"},
                {"data" : "total_sss"},
                {"data" : "total_ssts"},
                {"data" : "total_ssr"},
                {"data" : "pemilih_hadir"},
                {"data" : "pemilih_tidak_hadir"},
                {"data" : "partai", "name" : "master_partais.name"},
                {
                    "data" : "image",
                    "render" : function(data, type, row){
                        return '<img src="/storage/' +data+ '" '+ 'alt="plano" ' + '/>'
                    }
                },
                {"data" : "created_at"},
                {"data" : "updated_at"},
                {"data" : "caleg1", "name" : "caleg_groups.caleg1"},
                {"data" : "suara1", "name" : "suara_groups.suara1"},
                {"data" : "caleg2", "name" : "caleg_groups.caleg2"},
                {"data" : "suara2", "name" : "suara_groups.suara2"},
                {"data" : "caleg3", "name" : "caleg_groups.caleg3"},
                {"data" : "suara3", "name" : "suara_groups.suara3"},
                {"data" : "caleg4", "name" : "caleg_groups.caleg4"},
                {"data" : "suara4", "name" : "suara_groups.suara4"},
                {"data" : "caleg5", "name" : "caleg_groups.caleg5"},
                {"data" : "suara5", "name" : "suara_groups.suara5"},
                {"data" : "caleg6", "name" : "caleg_groups.caleg6"},
                {"data" : "suara6", "name" : "suara_groups.suara6"},
                {"data" : "caleg7", "name" : "caleg_groups.caleg7"},
                {"data" : "suara7", "name" : "suara_groups.suara7"},
                {"data" : "caleg8", "name" : "caleg_groups.caleg8"},
                {"data" : "suara8", "name" : "suara_groups.suara8"},
                {"data" : "caleg9", "name" : "caleg_groups.caleg9"},
                {"data" : "suara9", "name" : "suara_groups.suara9"},
                {"data" : "caleg10", "name" : "caleg_groups.caleg10"},
                {"data" : "suara10", "name" : "suara_groups.suara10"},
                {"data" : "action"}
            ]
        });
    });
</script>