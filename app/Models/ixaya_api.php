<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Request;

class ixaya_api extends Model
{
    protected $url = "https://sandbox.ixaya.net/api/";

    /**
     * Realiza una llamada HTTP GET a la API externa.
     * 
     * Esta función realiza una solicitud HTTP GET a la API de IXAYA utilizando un token de autenticación.
     *
     * @param  string  $api_rest  La ruta de la API a la que se hará la solicitud.
     * @return array  La respuesta de la API en formato JSON o una estructura de error si la solicitud falla.
     */
    private function api_call($api_rest){
        $token = env('IXAYA_TOKEN');  
        $api_response = Http::withHeaders([
            'X-API-KEY' => $token 
        ])->get($this->url . $api_rest);  
    
        if ($api_response->successful()) {
            return $api_response->json();  
        } else {
            // Si hubo un error, manejarlo
            $api_response = [
                'status' => 0,
                'message' => 'Could not connect to the server. Please try again later.',
                'response' => "error",
            ];
            return $api_response;
        }
    }
    protected  $currenty_url = "https://currency-converter5.p.rapidapi.com/currency/convert";
    /**
     * Realiza una llamada a la API de conversión de divisas.
     * 
     * Esta función hace una solicitud HTTP GET a la API de conversión de divisas, proporcionando los parámetros necesarios
     * para convertir una cantidad de una moneda a otra.
     * 
     * @param  string  $from_currency  La moneda de origen en formato ISO (por ejemplo, 'USD').
     * @param  string  $to_currency    La moneda de destino en formato ISO (por ejemplo, 'EUR').
     * @param  float   $qty            La cantidad a convertir.
     * 
     * @return \Illuminate\Http\Client\Response  La respuesta de la API de conversión de divisas.
     */
    public function currency_api($from_currency,$to_currency,$qty){
        $token = env('CURRENCY_TOKEN');  

        $url = $this->currenty_url; // Asegúrate de que $this->currenty_url contenga la URL base

        $api_response = Http::withHeaders([
            'X-RapidAPI-Key' => $token
        ])->get($url, [
            'format' => 'json',
            'from' => $from_currency,
            'to' => $to_currency,
            'amount' => $qty,
            'language' => 'es'
        ]);

            return $api_response;
    }
    /**
     * Convierte una cantidad de una moneda a otra utilizando una API externa.
     * 
     * Esta función llama a la API de conversión de divisas para obtener la tasa de cambio y convertir una cantidad de 
     * dinero de una moneda a otra.
     * 
     * @param  string  $from_currency  La moneda de origen en formato ISO (por ejemplo, 'USD').
     * @param  string  $to_currency    La moneda de destino en formato ISO (por ejemplo, 'EUR').
     * @param  float   $qty            La cantidad a convertir.
     * 
     * @return mixed  El resultado de la conversión o un array con el estado del error.
     * 
     * @throws \Exception Si hay un problema con la llamada a la API o la conversión.
     */
    public function currency_convert($from_currency,$to_currency,$qty){
        
        $convert_results = $this->currency_api($from_currency,$to_currency,$qty);
        if($convert_results['status'] != "success"){
            return ['status' => 0, 'message' => $convert_results['error']['message']];
        }
        return $convert_results['rates'][$to_currency]['rate_for_amount'];
    }

    /**
     * Obtiene los 5 productos más vendidos históricamente.
     * 
     * Esta función hace una llamada a la API para obtener una lista de productos, luego ordena esos productos
     * en función de su cantidad de ventas (`sale_count`) y retorna los 5 productos más vendidos.
     * 
     * @return array Un array con el estado de la operación y los 5 productos más vendidos o un mensaje de error.
     * 
     */
    public function historical_best_selling_products(){
        $products = $this->api_call('products');
        if ($products['status'] == 0) {
            return ['status' => 0, 'message' => $products['message']];
        }
        $products = $products['response'];

        usort($products,function($a,$b){
            return $b['sale_count'] - $a['sale_count'];
        });
        $top_5_products = array_slice($products, 0, 5);
        return ['status' => 1, 'data' => $top_5_products];
    }
    
    /**
     * Obtiene los 5 productos más vendidos en un rango de fechas especificado.
     * 
     * La función realiza las siguientes tareas:
     * - Consume una API para obtener el registro de órdenes y productos.
     * - Filtra las órdenes por un rango de fechas.
     * - Calcula la cantidad total de ventas por producto.
     * - Ordena los productos por el número de ventas y devuelve los 5 más vendidos.
     *
     * @return array Los 5 productos más vendidos con la cantidad total de ventas.
    */
    public function best_selling_products_by_date($request) {
        // Llamada a la API para obtener el registro de órdenes
        $orders_record = $this->api_call('orders/list_record');

        // Verificar si hubo un error en la respuesta
        if ($orders_record['status'] == 0) {
            return ['status' => 0, 'message' => $orders_record['message']];
        }

        $products = $this->api_call('products');
        if ($products['status'] == 0) {
            return ['status' => 0, 'message' => $products['message']];
        }
        
        $orders_record = $orders_record['response'];
        $products = $products['response'];
        // Inicializa el campo 'sale_count' en cada producto a 0
        $products = array_map(function($product) {
            $product["sale_count"] = 0;
            return $product;
        }, $products);

        // Define el rango de fechas para el filtrado
        $start_date = new DateTime($request["start_date"]);
        $end_date = new DateTime($request["end_date"]);

        // Filtra las órdenes basándose en el rango de fechas
        $orders_filtered = array_filter($orders_record, function($order) use ($start_date, $end_date) {
            $last_update = new DateTime($order['last_update']);
            return $last_update >= $start_date && $last_update <= $end_date;
        });
        dump($orders_filtered);
        if(empty($orders_filtered)){

            $mensaje = 'No se encontraron productos en el rango de fechas seleccionados.';
            return ['status' => 0, 'message' => $mensaje];;
        }
        
        // Crea un mapa de productos con referencia por ID para actualizaciones rápidas
        $products_map = [];
        foreach ($products as &$p) {
            $products_map[$p['id']] = &$p; // Referencia para modificar el producto original
        }

        // Recorre las órdenes filtradas y actualiza el 'sale_count' de los productos vendidos
        foreach ($orders_filtered as $orders) {
            foreach ($orders['products'] as $product) {
                $id_product = $product['id'];
                $qty = $product['qty'];

                // Verifica si el producto existe en el mapa y actualiza el 'sale_count'
                if (isset($products_map[$id_product])) {
                    $products_map[$id_product]['sale_count'] += $qty;
                }
            }
        }

        // Ordena los productos en orden descendente por 'sale_count' y selecciona los 5 más vendidos
        usort($products, function($a, $b) {
            return $b['sale_count'] - $a['sale_count'];
        });

        // Devuelve los 5 productos más vendidos
        $top_5_products = array_slice($products, 0, 5);
        return ['status' => 1, 'data' => $top_5_products];
    }

    /**
     * Obtiene productos que tienen órdenes asociadas, con la posibilidad de filtrar por rango de fechas.
     * 
     * Esta función hace dos llamadas a la API: una para obtener los productos y otra para obtener las órdenes.
     * Luego, filtra las órdenes por el rango de fechas proporcionado (si se incluye), vincula los productos con sus órdenes correspondientes,
     * y retorna los productos que tienen al menos una orden asociada.
     * 
     * @param array $request Los parámetros de la solicitud, que pueden incluir las fechas `start_date` y `end_date` para filtrar las órdenes.
     * 
     * @return array Un array con el estado de la operación y los productos que tienen órdenes asociadas o un mensaje de error.
     * 
     */
    public function get_products_with_orders($request) {
        // Obtener los productos desde la API
        $productsResponse = $this->api_call('products');
        if ($productsResponse['status'] == 0) {
            return ['status' => 0, 'message' => $productsResponse['message']];
        }
    
        $ordersResponse = $this->api_call('orders/list_record');
        if ($ordersResponse['status'] == 0) {
            return ['status' => 0, 'message' => $ordersResponse['message']];
        }
    
        $products = $productsResponse['response'];
        $orders = $ordersResponse['response'];

        
        $start_date = isset($request["start_date"]) ? new DateTime($request["start_date"]) : null;
        $end_date = isset($request["end_date"]) ?new DateTime($request["end_date"]) : null;
    
        // Filtro de fechas en las órdenes
        if ($start_date && $end_date) {
            $orders = array_filter($orders, function($order) use ($start_date, $end_date) {
                $orderDate = new DateTime($order['last_update']);
                return $orderDate >= $start_date && $orderDate <= $end_date;
            });
        }
    
        // Vincular productos con órdenes
        foreach ($products as &$product) {
            $product['orders'] = array_filter($orders, function($order) use ($product) {
                foreach ($order['products'] as $orderProduct) {
                    if ($orderProduct['id'] == $product['id']) {
                        return true; // Se confirma que la orden incluye el producto actual
                    }
                }
                return false; // Si no se encuentra, no se incluye la orden
            });
            // Limpia los productos dentro de la orden)
            foreach ($product['orders'] as &$order) {
                unset($order['products']); // Remueve los productos de las órdenes si no los necesitas
            }
        }
        // Filtrar los elementos donde el campo 'orders' no esté vacío
        $products = array_filter($products, function($item) {
            return !empty($item['orders']);
        });
        
        if (empty($products)) {
            $mensaje = 'No se encontraron productos con pedidos el rango de fechas seleccionado.';
            return ['status' => 0, 'message' => $mensaje];
        }

        // Reindexar los valores del arreglo
        $products = array_values($products);

        
    
        return ['status' => 1, 'data' => $products];
    }

    /**
     * Obtiene un registro de órdenes, con la opción de filtrarlas por un rango de fechas.
     * 
     * Esta función hace una llamada a la API para obtener la lista de órdenes y luego filtra las órdenes por el rango de fechas
     * proporcionado en la solicitud (si se incluyen). Si no se encuentran órdenes dentro del rango de fechas, retorna un mensaje de error.
     * 
     * @param array $request Los parámetros de la solicitud, que pueden incluir las fechas `start_date` y `end_date` para filtrar las órdenes.
     * 
     * @return array Un array con el estado de la operación y las órdenes filtradas por fecha, o un mensaje de error si no se encuentran órdenes.
     * 
     */
    public function orders_record($request){
        $ordersResponse = $this->api_call('orders/list_record');
        if ($ordersResponse['status'] == 0) {
            return ['status' => 0, 'message' => $ordersResponse['message']];
        }
        $orders = $ordersResponse['response'];

        $start_date = isset($request["start_date"]) ? new DateTime($request["start_date"]) : null;
        $end_date = isset($request["end_date"]) ?new DateTime($request["end_date"]) : null;
    
        // Filtro de fechas en las órdenes
        if ($start_date && $end_date) {
            $orders = array_filter($orders, function($order) use ($start_date, $end_date) {
                $orderDate = new DateTime($order['last_update']);
                return $orderDate >= $start_date && $orderDate <= $end_date;
            });
            if(empty($orders)){
                $mensaje = 'No se encontraron ordenes en el rango de fechas seleccionado.';
            return ['status' => 0, 'message' => $mensaje];
            }
        }
        return ['status' => 1, 'data' => $orders];
    }
    /**
     * Obtiene los detalles de una orden específica a partir de su ID.
     * 
     * Esta función realiza una llamada a la API para obtener el registro de órdenes, luego filtra las órdenes
     * para encontrar la que coincida con el ID proporcionado. Si no se encuentra la orden, se retorna un mensaje de error.
     * 
     * @param int $id El ID de la orden cuya información se desea obtener.
     * 
     * @return array Un array con el estado de la operación y los detalles de la orden, o un mensaje de error si no se encuentra la orden.
     */
    public function order_detail($id){
        // Llamada a la API para obtener el registro de órdenes
        $orders_record = $this->api_call('orders/list_record');

        // Verificar si hubo un error en la respuesta
        if ($orders_record['status'] == 0) {
            return ['status' => 0, 'message' => $orders_record['message']];
        }
        $orders_record = $orders_record['response'];

        $result = array_filter($orders_record, function ($order) use ($id) {
            return $order['id'] === $id;
        });
        
        // Verificar si se encontró el producto
        if (!empty($result)) {
            $order_detail = array_values($result)[0]; // Obtener el primer resultado si hay más de uno
        } else {
            $message = 'Orden no encontrada.';
            return ['status' => 0, 'message' => $message];
        }
        return ['status' => 1, 'data' => $order_detail];
    }
    

}
