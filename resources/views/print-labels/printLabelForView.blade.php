<div class="label-row" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 20px;color:black;">
    <div class="label-item"
        style="text-align: center; font-size: 12px; line-height: 1.2; width: 45mm; height: 28mm; overflow: hidden; display: flex; flex-direction: column; justify-content: center; align-items: center; border: 1px solid #ddd; padding: 5px;">

        <div style="width: 100%; height: 5px;"></div>

        <strong style="font-size: 8px; display: block; font-weight: 100;color:black;">
            {{ $seller_name }}
        </strong>

        <img src="data:image/png;base64,{{ $barcode }}" alt="barcode"
            style="width: 60%; max-height: 8mm; object-fit: contain; height: 8mm; margin-top: 4px;" />

        <span style="font-size: 12px; display: block; margin-bottom: 2px;">{{ $barcode_label }}</span>

        <div style="width: 100%; text-align: center; padding-left: 5px;">
            <strong style="font-size: 10px; display: block;">{{ $item->name }}</strong>
        </div>

        <table style="width: 97%; font-size: 10px; margin: auto;">
            <tr>
                <td style="text-align: left; padding-right: 1mm; white-space: nowrap;">
                    <img src="{{ $currency_icon }}" alt="SAR"
                        style="width: 30px; height: 30px; vertical-align: middle;">
                    <strong style="font-size: 30px;">{{ $item->price ?? 0 }}</strong>
                </td>
                <td style="text-align: right; padding-left: 1mm; white-space: nowrap;">Include Vat</td>
            </tr>
        </table>
    </div>
</div>
