@extends('layouts.master-admin')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row p-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">Halaman Data Orang Tua</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active"><a href="{{ url('admin') }}">Dashboard</a></li>
                            <li class="breadcrumb-item ">Data Orang Tua</li>
                        </ol>
                    </div>
                </div>

                {{-- Konten Data --}}
                <div class="card p-4 m-2">
                    <div class="row">
                        <div class="col-6">
                            <h5>Data Orang Tua</h5>
                        </div>
                        <div class="col-6">
                            <div class="float-right mb-3">
                            <button id="printBtn" class="btn btn-success">Print Data</button>
                                <a href="{{ route('tambah-ortuadm') }}" class="btn btn-primary"><i
                                        class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="table-striped" id="table" style="min-width: 950px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Nama Orang Tua</th>
                                    <th>ID Siswa</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Nomor Telepon</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ortu as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>
                                            @if ($row->foto)
                                                <img src="{{ asset('uploads/ortu/' . $row->foto) }}" {{ $row->nama }}
                                                    style="width: 50px; height: 50px; border-radius: 50px;">
                                            @else
                                                No Photo
                                            @endif
                                        </td>
                                        <td>{{ $row->user ? $row->user->name : 'Belum ada Ortu' }}</td>
                                        <td>{{ $row->id_siswa ? $row->id_siswa : 'Belum ada siswa' }}</td>
                                        <td>{{ $row->tgl_lahir }}</td>
                                        <td>{{ $row->jenis_kelamin }}</td>
                                        <td>{{ $row->nomor_telepon }}</td>
                                        <td>{{ $row->alamat }}</td>
                                        <td>
                                            <a href="{{ route('update-ortuadm', ['id' => $row->id]) }}"
                                                class="btn btn-warning btn-sm" role="button">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm deletebtn"
                                                data-id="{{ $row->id }}">Hapus</button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('addJS')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function download() {
            // Clone the table without the photo and action columns
            var print = $('#table').clone();
            print.find('th:nth-child(2), td:nth-child(2), th:last-child, td:last-child').remove();

            // Open a new window and write the table to it
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write('<h1>Rekapitulasi Data Orang Tua</h1>');
            printWindow.document.write(
                '<table border=1 style="border-collapse:collapse; width: 100%; text-align: center;">' + print
                .html() + '</table>');
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.print();
        }

        $(document).ready(function() {
            table = $('#table').DataTable();

            $('#filter_kelas').change(function() {
                var pilihkelas = $(this).val();
                table.column(3).search(pilihkelas).draw();
            });

            $('.deletebtn').click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                Swal.fire({
                    title: "Apakah anda yakin?",
                    text: "Data yang sudah dihapus, tidak bisa dipulihkan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.post('{{ route('delete-ortuadm', ['id' => '__id__']) }}'.replace('__id__',
                                id), {
                                '_token': '{{ csrf_token() }}',
                                'id': id
                            },
                            function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Data berhasil dihapus!",
                                    icon: "success"
                                });

                                // Timer sebelum refresh halaman
                                setTimeout(function() {
                                    window.location.href = window.location.href;
                                }, 1000);
                            }).fail(function(error) {
                            console.error('Error deleting record: ', error);
                        });
                    }
                });
            });

            // Initialize DataTable
            $('#table').DataTable();

            $('#printBtn').on('click', function() {
                download();
            });
        });
    </script>
@endsection
