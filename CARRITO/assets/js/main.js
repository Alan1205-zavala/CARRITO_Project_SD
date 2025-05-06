$(document).ready(function() {
    const userId = typeof USER_ID !== 'undefined' ? USER_ID : null;
    const isLoggedIn = typeof IS_LOGGED_IN !== 'undefined' ? IS_LOGGED_IN : false;

    updateCartCount();

    $(document).on('click', '.add-to-cart', function() {
        const id_producto = $(this).data('id');
        
        $.post('api/carrito/add', {
            id_cliente: userId,
            id_producto: id_producto,
            cantidad: 1
        }, function(response) {
            updateCartCount();
            showToast('Producto agregado al carrito');
        }).fail(function(error) {
            showToast(error.responseJSON?.message || 'Error al agregar al carrito', 'danger');
        });
    });

    $('#cartButton').click(function(e) {
        e.preventDefault();
        
        if (!isLoggedIn) {
            showToast('Debes iniciar sesión para ver el carrito', 'warning');
            return;
        }
        
        $.get('api/carrito', {
            id_cliente: userId
        }, function(response) {
            renderCart(response);
            $('#cartModal').modal('show');
        }).fail(function() {
            showToast('Error al cargar el carrito', 'danger');
        });
    });

    // Eliminar item del carrito
    $(document).on('click', '.remove-item', function() {
        const id_producto = $(this).data('id');
        
        $.post('api/carrito/remove', {
            id_cliente: userId,
            id_producto: id_producto
        }, function(response) {
            renderCart(response);
            showToast('Producto eliminado del carrito');
        }).fail(function(error) {
            showToast(error.responseJSON?.message || 'Error al eliminar del carrito', 'danger');
        });
    });

    // Actualizar contador del carrito
    function updateCartCount() {
        if (!isLoggedIn) return;
        
        $.get('api/carrito', {
            id_cliente: userId
        }, function(response) {
            $('#cartCount').text(response.items_count || 0);
        });
    }

    // Renderizar carrito en el modal
    function renderCart(cart) {
        let html = '';
        
        if (cart.items && cart.items.length > 0) {
            cart.items.forEach(item => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6>${item.nombre}</h6>
                                <p>Cantidad: ${item.cantidad}</p>
                                <p>Precio unitario: $${item.precio_unitario.toFixed(2)}</p>
                            </div>
                            <div>
                                <p>Total: $${(item.precio_unitario * item.cantidad).toFixed(2)}</p>
                                <button class="btn btn-sm btn-outline-danger remove-item" 
                                        data-id="${item.id_producto}">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<p>Tu carrito está vacío</p>';
        }
        
        $('#cartItems').html(html);
        $('#cartTotal').text('$' + (cart.total?.toFixed(2) || '0.00'));
        
    // Configurar PayPal
    if (cart.items && cart.items.length > 0) {
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: cart.total.toFixed(2),
                            currency_code: 'MXN',
                            breakdown: {
                                item_total: {
                                    value: cart.total.toFixed(2),
                                    currency_code: 'MXN'
                                }
                            }
                        },
                        items: cart.items.map(item => ({
                            name: item.nombre,
                            unit_amount: {
                                value: item.precio_unitario.toFixed(2),
                                currency_code: 'MXN'
                            },
                            quantity: item.cantidad.toString(),
                            sku: item.id_producto.toString()
                        }))
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Enviar datos al servidor
                    $.post('api/carrito/checkout', {
                        id_cliente: userId,
                        payment_data: {
                            id_transaccion: details.id,
                            status: details.status,
                            email: details.payer.email_address,
                            amount: cart.total
                        }
                    }).done(function(response) {
                        showToast('¡Pago completado con éxito!', 'success');
                        $('#cartModal').modal('hide');
                        updateCartCount();
                    }).fail(function() {
                        showToast('Error al registrar el pago', 'danger');
                    });
                });
            },
            onError: function(err) {
                showToast('Error en el proceso de pago: ' + err, 'danger');
            }
        }).render('#paypal-button-container');
    }
}

    // Mostrar notificaciones toast
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        toast.on('hidden.bs.toast', function() {
            toast.remove();
        });
    }

    function renderCart(cart) {
        let html = '';
        
        if (cart.items && cart.items.length > 0) {
            cart.items.forEach(item => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6>${item.nombre}</h6>
                                <p>Cantidad: ${item.cantidad}</p>
                                <p>Precio unitario: $${item.precio_unitario.toFixed(2)}</p>
                            </div>
                            <div>
                                <p>Total: $${(item.precio_unitario * item.cantidad).toFixed(2)}</p>
                                <button class="btn btn-sm btn-outline-danger remove-item" 
                                        data-id="${item.id_producto}">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="alert alert-info">Tu carrito está vacío</div>';
        }
        
        $('#cartItems').html(html);
        $('#cartTotal').text('$' + (cart.total?.toFixed(2) || '0.00'));
        
        // Mostrar PayPal solo si hay items
        $('#paypal-button-container').toggle(cart.items && cart.items.length > 0);
    }
});