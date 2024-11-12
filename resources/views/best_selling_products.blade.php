@extends('layouts.app')
@section('name','Productos mas vendidos')
@section('content')
    

<div class="container mx-auto p-4">
        
    <!-- Formulario para Filtros de Fecha -->
    @include('partials.form_dates', ['action' => route('products.filter')])

    <!-- Mensaje en caso de que no haya productos -->
    @if(isset($api_error))
        @include('partials.error_view', ['error' => $api_error])
    @else
    
    <!-- Tabla de Productos Más Vendidos -->
    <div>
        <div class="flex flex-row justify-between pb-4">
            <h2 id="title" class="text-2xl font-semibold"> Los 5 Productos Más Vendidos</h2>
            <div>
                <label class="text-lg px-2" for="toggleSwitch">Mostrar grafica</label>
                <input type="checkbox" id="toggleSwitch">
            </div>
        </div>

    </div>
    <section id="lista">
    <div class="max-w-7xl mx-auto p-6">
       
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <div class="bg-white border rounded-lg shadow-md p-4">
                    <!-- Imagen del producto con chip de categoría -->
                    <div class="relative">
                        <img src="{{ $product['image_url'] }}" alt="{{ $product['title'] }}" class="w-full h-64 object-contain rounded-t-lg">
                        <!-- Chip de categoría -->
                        <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-3 py-1 rounded-full">
                            {{ $product['category'] }}
                        </span>
                    </div>
    
                    <div class="mt-4">
                        <!-- Título del producto -->
                        <h2 class="text-xl font-semibold text-gray-800">{{ $product['title'] }}</h2>
                        
                        <!-- Descripción corta -->
                        <p class="text-gray-600 mt-2 text-sm">{{ $product['short_description'] }}</p>
    
                        <!-- Cantidad de ventas -->
                        <div class="mt-2">
                            <p class="text-gray-500 text-sm">Ventas: <span class="font-bold text-gray-700">{{ $product['sale_count'] }}</span></p>
                        </div>
    
                        <!-- Precio y descuento -->
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-xl font-bold text-green-600">{{ '$' . number_format($product['price'], 2) }}</p>
                            @if ($product['discount'] > 0)
                                <span class="text-red-500 line-through">{{ '$' . number_format($product['price'] + $product['discount'], 2) }}</span>
                            @endif
                        </div>
    
                        <!-- Botón para más detalles -->
                        <div class="mt-4">
                            <a href="/product/{{ $product['id'] }}" class="text-blue-500 hover:underline">Ver detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </section>

    <section id="grafica" class="h-full">
            <canvas class="h-full" id="myChart"></canvas>
    </section>
    @endif
</div>
@endsection
@if(!isset($api_error))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        get_title_with_dates();
        toggle_list_and_chart('switch_products');
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartInfo = @json($chart_info);
        const labels = chartInfo.labels;
        const data = chartInfo.data;

        

        const ctx = document.getElementById('myChart').getContext('2d');
        
        // Convertir la data a números
            const numericData = data.map(value => Number(value));

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
                labels: labels,
                datasets: [{
                    label: 'Ventas totales',
                    data: data,
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
                                return value + ' ventas';
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
                        text: 'Ventas Totales por Producto',
                        font: {
                            size: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Ventas: ' + tooltipItem.raw + ' unidades';
                            }
                        }
                    }
                }
            }
        });
    });

</script>
@endif
