@if($jenis == "create")
    <form class="form-horizontal" role="form" action="{{ route('storePerusahaanMember',['ktp' => $ktp]) }}" enctype="multipart/form-data" method="POST">
@elseif($jenis == "edit")
    <form class="form-horizontal" role="form" action="{{ route('updatePerusahaanMember',['ktp' => $ktp,'pid'=>$perid->id]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
@endif

@csrf
    <div class="card-box">
        <h4 class="m-t-0 header-title">Form Perusahaan Member</h4>
        <div class="row">
            <div class="col-12">
                <div class="p-20">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Perusahaan</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="perusahaan" id="perusahaan">
                                <option value="#" disabled selected>Pilih Perusahaan</option>
                                @foreach ($perusahaan as $per)
                                    @isset($perid->perusahaan_id)
                                        @if ($per->id == $perid->perusahaan_id)
                                            <option value="{{$per->id}}" selected>{{$per->nama}}</option>
                                        @else
                                            <option value="{{$per->id}}">{{$per->nama}}</option>
                                        @endif
                                    @else
                                        <option value="{{$per->id}}">{{$per->nama}}</option>
                                    @endisset
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor ID</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="nomorid" id="nomorid" value="@isset($perid->noid){{$perid->noid}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Password</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="password" id="password" value="@isset($perid->passid){{$perid->passid}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Posisi</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="posisi" id="posisi" value="@isset($perid->posisi){{$perid->posisi}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Status Akun</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="status" id="status">
                                <option value="#" disabled selected>Pilih Status Akun</option>
                                @foreach ($statusnoid as $stat)
                                    @isset($perid->status)
                                        @if ($stat->id == $perid->status)
                                            <option value="{{$stat->id}}" selected>{{$stat->status}}</option>
                                        @else
                                            <option value="{{$stat->id}}">{{$stat->status}}</option>
                                        @endif
                                    @else
                                        <option value="{{$stat->id}}">{{$stat->status}}</option>
                                    @endisset
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            <button class="btn btn-primary waves-effect waves-light" type="submit">
                Submit
            </button>
        </div>
    </div>
</form>

<script>
    // Select2
    $(".select2").select2({});
</script>