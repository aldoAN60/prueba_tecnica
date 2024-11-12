@extends('layouts.app')
@section('title','detalle de orden')
@section('content')

<div class="container mx-auto p-4">
    
    <section class="pb-2 flex justify-end flex-row">
        <a href="javascript:history.back()" class="ml-2  bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Regresar
        </a>
    </section>
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-2xl font-bold mb-4">Detalles de la Orden</h2>
        <div class="grid grid-cols-2 gap-4">
            <p><strong>ID de Orden:</strong> {{ $order_detail['id'] }}</p>
            <p><strong>Usuario ID:</strong> {{ $order_detail['user_id'] }}</p>
            <p><strong>Teléfono:</strong> {{ $order_detail['phone'] }}</p>
            <p><strong>Dirección:</strong> {{ $order_detail['address'] }}</p>
            <p><strong>Ciudad:</strong> {{ $order_detail['city'] }}</p>
            <p><strong>Estado:</strong> {{ $order_detail['state'] }}</p>
            <p><strong>Calle:</strong> {{ $order_detail['street_name'] }}</p>
            <p><strong>Código Postal:</strong> {{ $order_detail['zip_code'] }}</p>
            <p><strong>Descuento:</strong> ${{ number_format($order_detail['discount'], 2) }}</p>
            <p><strong>Subtotal:</strong> ${{ number_format($order_detail['subtotal'], 2) }}</p>
            <p><strong>Total:</strong> ${{ number_format($order_detail['total'], 2) }}</p>
            <p><strong>Código de Orden:</strong> {{ $order_detail['order_code'] }}</p>
            <p><strong>Pagada:</strong> {{ $order_detail['paid'] ? 'Sí' : 'No' }}</p>
            <p><strong>Habilitada:</strong> {{ $order_detail['enabled'] ? 'Sí' : 'No' }}</p>
            <p><strong>Fecha de Creación:</strong> {{ $order_detail['create_date'] }}</p>
            <p><strong>Última Actualización:</strong> {{ $order_detail['last_update'] }}</p>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4">Productos</h2>
    @foreach ($order_detail['products'] as $product)
        <div class="bg-gray-100 p-4 rounded-lg shadow-lg mb-4">
            <div class="flex">
                <img src="{{ $product['image_url'] }}" alt="{{ $product['title'] }}" class="w-32 h-32 object-contain mr-4">
                <div>
                    <h3 class="text-xl font-semibold">{{ $product['title'] }}</h3>
                    <p><strong>Categoría:</strong> {{ $product['category'] }}</p>
                    <p><strong>Descripción:</strong> {{ $product['short_description'] }}</p>
                    <p><strong>Precio:</strong> ${{ number_format($product['price'], 2) }}</p>
                    <p><strong>Cantidad:</strong> {{ $product['qty'] }}</p>
                    <p><strong>Total:</strong> ${{ number_format($product['total'], 2) }}</p>
                    <p><strong>Ventas:</strong> {{ $product['sale_count'] }}</p>
                    <p><strong>Descuento:</strong> ${{ number_format($product['discount'], 2) }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
    
@endsection