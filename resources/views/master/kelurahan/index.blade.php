<x-app-layout>
    {{-- @dd($posts) --}}
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Master Kelurahan') }}
            </h2>
            <x-button-group-init>
                <x-button-group-content-middle :href="route('kelurahanCreate')">
                    <i class="fa fa-plus text-blue-600 text-lg"></i>
                </x-button-group-content-middle>
            </x-button-group-init>
        </div>
    </x-slot>

    <div class="max-w-sm sm:max-w-7xl mx-auto my-10">
        <table id="kelurahanTable" class="display">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelurahan</th>
                    <th>Kecamatan</th>
                    <th>Dibuat</th>
                    <th>Diedit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
</x-app-layout>

<script>
    $(document).ready( function () {
        $('#kelurahanTable').DataTable({
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            "ajax": "{{ route('ApiMasterKelurahan') }}",
            "columns" : [
                {"data" : "id"},
                {"data" : "kelurahan", "name" : "master_kelurahans.name"},
                {"data" : "kecamatan", "name" : "master_kecamatans.name"},
                {"data" : "created_at"},
                {"data" : "updated_at"},
                {"data" : "action"}
            ]
        });
    });
</script>