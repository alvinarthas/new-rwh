<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <input type="hidden" id="start" value="{{$start}}">
            <input type="hidden" id="end" value="{{$end}}">
            <h4 class="m-t-0 header-title">Index Poin Pegawai</h4>

            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Periode Poin</th>
                    <th>Jumlah Poin</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @php($i = 1)
                    @foreach($datas as $data)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$data['username']}}</td>
                        <td>{{$start}} - {{$end}}</td>
                        <td>{{$data['poin']}}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" onclick="viewDetail({{$data['id']}})"><i class="fa fa-file-pdf-o"></i>View Detail Poin</a>
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
                <h4 class="modal-title" id="myLargeModalLabel">Detail Poin Pegawai</h4>
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

    function viewDetail(id){
        start = $('#start').val();
        end = $('#end').val();

        $.ajax({
            url : "{{route('detailPoin')}}",
            type : "get",
            dataType: 'json',
            data:{
                employee_id:id,
                start:start,
                end:end,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
    
</script>