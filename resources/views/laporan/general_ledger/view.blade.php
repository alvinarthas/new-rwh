@php
    use Carbon\Carbon;
@endphp
<div class="card-box">
    <h4 class="m-t-0 header-title">General Ledger Detail</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Account Number</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$coa->AccNo}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Account Name</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$coa->AccName}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Oppening Balance</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($coa->SaldoAwal)}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Saldo Normal</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$coa->SaldoNormal}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Current Balance</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($current)}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Date</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Position</th>
            <th>Amount</th>
            <th>Action</th>
        </thead>
        <tbody>
            @csrf
            @php($i=1)
            @foreach ($jurnals as $jurnal)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{ Carbon::parse($jurnal->date)->format('d M Y')}}</td>
                    <td>{{$jurnal->description}}</td>
                    <td>{{$jurnal->notes_item}}</td>
                    <td>{{$jurnal->AccPos}}</td>
                    <td>{{number_format($jurnal->Amount)}}</td>
                    <td><a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" onclick="viewJurnal({{$jurnal->id_jurnal}})"><i class="fa fa-file-pdf-o"></i> Preview Invoice</a></td>
                </tr>
            @php($i++)
            @endforeach
        </tbody>
    </table>

    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="custom-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                </div>
                <div class="modal-body" id="modalView">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<div class="card-box">
    <h4 class="m-t-0 header-title">Total Jurnal</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Debet</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_debet)}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Credit</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_credit)}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script>
// Responsive Datatable
$('#responsive-datatable').DataTable();

function viewJurnal(id){
    $.ajax({
        url : "{{route('viewGlJurnal')}}",
        type : "get",
        dataType: 'json',
        data:{
            jurnal_id:id,
        },
    }).done(function (data) {
        $('#modalView').html(data);
        $('#modalLarge').modal("show");
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}
</script>
    