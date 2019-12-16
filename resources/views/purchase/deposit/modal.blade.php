<div class="card-box table-responsive">
    <div class="form-group row">
        <label class="col-2 col-form-label">Supplier</label>
        <div class="col-10">
            <input type="text" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="{{$name}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Saldo</label>
        <div class="col-10">
            <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp {{number_format($saldo,2,",",".")}}" readonly>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Creator</th>
                    <th>Keterangan</th>
                    <th>ID Jurnal</th>
                    <th>Option</th>
                </thead>
                <tbody>
                    @php($i=1)
                    @foreach ($details as $key)
                        <tr>
                            <td>{{$i}}</td>
                            <td>Rp {{number_format($key->amount,2,",",".")}}</td>
                            <td>
                                @if ($key->status == 1)
                                    <a href="javascript:;" disabled="disabled" class="btn btn-success btn-rounded waves-effect w-md waves-danger m-b-5" >Masuk</a>
                                @else
                                    <a href="javascript:;" disabled="disabled" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" >Keluar</a>
                                @endif
                            </td>
                            <td>{{$key->creator()->first()->name}}</td>
                            <td>{{$key->keterangan}}</td>
                            <td><a href="javascript:;" disabled="disabled" class="btn btn-custom btn-rounded waves-effect w-md waves-danger m-b-5" >{{$key->jurnal_id}}</a></td>
                            <td>
                                @if (array_search("PUDPS",$page))
                                    <a href="javascript:;" onclick="destory({{$key->id}})" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5">Hapus</a>
                                @endif
                            </td>
                        </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function destory(id){
        var token = $("meta[name='csrf-token']").attr("content");

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                url: "deposit/"+id,
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