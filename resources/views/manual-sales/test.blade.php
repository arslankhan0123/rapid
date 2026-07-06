@if ($qr_code)
    <img style="width: 450px;height:450px;"  src="{{ $qr_code }}" alt="QR Code" />
@else
    <p>No QR code generated.</p>
@endif
