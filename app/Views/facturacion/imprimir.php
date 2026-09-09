<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= $factura['id_venta'] ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Librería HTML2PDF para generar PDF descargable directamente -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: 30px auto; padding: 30px; background: #fff; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
    </style>
</head>
<body>

<div class="container text-end my-3">
    <button onclick="descargarPDF();" class="btn btn-danger fw-bold">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
    </button>
    <button onclick="window.print();" class="btn btn-outline-secondary me-2">
        <i class="bi bi-printer-fill me-1"></i> Imprimir
    </button>
    <a href="<?= base_url('facturas/historial') ?>" class="btn btn-secondary">Volver al Historial</a>
</div>

<div class="invoice-box" id="factura-contenido">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Facturación App</h2>
            <small class="text-muted">Comprobante de Venta</small>
        </div>
        <div class="text-end">
            <h4 class="mb-0">Factura #<?= str_pad($factura['id_venta'], 5, '0', STR_PAD_LEFT) ?></h4>
            <small>Fecha: <?= date('d/m/Y H:i', strtotime($factura['fecha'])) ?></small>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-12">
            <h6 class="fw-bold">Datos del Cliente:</h6>
            <p class="mb-1"><strong>Nombre:</strong> <?= esc($factura['cliente_nombre']) ?></p>
            <p class="mb-0"><strong>Identificación / Cédula:</strong> <?= esc($factura['identificacion'] ?? 'N/A') ?></p>
        </div>
    </div>

    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th class="text-center" style="width: 100px;">Cantidad</th>
                <th class="text-end" style="width: 130px;">Precio U.</th>
                <th class="text-end" style="width: 130px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $item): ?>
                <tr>
                    <td><?= esc($item['producto_nombre']) ?></td>
                    <td class="text-center"><?= $item['cantidad'] ?></td>
                    <td class="text-end">$<?= number_format($item['precio_unitario'], 2) ?></td>
                    <td class="text-end">$<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        <div class="text-end" style="width: 250px;">
            <div class="d-flex justify-content-between border-top pt-2">
                <span class="fw-bold fs-5">Total:</span>
                <span class="fw-bold fs-5 text-success">$<?= number_format($factura['total'], 2) ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarPDF() {
        const elemento = document.getElementById('factura-contenido');
        const opciones = {
            margin:       10,
            filename:     'Factura_<?= str_pad($factura['id_venta'], 5, '0', STR_PAD_LEFT) ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opciones).from(elemento).save();
    }

    // Descarga automática limpia al cargar la página
    window.onload = function() {
        descargarPDF();
    };
</script>

</body>
</html>