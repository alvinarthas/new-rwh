
<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Jurnal ID</th>
            <th>Transaction Date</th>
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Notes</th>
            <th>Description</th>
            @if($param == "umum")
            <th>Option</th>
            @endif
        </thead>
        <tbody>
            @csrf
            @php($i=1)
            @foreach ($jurnals['data'] as $jurnal)
            @isset($jurnal->coa->AccName)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$jurnal->id_jurnal}}</td>
                    <td>{{$jurnal->date}}</td>
                    <td>{{$jurnal->AccNo}}</td>
                    <td>{{$jurnal->coa->AccName}}</td>
                    @if ($jurnal->AccPos == "Debet")
                        <td>Rp {{number_format($jurnal->Amount,2,",",".")}}</td>
                    @else <td></td> @endif
                    @if ($jurnal->AccPos == "Credit")
                        <td>Rp {{number_format($jurnal->Amount,2,",",".")}}</td>
                    @else <td></td> @endif
                    <td>{{$jurnal->notes_item}}</td>
                    <td>{{$jurnal->description}}</td>
                    @php($id = substr($jurnal->id_jurnal,0,2))
                    @if ($param == "umum")
                    <td>

                        @if(array_search("FIJUE",$page))
                        <a href="{{route('jurnal.edit',['id'=>$jurnal->id_jurnal])}}" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" >Update</a>
                        @endif
                        @if(array_search("FIJUD",$page))
                        <a href="javascript:;" onclick="jurnalDelete('{{$jurnal->id_jurnal}}')" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5">Delete</a>
                        @endif

                    </td>
                    @endif
                </tr>
                @php($i++)
            @endisset
            @endforeach
        </tbody>
    </table>
</div>
<div class="card-box">
    <h4 class="m-t-0 header-title">Total Jurnal</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Debet</label>
                <div class="col-10">
                    @if ($jurnals['ttl_debet'] < 0)
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp ({{number_format($jurnals['ttl_debet'],2,',','.')}})" readonly>
                    @else
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($jurnals['ttl_debet'],2,',','.')}}" readonly>
                    @endif

                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Credit</label>
                <div class="col-10">
                    @if ($jurnals['ttl_credit'] < 0)
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp ({{number_format($jurnals['ttl_credit'],2,',','.')}})" readonly>
                    @else
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($jurnals['ttl_credit'],2,',','.')}}" readonly>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable({
     columnDefs: [
       {targets: '_all', type: 'natural'}
     ]
  } );

function jurnalDelete(id){
    var token = $("meta[name='csrf-token']").attr("content");

    swal({
        title: 'Apa kamu yakin akan menghapus semua data '+id+'?',
        text: "Kamu tidak akan dapat mengembalikan data yang sudah terhapus!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Tidak, Batalkan!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url: "jurnal/"+id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
            location.reload();
        }).fail(function (msg) {
            swal(
                'Failed',
                'Your imaginary file is safe :)',
                'error'
            )
        });

    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            swal(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
}
</script>
