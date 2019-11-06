<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Gaji Karyawan Periode {{date("F", mktime(0, 0, 0, $bulan, 10))}} {{$tahun}}</h4>
            <hr>
            @if (array_search("EMESD",$page) && $salary)
                <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5" onclick="deleteGaji({{$salary->id}})">Delete Data Gaji</a>
            @endif
            <hr><br>
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan Jabatan</th>
                    <th>Bonus Jabatan</th>
                    <th>Total Bonus</th>
                    <th>Take Home Pay</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @php($i = 1)
                    @foreach($salaries as $salary)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$salary->employee->name}}</td>
                        <td>Rp. {{number_format($salary->gaji_pokok)}}</td>
                        <td>Rp. {{number_format($salary->tunjangan_jabatan)}}</td>
                        <td>Rp. {{number_format($salary->bonus_jabatan)}}</td>
                        <td>Rp. {{number_format($salary->bonus)}}</td>
                        <td>Rp. {{number_format($salary->take_home_pay)}}</td>
                        <td>
                            @if (array_search("EMESE",$page))
                                <a href="javascript:;" onclick="getDetail({{$salary->employee_id}},{{$bulan}},{{$tahun}})" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5"><i class="fa fa-file-pdf-o"></i>View Detail Bonus</a>
                            @endif
                        </td>
                    </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> <!-- end row -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="myLargeModalLabel"><strong>Detail Bonus Karyawan</strong></h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    // Responsive Datatable
    $('#responsive-datatable').DataTable();

    function getDetail(id,bulan,tahun){
        $.ajax({
            url : "{{route('detGajiPegawai')}}",
            type : "get",
            dataType: 'json',
            data:{
                employee:id,
                bulan:bulan,
                tahun:tahun,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function deleteGaji(id){
        var token = $("meta[name='csrf-token']").attr("content");

        swal({
            title: 'Apakah Anda Yakin?',
            text: "Data yang terhapus dapat dibuat kembali",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                url: "{{route('deletePerhitunganGaji')}}",
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
                },
            }).done(function (data) {
                swal(
                    'Deleted!',
                    'Data Berhasil dihapus',
                    'success'
                )
                location.reload();
            }).fail(function (msg) {
                swal(
                    'Failed',
                    'Data belum terhapus',
                    'error'
                )
            });
            
        }, function (dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            if (dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Data belum terhapus',
                    'error'
                )
            }
        })
    }
    
</script>