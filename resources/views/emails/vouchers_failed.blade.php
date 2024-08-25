<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Fallidos</title>
</head>
<body>
    <h1>Comprobantes Fallidos</h1>
    <p>Hola {{ $user->name }},</p>
    <p>Los siguientes comprobantes no se pudieron registrar:</p>
    <ul>
        @foreach($failedVouchers as $voucher)
            <li>Error: {{ $voucher['error'] }}</li>
            <li>Contenido: <pre>{{ $voucher['content'] }}</pre></li>
        @endforeach
    </ul>
</body>
</html>
