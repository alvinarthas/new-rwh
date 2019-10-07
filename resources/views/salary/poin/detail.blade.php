<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Periode Poin: {{$start}} - {{$end}}</h4>
    <p class="text-muted font-14 m-b-30">
        {{$nama}}
    </p>

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
                    <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="deletePoin({{$data['id']}})"><i class="fa fa-file-pdf-o"></i>Delete Poin</a>
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
</script>