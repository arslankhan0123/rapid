<div
    style="text-align: center; font-size: 12px; line-height: 1.2; width: 38mm; height: 28mm; overflow: hidden; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div style="width: 100%;height:5px;"></div>
    <strong style="font-size: 8px; display: block; font-weight: 100;">
        {{ $seller_name }}
    </strong><br>
    <img src="data:image/png;base64,{{ $barcode }}" alt="barcode"
        style="width: 60%; max-height: 8mm; object-fit: contain; height: 8mm;margin-top:4px;" />
    <br>
    <span style="font-size: 12px; display: block; margin-bottom: 2px;">{{ $barcode_label }}</span><br>
    <div style="width: 100%;text-align: center;padding-left: 5px;">
        <strong style="font-size: 10px; display: block;">{{ $item->name }}</strong><br>
    </div>

    <table style="width: 97%; font-size: 10px;margin:auto; ">
        <tr>
            <td style="text-align: left; padding-right: 1mm; white-space: nowrap;">
                <img src="{{ $currency_icon }}" alt="SAR"
                    style="width: 30px; height: 30px; vertical-align: middle;">
                <strong style="font-size: 30px;"> {{ $item->price??0 }}</strong>
            </td>
            <td style="text-align: right; padding-left: 1mm; white-space: nowrap;">Include Vat</td>
        </tr>
    </table>

</div>
