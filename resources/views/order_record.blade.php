@extends('layouts.app')
@section('title', 'Historial de ordenes')
@section('content')


<div class="container mx-auto px-4 py-8">
    @include('partials.form_dates', ['action' => route('orders.record')])
    @if(isset($api_error))
        @include('partials.error_view', ['error' => $api_error])
    @else
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex flex-row justify-between pb-4">
            <h2 id="title" class="text-2xl font-semibold"> Historial de órdenes</h2>
            <div>
                <label class="text-lg px-2" for="toggleSwitch">Mostrar grafica</label>
                <input type="checkbox" id="toggleSwitch">
            </div>
        </div>

    <section id="lista">
            <section>
                <h2 id="totalTitle" class="text-2xl font-bold text-gray-800 bg-gray-100 p-4 rounded-md shadow-md">
                    Total: <span id="converted_amount" class="text-green-600">${{ number_format($orders_total, 2) }}</span>
                </h2>
                <div class="mt-4 flex items-center">
                    <label for="currency_selector" class="text-gray-700 font-medium mr-2">Cambiar moneda:</label>
                    <select id="currency_selector" class="border border-gray-300 rounded-md p-2 bg-white shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        <option value="MXN">Pesos Mexicanos (MXN)</option>
                        <option value="USD">Dólares (USD)</option>
                        <option value="EUR">Euros (EUR)</option>
                        <option value="VES">Bolívares (VES)</option>
                    </select>
                </div>
            </section>

        <div class="overflow-x-auto">
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">orden ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ultima actualización</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($order_record as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ route('orders.detail', $order['id']) }}" class="text-indigo-600 hover:text-indigo-900">{{ $order['order_code'] }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order['phone'] }}</div>
                                <div class="text-sm text-gray-500">{{ $order['address'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order['street_name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $order['city'] }}, {{ $order['state'] }} - {{ $order['zip_code'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${{ number_format($order['subtotal'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                ${{ number_format($order['total'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($order['paid'] == "1")
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Pagado
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($order['last_update'])->format('d-m-Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="m-4">
            {{ $order_record->links() }}
        </div>
    </section>

    <section id="grafica" class="h-full">
        <canvas class="h-full" id="myChart"></canvas>
    </section>
    </div>

@endif
</div>
@endsection
@if(!isset($api_error))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        get_title_with_dates();
        toggle_list_and_chart('switch_order_record');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currency_selector = document.getElementById('currency_selector');
        
        
        
        let lastExecuted = 0; // Variable para llevar el control del último momento de ejecución

        currency_selector.addEventListener('change', function() {
            const from_currency = 'MXN'; 
            const to_currency = currency_selector.value;
            const qty = {{ $orders_total }};

            localStorage.setItem('selected_currency', to_currency);
            

            convertCurrency(from_currency, to_currency, qty);
        });
    });
    window.addEventListener('load', function() {
        
        const storedCurrency = localStorage.getItem('selected_currency');

        if (storedCurrency) {
            // Si hay una moneda seleccionada previamente, actualizamos el selector y hacemos la solicitud de conversión
            currency_selector.value = storedCurrency;
            const from_currency = 'MXN'; 
            const to_currency = storedCurrency;
            const qty = {{ $orders_total }};

            convertCurrency(from_currency, to_currency, qty);
        }
    });
    function convertCurrency(from_currency, to_currency, qty) {
        const amount_element = document.getElementById('converted_amount');
        amount_element.textContent = "Cargando...";
        fetch(`/convert-currency?from_currency=${from_currency}&to_currency=${to_currency}&qty=${qty}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const converted_amount = parseInt(data.converted_amount);
                    
                    // Actualiza el contenido con la cantidad convertida
                    amount_element.textContent = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: to_currency
                    }).format(converted_amount);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartInfo = @json($chart_info);
        const labels = chartInfo.labels;
        const data = chartInfo.data;

        // Umbral de frecuencia de datos
        const threshold = 5;

        // Contar las ocurrencias de cada estado
        const groupedOrders = {};
        for (let i = 0; i < data.length; i++) {
            if (groupedOrders[labels[i]]) {
                groupedOrders[labels[i]] += data[i];
            } else {
                groupedOrders[labels[i]] = data[i];
            }
        }

        // Filtrar y agrupar las categorías menos frecuentes en "Otros"
        const filteredLabels = [];
        const filteredData = [];
        let others = 0;
        for (const [state, count] of Object.entries(groupedOrders)) {
            if (count < threshold) {
                others += count;
            } else {
                filteredLabels.push(state);
                filteredData.push(count);
            }
        }

        // Agregar la categoría "Otros" si es necesario
        if (others > 0) {
            filteredLabels.push('Otros');
            filteredData.push(others);
        }

        // Asignar los valores filtrados a la gráfica
        chartInfo.labels = filteredLabels;
        chartInfo.data = filteredData;
                

        const ctx = document.getElementById('myChart').getContext('2d');
        
        // Convertir la data a números
            const numericData = filteredData.map(value => Number(value));

            // Obtener los índices de los valores máximo y mínimo
            const maxIndex = numericData.indexOf(Math.max(...numericData));
            const minIndex = numericData.indexOf(Math.min(...numericData));
        // Crear gradientes
        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
        gradientGreen.addColorStop(0, 'rgba(52, 211, 158, 0.5)');
        gradientGreen.addColorStop(1, 'rgba(16, 185, 129, 0.8)');

        const gradientGray = ctx.createLinearGradient(0, 0, 0, 400);
        gradientGray.addColorStop(0, 'rgba(75, 85, 99, 0.5)');
        gradientGray.addColorStop(1, 'rgba(31, 41, 55, 0.8)');

        const gradientYellow = ctx.createLinearGradient(0, 0, 0, 400);
        gradientYellow.addColorStop(0, 'rgba(8, 145, 178, 0.5)');
        gradientYellow.addColorStop(1, 'rgba(14, 116, 144, 0.8)');

        // Asignar colores a cada columna
        const backgroundColors = data.map((value, index) => {
            if (value === 0) {
                return 'rgba(200, 200, 200, 0.2)';
            } else if (index === maxIndex) {
                return gradientGreen;
            } else if (index === minIndex) {
                return gradientGray;
            } else {
                return gradientYellow;
            }
        });

        new Chart(ctx, {
    type: 'bar',
    data: {
        labels: filteredLabels,
        datasets: [{
            label: 'Ventas totales',
            data: filteredData,
            backgroundColor: backgroundColors,
            borderColor: 'rgba(0, 0, 0, 0.1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1500,
            easing: 'easeInOutQuart'
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5,
                    callback: function(value) {
                        return value + ' ordenes';
                    }
                },
                grid: {
                    color: '#ddd',
                    lineWidth: 1
                }
            },
            x: {
                ticks: {
                    autoSkip: true,  // Salta etiquetas si hay muchas
                    maxRotation: 90, // Rotar las etiquetas para que no se solapen
                    minRotation: 45   // Controla el ángulo de rotación
                },
                grid: {
                    color: '#ddd',
                    lineWidth: 1
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            title: {
                display: true,
                text: 'ordenes Totales por Estado',
                font: {
                    size: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return 'ordenes: ' + tooltipItem.raw;
                    }
                }
            }
        },
        // Agregar la barra de desplazamiento horizontal
        layout: {
            padding: {
                left: 50,
                right: 50,
                top: 10,
                bottom: 30
            }
        },
        plugins: {
            tooltip: {
                enabled: true
            }
        },
        elements: {
            bar: {
                borderWidth: 2
            }
        }
    }
});

    });

</script>
@endif