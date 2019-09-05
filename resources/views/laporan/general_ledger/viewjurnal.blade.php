<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>Jurnal ID</th>
            <th>Transaction Date</th>
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Position</th>
            <th>Amount</th>
            <th>Notes</th>
            <th>Description</th>
        </thead>
        <tbody>
            @csrf
            @foreach ($jurnals as $jurnal)
                <tr>
                    <td>{{$jurnal->id_jurnal}}</td>
                    <td>{{ Carbon::parse($jurnal->date)->format('d M Y')}}</td>
                    <td>{{$jurnal->AccNo}}</td>
                    <td>{{$jurnal->coa->AccName}}</td>
                    <td>{{$jurnal->AccPos}}</td>
                    <td>{{number_format($jurnal->Amount)}}</td>
                    <td>{{$jurnal->description}}</td>
                    <td>{{$jurnal->notes_item}}</td>
                </tr>
            @php($i++)
        </tbody>
    </table>
</div>