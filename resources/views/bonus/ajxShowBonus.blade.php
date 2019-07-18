    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Detail Bonus Member</h4>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>KTP</th>
                        <th>No ID</th>
                        <th>Nama</th>
                        <th>Bonus</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($perusahaanmember as $prm)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prm->ktp}}</td>
                            <td>{{$prm->noid}}</td>
                            <td>{{$prm->nama}}</td>
                            <td><input disabled id="number" value="{{ $bonus->where('member_id', $prm->noid)->first()->bonus }}"></td>
                        </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
