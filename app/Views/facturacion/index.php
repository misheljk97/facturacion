<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Nueva Factura de Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Facturación</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header font-weight-bold">
            <i class="fas fa-file-invoice me-1"></i> Datos de la Factura
        </div>
        <div class="card-body">
            <!-- Selección de Cliente -->
            <div class="row mb-3">
                <div class="col-md-6 position-relative">
                    <label for="buscar_cliente" class="form-label">Cliente</label>
                    <input type="text" id="buscar_cliente" class="form-control" placeholder="Buscar por Nombre o Identificación..." autocomplete="off">
                    <div id="lista_clientes" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                    <input type="hidden" id="id_cliente">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cliente Seleccionado</label>
                    <input type="text" id="cliente_seleccionado" class="form-control" readonly placeholder="Ningún cliente seleccionado">
                </div>
            </div>

            <hr>

            <!-- Búsqueda de Productos -->
            <div class="row mb-3">
                <div class="col-md-8 position-relative">
                    <label for="buscar_producto" class="form-label">Agregar Producto</label>
                    <input type="text" id="buscar_producto" class="form-control" placeholder="Buscar por Nombre o Código de Barras..." autocomplete="off">
                    <div id="lista_productos" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                </div>
            </div>

            <!-- Tabla Detalle de Venta -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle" id="tabla_detalle">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th width="120px">Precio Unit.</th>
                            <th width="120px">Stock Disp.</th>
                            <th width="120px">Cantidad</th>
                            <th width="150px">Subtotal</th>
                            <th width="80px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Items dinámicos -->
                    </tbody>
                </table>
            </div>

            <!-- Resumen de Totales e Impuestos -->
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Subtotal:
                            <span id="txt_subtotal">$0.00</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            IVA (15%):
                            <span id="txt_iva">$0.00</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold bg-light">
                            Total:
                            <span id="txt_total">$0.00</span>
                        </li>
                    </ul>
                    <button class="btn btn-success w-100 mt-3" id="btn_guardar_factura">
                        <i class="fas fa-save me-1"></i> Procesar Factura
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let detalles = [];

document.addEventListener('DOMContentLoaded', () => {
    const inputCliente = document.getElementById('buscar_cliente');
    const inputProducto = document.getElementById('buscar_producto');

    // Buscar clientes en tiempo real
    inputCliente.addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
            document.getElementById('lista_clientes').innerHTML = '';
            return;
        }
        const res = await fetch(`<?= base_url('facturas/buscarClientes') ?>?q=${encodeURIComponent(query)}`);
        const data = await res.json();
        
        let html = '';
        data.forEach(c => {
            html += `<button type="button" class="list-group-item list-group-item-action" onclick="seleccionarCliente(${c.id_cliente}, '${c.nombre.replace(/'/g, "\\'")}')">
                        ${c.nombre} (${c.identificacion})
                     </button>`;
        });
        document.getElementById('lista_clientes').innerHTML = html;
    });

    // Buscar productos en tiempo real
    inputProducto.addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
            document.getElementById('lista_productos').innerHTML = '';
            return;
        }
        const res = await fetch(`<?= base_url('facturas/buscarProductos') ?>?q=${encodeURIComponent(query)}`);
        const data = await res.json();

        let html = '';
        data.forEach(p => {
            html += `<button type="button" class="list-group-item list-group-item-action" onclick='agregarProducto(${JSON.stringify(p)})'>
                        ${p.nombre} - $${p.precio_venta} (Stock: ${p.stock})
                     </button>`;
        });
        document.getElementById('lista_productos').innerHTML = html;
    });

    // Ocultar listas desplegables al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!inputCliente.contains(e.target)) {
            document.getElementById('lista_clientes').innerHTML = '';
        }
        if (!inputProducto.contains(e.target)) {
            document.getElementById('lista_productos').innerHTML = '';
        }
    });

    // Procesar factura
    document.getElementById('btn_guardar_factura').addEventListener('click', guardarFactura);
});

function seleccionarCliente(id, nombre) {
    document.getElementById('id_cliente').value = id;
    document.getElementById('cliente_seleccionado').value = nombre;
    document.getElementById('lista_clientes').innerHTML = '';
    document.getElementById('buscar_cliente').value = '';
}

function agregarProducto(p) {
    document.getElementById('lista_productos').innerHTML = '';
    document.getElementById('buscar_producto').value = '';

    const existe = detalles.find(item => item.id_producto == p.id_producto);
    if (existe) {
        if (existe.cantidad + 1 > p.stock) {
            alert('No hay suficiente stock disponible.');
            return;
        }
        existe.cantidad += 1;
        existe.subtotal = existe.cantidad * existe.precio_unitario;
    } else {
        detalles.push({
            id_producto: p.id_producto,
            nombre: p.nombre,
            precio_unitario: parseFloat(p.precio_venta),
            stock_max: parseInt(p.stock),
            cantidad: 1,
            subtotal: parseFloat(p.precio_venta)
        });
    }
    renderTabla();
}

function cambiarCantidad(idProducto, nuevaCantidad) {
    const item = detalles.find(i => i.id_producto == idProducto);
    if (item) {
        const cant = parseInt(nuevaCantidad) || 1;
        if (cant > item.stock_max) {
            alert(`Stock máximo disponible: ${item.stock_max}`);
            renderTabla();
            return;
        }
        item.cantidad = cant;
        item.subtotal = item.cantidad * item.precio_unitario;
        renderTabla();
    }
}

function eliminarProducto(idProducto) {
    detalles = detalles.filter(i => i.id_producto != idProducto);
    renderTabla();
}

function renderTabla() {
    const tbody = document.querySelector('#tabla_detalle tbody');
    tbody.innerHTML = '';

    let subtotalGen = 0;

    detalles.forEach(item => {
        subtotalGen += item.subtotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.nombre}</td>
            <td>$${item.precio_unitario.toFixed(2)}</td>
            <td><span class="badge bg-secondary">${item.stock_max}</span></td>
            <td>
                <input type="number" class="form-control form-control-sm" value="${item.cantidad}" min="1" max="${item.stock_max}" onchange="cambiarCantidad(${item.id_producto}, this.value)">
            </td>
            <td>$${item.subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${item.id_producto})"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    const iva = subtotalGen * 0.15;
    const total = subtotalGen + iva;

    document.getElementById('txt_subtotal').innerText = `$${subtotalGen.toFixed(2)}`;
    document.getElementById('txt_iva').innerText = `$${iva.toFixed(2)}`;
    document.getElementById('txt_total').innerText = `$${total.toFixed(2)}`;
}

async function guardarFactura() {
    const idCliente = document.getElementById('id_cliente').value;
    if (!idCliente) {
        alert('Debe seleccionar un cliente.');
        return;
    }
    if (detalles.length === 0) {
        alert('Debe agregar al menos un producto.');
        return;
    }

    const subtotal = detalles.reduce((acc, i) => acc + i.subtotal, 0);
    const total = subtotal + (subtotal * 0.15);

    const payload = {
        id_cliente: idCliente,
        total: total,
        detalles: detalles
    };

    const res = await fetch('<?= base_url('facturas/guardar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    });

    const result = await res.json();
    if (result.success) {
        alert(result.message);
        window.location.reload();
    } else {
        alert('Error: ' + result.message);
    }
}
</script>
<?= $this->endSection() ?>