@if($jenis == "create")
    <form class="form-horizontal" role="form" action="{{ route('storeBankMember',['ktp' => $ktp]) }}" enctype="multipart/form-data" method="POST">
@elseif($jenis == "edit")
    <form class="form-horizontal" role="form" action="{{ route('updateBankMember',['ktp' => $ktp,'bid'=>$banm->id]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
@endif
@csrf
    <div class="card-box">
        <h4 class="m-t-0 header-title">Form Bank Member</h4>
        <div class="row">
            <div class="col-12">
                <div class="p-20">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Bank</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="bank" id="bank">
                                <option value="#" disabled selected>Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    @isset($banm->bank_id)
                                        @if ($bank->id == $banm->bank_id)
                                            <option value="{{$bank->id}}" data-image="{{asset('assets/images/bank/'.$bank->icon)}}" selected>{{$bank->nama}}</option>
                                        @else
                                            <option value="{{$bank->id}}" data-image="{{asset('assets/images/bank/'.$bank->icon)}}">{{$bank->nama}}</option>
                                        @endif
                                    @else
                                        <option value="{{$bank->id}}" data-image="{{asset('assets/images/bank/'.$bank->icon)}}">{{$bank->nama}}</option>
                                    @endisset
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Rekening</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="rekening" id="rekening" value="@isset($banm->norek){{$banm->norek}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Cabang Pembuka</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="cabang" id="cabang" value="@isset($banm->cabbank){{$banm->cabbank}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">No Kartu ATM</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="atm" id="atm" value="@isset($banm->noatm){{$banm->noatm}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">No Buku Tabungan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" parsley-trigger="change" required name="tabungan" id="tabungan" value="@isset($banm->nobuku){{$banm->nobuku}}@endisset">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Status Rekening</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="status_rek" id="status_rek">
                                @isset($banm->status)
                                    <option value="#" disabled>Pilih Status Rekening</option>
                                    @foreach($statusrek as $status)
                                        @if($status->id == $banm->status)
                                            <option value="{{ $status->id }}" selected>{{ $status->status }}</option>
                                        @else
                                            <option value="{{ $status->id }}" selected>{{ $status->status }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="#" selected disabled>Pilih Status Rekening</option>
                                    @foreach($statusrek as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Primary</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="primary" id="primary">
                                @isset($banm->status)
                                    @if ($banm->p_status == "Yes")
                                        <option value="Yes" selected>Yes</option>
                                        <option value="No">No</option>
                                    @elseif($banm->p_status == "No")
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    @endif
                                @else
                                    <option value="Yes" selected>Yes</option>
                                    <option value="No">No</option>
                                @endisset

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Upload Foto Tabungan</label>
                        <div class="col-10">
                            <input type="file" class="dropify" data-height="100" name="scantabungan" id="scantabungan" data-default-file="@isset($banm->scantabungan){{ asset('assets/images/member/tabungan/'.$banm->scantabungan) }}@endisset"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Upload Foto ATM</label>
                        <div class="col-10">
                            <input type="file" class="dropify" data-height="100" name="scanatm" id="scanatm" data-default-file="@isset($banm->scanatm){{ asset('assets/images/member/atm/'.$banm->scanatm) }}@endisset" />
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
    $(".select2").select2({
        templateResult: formatState,
        templateSelection: formatState
    });

    // Dropfiy
    $('.dropify').dropify({
        messages: {
            'default': 'Drag and drop a file here or click',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong appended.'
        },
        error: {
            'fileSize': 'The file size is too big (1M max).'
        }
    });

    function formatState (opt) {
        if (!opt.id) {
            return opt.text.toUpperCase();
        }

        var optimage = $(opt.element).attr('data-image');
        console.log(optimage)
        if(!optimage){
        return opt.text.toUpperCase();
        } else {
            var $opt = $(
            '<span><img src="' + optimage + '" width="60px" /> ' + opt.text.toUpperCase() + '</span>'
            );
            return $opt;
        }
    };
</script>
