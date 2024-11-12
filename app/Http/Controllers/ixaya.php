<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ixaya_api;
use Illuminate\Pagination\LengthAwarePaginator;
class ixaya extends Controller
{

    /**
     * Muestra los productos más vendidos de la historia y prepara los datos
     * necesarios para su visualización en una gráfica.
     *
     * @return \Illuminate\View\View Retorna la vista 'best_selling_products' con la
     *         información de los productos y los datos de la gráfica.
     */
    public function index() {
        $response = new ixaya_api();
        $result = $response->historical_best_selling_products();
    
        if ($result['status'] == 0) {
            // Enviar un error a la vista sin redirección
            return view('best_selling_products')->withErrors(['api_error' => $result['message']]);
        }
        $chart_info = [];
        
        // Extraer solo los títulos y la cantidad de órdenes
        $chart_info['labels'] = array_map(function($product) {
            return $product['title'];  // Título del producto
        }, $result['data']);

        // Obtener la cantidad de órdenes para cada producto
        $chart_info['data'] = array_map(function($product) {
            return$product['sale_count'];  // Contamos la cantidad de órdenes asociadas al producto
        }, $result['data']);


        $chart_info = [
            "labels" => $chart_info['labels'],
            "data" => $chart_info['data']
        ];

        $data = [
            'products' => $result['data'],
            'chart_info' => $chart_info
        ];
        
        return view('best_selling_products', $data);
    }
    /**
     * Muestra los productos más vendidos dentro de un rango de fechas específico,
     * y prepara los datos necesarios para la visualización en una gráfica.
     *
     * @param \Illuminate\Http\Request $request El objeto de solicitud que contiene
     *        los parámetros de fecha enviados por el usuario.
     * @return \Illuminate\View\View Retorna la vista 'best_selling_products' con la
     *         información de los productos y los datos de la gráfica.
     */
    public function best_selling_products_by_date(Request $request){
        
        $response = new ixaya_api();
        $result = $response->best_selling_products_by_date($request->all());
    
        if ($result['status'] == 0) {
            return view('best_selling_products')->with(['api_error' => $result['message']]);
        }
        if (empty($result['data'])) {
            $data = [
                'products' => [],
                'message' => 'No se encontraron productos.',
            ];
            return view('best_selling_products', $data);
        }
    
        $chart_info = [];
        
        $chart_info['labels'] = array_map(function($product) {
            return $product['title'];
        }, $result['data']);

        $chart_info['data'] = array_map(function($product) {
            return$product['sale_count'];  
        }, $result['data']);


        $chart_info = [
            "labels" => $chart_info['labels'],
            "data" => $chart_info['data']
        ];

        $data = [
            'products' => $result['data'],
            'chart_info' => $chart_info
        ];
        
        return view('best_selling_products', $data);
    }

    /**
     * Muestra los productos con sus respectivas órdenes, y prepara los datos
     * necesarios para visualizarlos en una gráfica.
     *
     * @param \Illuminate\Http\Request $request El objeto de solicitud que contiene
     *        los parámetros enviados por el usuario para obtener los productos con órdenes.
     * @return \Illuminate\View\View Retorna la vista 'orders_and_products' con la
     *         información de los productos y los datos de la gráfica.
     */
    public function show_products_with_orders(Request $request) {
        $response = new ixaya_api();
        $result = $response->get_products_with_orders($request->all());
        
        if ($result['status'] == 0) {
            return view('orders_and_products')->with('api_error', $result['message']);
        }

        
        $chart_info = [];
        
        // Extraer solo los títulos y la cantidad de órdenes
        $chart_info['labels'] = array_map(function($product) {
            return $product['title'];  // Título del producto
        }, $result['data']);

        // Obtener la cantidad de órdenes para cada producto
        $chart_info['data'] = array_map(function($product) {
            return count($product['orders']);  // Contamos la cantidad de órdenes asociadas al producto
        }, $result['data']);


        $chart_info = [
            "labels" => $chart_info['labels'],
            "data" => $chart_info['data']
        ];

        $data = [
            'products' => $result['data'],
            'chart_info' => $chart_info,
        ];
        return view('orders_and_products', $data);
    }
    /**
     * Muestra el registro de órdenes, calcula el total de las órdenes y prepara los
     * datos para mostrar estadísticas y gráficos sobre los estados de las órdenes.
     *
     * @param \Illuminate\Http\Request $request El objeto de solicitud que contiene
     *        los parámetros enviados por el usuario para filtrar las órdenes por fecha.
     * @return \Illuminate\View\View Retorna la vista 'order_record' con la información
     *         de las órdenes, su total y las estadísticas de los estados.
     */
    public function orders_record(Request $request) {
        $response = new ixaya_api();
        $result = $response->orders_record($request->all());
    
        if ($result['status'] == 0) {
            return view('order_record')->with(['api_error' => $result['message']]);
        }
    
        $orders = $result['data'];
        $orders_total = array_sum(array_map(function($order){
            return $order['total'];
        },$orders));

        $chart_info = [];


        $states = array_map(function($order) {
            return $order['state'];
        }, array: $orders);


        $stateCounts = array_count_values($states);


        $chart_info['labels'] = array_keys($stateCounts); 
        $chart_info['data'] = array_values($stateCounts);


        $chart_info = [
            "labels" => $chart_info['labels'],
            "data" => $chart_info['data']
        ];
        $ordersCollection = collect($orders);
    
        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $ordersCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        
        $url = $request->fullUrlWithQuery([
            'page' => $currentPage,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ]);

        $paginatedOrders = new LengthAwarePaginator(
            $currentPageItems, 
            $ordersCollection->count(), 
            $perPage, 
            $currentPage, 
            ['path' => $url]
        );
    
        // Pasar la variable paginada a la vista
        $data = [
            'order_record' => $paginatedOrders,
            'orders_total' => $orders_total,
            'chart_info' => $chart_info,
        ];
    
        return view('order_record', $data);
    }
    /**
     * Realiza la conversión de divisas entre dos monedas utilizando una API externa.
     * Valida los parámetros de entrada y devuelve el resultado de la conversión.
     *
     * @param \Illuminate\Http\Request $request La solicitud del usuario con parámetros
     *        para realizar la conversión de divisas.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con el resultado
     *         de la conversión o un mensaje de error.
     */
    public function currency_convert(Request $request){
        $validated = $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3',
            'qty' => 'required|numeric|min:0',
        ]);
        $from_currency = $validated['from_currency'];
        $to_currency = $validated['to_currency'];
        $qty = $validated['qty'];
        
        $response = new ixaya_api();
        $result = $response->currency_convert($from_currency,$to_currency,$qty);
        if (isset($result['status']) && $result['status'] === 0) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400); // Código 400 para errores de solicitud
        }

        return response()->json([
            'status' => 'success',
            'converted_amount' => $result
        ]);
    }
    
    /**
     * Muestra los detalles de una orden específica.
     * 
     * Esta función consulta la API externa para obtener los detalles de una orden
     * usando el ID de la orden proporcionado.
     *
     * @param  int  $id  El ID de la orden cuyo detalle se desea obtener.
     * @return \Illuminate\View\View  La vista 'order-detail' con los detalles de la orden o un mensaje de error.
     */
    public function order_detail($id)
    {
            $response = new ixaya_api();
            $result = $response->order_detail($id);
    
            if ($result['status'] == 0) {
                return view('order-detail')->withErrors(['api_error' => $result['message']]);
            }
            $data = [
                'order_detail' => $result['data'],
            ];
            return view('order-detail', $data);
    }
    

    
    
    
    
    
}
