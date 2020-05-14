<div class="card-box table-responsive">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Informasi Customer</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Customer ID Number</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="customer_id" id="customer_id" value="@isset($customer->cid){{$customer->cid}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tipe Customer</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="cust_type" disabled>
                                        @if($customer->cust_type == 0)
                                            <option value="0" selected>Customer Offline</option>
                                        @elseif($customer->cust_type == 1)
                                            <option value="1" selected>Customer Online</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="name" id="name" value="@isset($customer->apname){{$customer->apname}}@endisset" required disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="phone" id="phone" value="@isset($customer->apphone){{$customer->apphone}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal Lahir</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="birth" id="birth" value="@isset($customer->apbirthdate){{$customer->apbirthdate}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Email</label>
                                <div class="col-10">
                                    <input type="email" class="form-control" parsley-trigger="change" name="email" id="email" value="@isset($customer->apemail){{$customer->apemail}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <textarea class="form-control" name="address" id="address" disabled>@isset($customer->apadd){{$customer->apadd}}@endisset</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Informasi Perusahaan</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Perusahaan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cname" id="cname" value="@isset($customer->cicn){{$customer->cicn}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <textarea class="form-control" name="cadd" id="cadd" disabled>@isset($customer->ciadd){{$customer->ciadd}}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kota</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="ccity" id="ccity" value="@isset($customer->cicty){{$customer->cicty}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kode Pos</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="czipcode" id="czipcode" value="@isset($customer->cizip){{$customer->cizip}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Provinsi</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cprovince" id="cprovince" value="@isset($customer->cipro){{$customer->cipro}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Website</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cwebsite" id="cwebsite" value="@isset($customer->ciweb){{$customer->ciweb}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Email</label>
                                <div class="col-10">
                                    <input type="email" class="form-control" parsley-trigger="change" name="cemail" id="cemail" value="@isset($customer->ciemail){{$customer->ciemail}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cphone" id="cphone" value="@isset($customer->ciphone){{$customer->ciphone}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Fax</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cfax" id="cfax" value="@isset($customer->cifax){{$customer->cifax}}@endisset" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
