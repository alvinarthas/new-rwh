<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Periode Poin: {{$start}} - {{$end}}</h4>
    <p class="text-muted font-14 m-b-30">
        {{$nama}}
    </p>
    @csrf
    <table id="modal-table" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jumlah Poin</th>
            <th>Input By</th>
            <th>Action</th>
        </thead>

        <tbody>
            @php($i = 1)
            @foreach($datas as $data)
            <tr>
                <td>{{$i}}</td>
                <td>{{$data['date']}}</td>
                <td>{{$data['poin']}}</td>
                <td>{{$data['creator']}}</td>
                <td>
                    @if (array_search("EMEPD",$page))
                        <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="deletePoin({{$data['id']}})"><i class="fa fa-file-pdf-o"></i>Delete Poin</a>
                    @endif
                </td>
            </tr>
            @php($i++)
            @endforeach
        </tbody>
    </table>
</div>

<script>
// Responsive Datatable
$('#modal-table').DataTable();

function deletePoin(id){
    token = $("meta[name='csrf-token']").attr("content");
    swal({
        title: 'Apakah Anda yakin ?',
        text: "Poin tidak bisa dikembalikan lagi",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url : "{{route('delPoin')}}",
            type : "DELETE",
            dataType: 'json',
            data:{
                id: id,
                _token : token,
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Poin telah dihapus.',
                'success'
            )
            location.reload();
        }).fail(function (msg) {
            swal(
                'Failed',
                'Poin tidak kehapus :)',
                'error'
            )
        });
        
    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            swal(
                'Cancelled',
                'Poin tidak kehapus :)',
                'error'
            )
        }
    })
}
</script>