<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        @media print {
            body {
                width: 60mm;
                margin: 10px;
                font-family: 'Courier New', monospace;
                font-size: 12px;
            }
        }

        body {
            width: 70mm;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .struk {
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .item-name {
            word-break: break-word;
        }
    </style>
</head>

<body>
    <div class="struk">
        <div class="center bold">
            {{ $profil->nama }}<br>
            Jl. {{ $profil->alamat }}<br>
            Telp: {{ $profil->telpon }}
        </div>
        <div class="line"></div>
        <table>
            <tr>
                <td>Tanggal</td>
                <td class="right">{{ $penjualan->tanggal }}</td>
            </tr>
            <tr>
                <td>Pembeli</td>
                <td class="right">{{ $penjualan->pembeli }}</td>
            </tr>
        </table>
        <div class="line"></div>
        <table>
            <tr>
                <th class="item-name">Item</th>
                <th class="right">Qty</th>
                <th class="right">Harga</th>
                <th class="right">Total</th>
            </tr>
            @foreach ($penjualan->items as $item)
                @if ($item->item_type == 'kuliner')
                    <tr>
                        <td class="item-name">{{ $item->kuliner->nama }}</td>
                        <td class="right">{{ $item->jumlah }}</td>
                        <td class="right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @elseif ($item->item_type == 'panen')
                    <tr>
                        <td class="item-name">{{ $item->panen->bibit->nama }}</td>
                        <td class="right">{{ $item->jumlah }}</td>
                        <td class="right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <div class="line"></div>
        <table>
            <tr>
                <td class="bold">Total Bayar</td>
                <td class="right bold">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="line"></div>
        <div class="center">
            "Terima kasih telah berbelanja di <b>{{ env('APP_NAME') }}</b>"
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
