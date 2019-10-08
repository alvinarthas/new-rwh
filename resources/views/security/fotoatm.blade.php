@php
    // $asset = "asset('assets/images/member/atm/".$data['scanatm']."')";
    $asset = "assets/images/member/atm/".$data['scanatm'];
@endphp
<a href="{{ asset($asset) }}" class="image-popup" title="{{ $data['noatm'] }}">
    <img src="{{ asset($asset) }}" alt="no-image" title="{{ $data['noatm'] }}" id="img" class="img-thumbnail img-responsive photo">
</a>;

<script>
    $(document).ready(function () {
        $('.image-popup').magnificPopup({
            type: 'image',
        });

    });
</script>
