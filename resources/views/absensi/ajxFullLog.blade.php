<div class="col-12">
    <div class="card-box table-responsive">
        <h4 class="m-t-0 header-title">Log Kehadiran: {{$tanggal_awal}} s/d {{$tanggal_akhir}}</h4>
        <p class="text-muted m-b-30 font-14">
        </p>

        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <th>No</th>
                <th>Username</th>
                <th>Log Kehadiran</th>
                <th>Data</th>
                <th>Keterangan</th>
            </thead>
            <tbody>
                @php($i=1)
                @foreach ($logs as $log)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$log->user_name}}</td>
                        <td>{{$log->log_time}}</td>
                        <td>{{$log->data}}</td>
                        <td>{{$log->keterangan}}</td>
                    </tr>
                    @php($i++)
                @endforeach
            </tbody>
        </table>
    </div>
</div>