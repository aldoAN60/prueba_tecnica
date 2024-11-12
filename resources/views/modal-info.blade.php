<div class="bg-white p-6 rounded-lg shadow-lg w-96">
    <h3 class="text-lg font-medium text-gray-800">Órdenes Asociadas</h3>
    <div class="text-sm text-gray-600">
        @if(empty($orders))
            <h2>No hay nada para mostrar</h2>
        @else
        
        <div class="overflow-x-auto max-h-96">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-2"></th>
                        <th scope="col" class="px-4 py-2">Código de Orden</th>
                        <th scope="col" class="px-4 py-2">Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $index => $order)
                        <tr class="bg-white border-b">
                            <td class="px-4 py-2">{{ $index+1 }}</td>
                            <td class="px-4 py-2 text-cyan-600"> 
                                <a href="{{ route('orders.detail', $order['id']) }}">
                                    {{ $order['order_code'] }}
                                </a>
                            </td>
                            <td class="px-4 py-2">${{ number_format($order['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @endif
    </div>
    <button id="closeModalBtn-{{$productIndex}}"  class="close-modal-btn mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Cerrar</button>
</div>