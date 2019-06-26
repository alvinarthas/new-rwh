@php
    use App\BankMember;
    $i=1;
@endphp
<div id="load" class="table-responsive">
    <table class="table m-b-0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Gambar KTP</th>
                <th>Gambar Tabungan</th>
                <th>Gambar ATM</th>
                <th>Status Rekening</th>
                <th>Status Cetak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datas as $data)
            <tr>
                <td>{{$i}}</td>
                <td>{{$data->nama}}</td>
                {{-- Gambar KTP --}}
                @if ($data->scanktp == "noimage.jpg")
                    <td>Empty</td>
                @else
                    <td>Have Filled</td>
                @endif
                {{-- Bank --}}
                @php($tabungan = BankMember::getData($data->ktp))

                @if($tabungan <> NULL)
                    @if($tabungan->scantabungan == "noimage.jpg")
                        <td>Empty</td>
                    @else
                        <td>Have Filled</td>
                    @endif
                    @if($tabungan->scanatm == "noimage.jpg")
                        <td>Empty</td>
                    @else
                        <td>Have Filled</td>
                    @endif

                    <td>Aktif</td>
                @else
                    <td>No Primary</td>
                    <td>No Primary</td>
                    <td>Tidak Aktif</td>
                @endif
                @if($data->cetak == 0)
                    <td>Belum dicetak</td>
                @else
                    <td>Sudah Dicetak</td>
                @endif
                @php($i++)
            </tr>
            @endforeach
            
        </tbody>
    </table>
    {{ $datas->links() }} 
</div>