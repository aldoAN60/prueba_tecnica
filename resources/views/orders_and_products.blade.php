@extends('layouts.app')
@section('title', 'Productos - ordenes')

@section('content')
    @include('partials.form_dates', ['action' => route('orders.filter')])
    <div class="container mx-auto py-6 space-y-6">
        @if(isset($api_error))
        @include('partials.error_view', ['error' => $api_error])
        @else
            <div class="flex flex-row justify-between pb-4">
                <h2 id="title" class="text-2xl font-semibold" >Productos con Órdenes Asociadas</h2>
                <div>
                    <label class="text-lg px-2" for="toggleSwitch">Mostrar grafica</label>
                    <input type="checkbox" id="toggleSwitch">
                </div>
            </div>
            <section id="lista">
                @foreach ($products as $index => $product)
                    <div class="h-72 bg-gradient-to-b from-white to-white/90 dark:from-white/90 dark:to-white shadow-md rounded-lg overflow-hidden flex">
                        <!-- Imagen del producto -->
                        <div class="relative w-80 h-64">
                            <img src="{{ $product['image_url'] }}" alt="{{ $product['title'] }}" class="w-full h-full object-contain hover:scale-105 transition-transform duration-300">
                        </div>
                        <!-- Contenido de la tarjeta -->
                        <div class="w-2/3 p-6 flex flex-col bg-white">
                            <div class="flex items-start gap-2 mb-4">
                                <div>
                                    <h2 class="text-2xl mb-2">{{ $product['title'] }}</h2>
                                    <p class="text-base text-gray-500">{{ $product['short_description'] }}</p>
                                </div>
                            </div>

                            <!-- Estadísticas de órdenes -->
                            <div class="grid grid-cols-2 gap-4 mb-2">
                                <div class=" bg-gradient-to-r from-slate-400 to-slate-300 rounded-lg p-4 text-center">
                                    <p class="text-md text-muted-foreground mb-1">Órdenes Totales</p>
                                    <p class="text-2xl font-bold">{{ count($product['orders']) }}</p>
                                </div>
                                <div class="bg-gradient-to-r from-slate-400 to-slate-300 rounded-lg p-4 text-center">
                                    <p class="text-md text-muted-foreground mb-1">Ingresos Totales ($)</p>
                                    <p class="text-2xl font-bold">${{ number_format(array_sum(array_column($product['orders'], 'total')), 2) }}</p>
                                </div>
                            </div>
                            <!-- Botón para abrir el modal -->
                            <button id="openModalBtn-{{$index}}" data-index="{{$index}}"class=" open-modal-btn flex flex-row items-center w-full text-left text-sm font-medium text-gray-600 hover:text-gray-800 focus:outline-none">
                                <span class="ml-2">
                                    <!-- Icono del botón -->
                                    <svg width="28px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.9844 10C21.9473 8.68893 21.8226 7.85305 21.4026 7.13974C20.8052 6.12523 19.7294 5.56066 17.5777 4.43152L15.5777 3.38197C13.8221 2.46066 12.9443 2 12 2C11.0557 2 10.1779 2.46066 8.42229 3.38197L6.42229 4.43152C4.27063 5.56066 3.19479 6.12523 2.5974 7.13974C2 8.15425 2 9.41667 2 11.9415V12.0585C2 14.5833 2 15.8458 2.5974 16.8603C3.19479 17.8748 4.27063 18.4393 6.42229 19.5685L8.42229 20.618C10.1779 21.5393 11.0557 22 12 22C12.9443 22 13.8221 21.5393 15.5777 20.618L17.5777 19.5685C19.7294 18.4393 20.8052 17.8748 21.4026 16.8603C21.8226 16.1469 21.9473 15.3111 21.9844 14" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                                        <path d="M21 7.5L17 9.5M12 12L3 7.5M12 12V21.5M12 12C12 12 14.7426 10.6287 16.5 9.75C16.6953 9.65237 17 9.5 17 9.5M17 9.5V13M17 9.5L7.5 4.5" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                                    </svg>
                                </span>
                                <p class="px-2">Ver Órdenes Asociadas</p>
                            </button>
                        </div>
                    </div>
                    <!-- Modal único por producto -->
                    <div id="modal-{{$index}}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        @include('modal-info',[
                            'orders' => $product['orders'],
                            'productIndex' => $index
                            ])
                    </div>
                @endforeach
            </section >

            {{-- grafica --}}
            <section id="grafica" class="h-full">
                    <canvas id="myChart"></canvas>
            </section>
        @endif
    </div>
@endsection
@if(!isset($api_error))
<script>

    document.addEventListener("DOMContentLoaded", function() {
        get_title_with_dates();
        toggle_list_and_chart('switch_orders');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Iterar sobre los productos para configurar eventos de modales

        @foreach ($products as $productIndex => $product)
            const modalContainer{{$productIndex}} = document.getElementById('modal-{{$productIndex}}');
            const closeModalBtn{{$productIndex}} = document.getElementById('closeModalBtn-{{$productIndex}}');
            // Abrir el modal de órdenes al hacer clic en el botón (asumiendo un botón de apertura)
            const openModalBtn{{$productIndex}} = document.getElementById('openModalBtn-{{$productIndex}}');
            if (openModalBtn{{$productIndex}}) {
                openModalBtn{{$productIndex}}.addEventListener('click', function() {
                    modalContainer{{$productIndex}}.classList.remove('hidden');
                });
            }

            // Cerrar el modal principal al hacer clic en el botón de cierre
            if (closeModalBtn{{$productIndex}}) {
                closeModalBtn{{$productIndex}}.addEventListener('click', function() {
                    modalContainer{{$productIndex}}.classList.add('hidden');
                });
            }
        @endforeach
    });
</script>
<script>

    document.addEventListener("DOMContentLoaded", function() {
        // Pasar la variable PHP a JavaScript
        const chartInfo = @json($chart_info);

        // Acceder a las propiedades 'labels' y 'data'
        const labels = chartInfo.labels;
        const data = chartInfo.data;

        // Asegúrate de obtener el contexto 2d
        const ctx = document.getElementById('myChart');
        
        // Encontrar el índice del producto con más y menos órdenes
    // Filtrar los datos para excluir los productos con valor 0
    const nonZeroData = data.filter(value => value > 0);

        // Encontrar el índice del producto con más y menos órdenes excluyendo los ceros
        const maxValue = Math.max(...nonZeroData);
        const minValue = Math.min(...nonZeroData);

        const maxIndex = data.indexOf(maxValue);
        const minIndex = data.indexOf(minValue);

        // Asignar colores dinámicos a cada columna
        const backgroundColors = data.map((value, index) => {
            if (value === 0) {
                // No asignar un color especial para productos con 0 órdenes
                return 'rgba(200, 200, 200, 0.2)'; // Color neutro para representar 0 si se quiere mostrar
            } else if (index === maxIndex) {
                // Gradiente verde para el producto con más órdenes
                const gradientGreen = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradientGreen.addColorStop(0, 'rgba(52, 211, 158, 0.5)');
                gradientGreen.addColorStop(1, 'rgba(16, 185, 129, 0.8)');
                return gradientGreen;
            } else if (index === minIndex) {
                // Gradiente gris para el producto con menos órdenes
                const gradientGray = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradientGray.addColorStop(0, 'rgba(75, 85, 99, 0.5)');
                gradientGray.addColorStop(1, 'rgba(31, 41, 55, 0.8)');
                return gradientGray;
            } else {
                // Gradiente amarillo para los productos restantes
                const gradientYellow = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradientYellow.addColorStop(0, 'rgba(8, 145, 178, 0.5)');
                gradientYellow.addColorStop(1, 'rgba(14, 116, 144, 0.8)');
                return gradientYellow;
            }
        });

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ordenes relacionadas',
                    data: data,
                    borderWidth: 1,
                    backgroundColor: backgroundColors,
                    borderColor: 'rgba(0, 0, 0, 0.1)'
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
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Ordenes Totales por Producto',
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
                }
            }
        });
    });

  </script>

@endif