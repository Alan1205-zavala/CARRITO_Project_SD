$(document).ready(function() {
    // Obtener el ID de cliente de la variable de sesión PHP
    const userId = typeof USER_ID !== 'undefined' ? USER_ID : null;
    const isLoggedIn = typeof IS_LOGGED_IN !== 'undefined' ? IS_LOGGED_IN : false;

    // Cargar contador del carrito al iniciar
    updateCartCount();

    // Manejar clic en "Agregar al carrito"
    $(document).on('click', '.add-to-cart', function() {
        const id_producto = $(this).data('id');
        
        if (!isLoggedIn) {
            showToast('Debes iniciar sesión para agregar productos al carrito', 'warning');
            return;
        }
        
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

    // Mostrar carrito
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
        
        // Configurar PayPal si hay items
        if (cart.items && cart.items.length > 0) {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: cart.total.toFixed(2),
                                currency_code: 'USD',
                                breakdown: {
                                    item_total: {
                                        value: cart.total.toFixed(2),
                                        currency_code: 'USD'
                                    }
                                }
                            },
                            items: cart.items.map(item => ({
                                name: item.nombre,
                                unit_amount: {
                                    value: item.precio_unitario.toFixed(2),
                                    currency_code: 'USD'
                                },
                                quantity: item.cantidad.toString()
                            }))
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        $.post('api/carrito/checkout', {
                            id_cliente: userId,
                            payment_data: {
                                id_transaccion: details.id,
                                status: details.status,
                                email: details.payer.email_address
                            }
                        }, function(response) {
                            if (response.success) {
                                showToast('Compra realizada con éxito!', 'success');
                                $('#cartModal').modal('hide');
                                updateCartCount();
                            } else {
                                showToast('Error al procesar el pago', 'danger');
                            }
                        }).fail(function() {
                            showToast('Error al procesar el pago', 'danger');
                        });
                    });
                },
                onError: function(err) {
                    console.error('Error en PayPal:', err);
                    showToast('Error al procesar el pago con PayPal', 'danger');
                }
            }).render('#paypal-button-container');
        } else {
            $('#paypal-button-container').empty();
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
});