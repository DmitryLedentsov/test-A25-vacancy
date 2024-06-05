<?php
// Подключаем бд
include('sdbh.php');
$dbh = new sdbh();


// Функция для вычисления стоимости продукта с учетом тарифа
function calculateProductPrice($base_price, $tariffs, $days) {
    if (!$tariffs) { // если тарифов нет -> базовая формула
        return $base_price * $days;
    }

    $price_per_day = $base_price;

    //file_put_contents(__DIR__.'/tariff-data', print_r([
    //    "tariffs"=>$tariffs, "base"=>$base_price, "days"=>$days
    //], true));
    
    //идем последовательно по граничным значениям начиная с первого, и запоминаем на каком тарифе сейчас находимся
    $cur_range_price = array_values($tariffs)[0];
    foreach ($tariffs as $days_range => $price) {
        
        if ($days_range >= $days) { //перевалили за данное кол-во дней -> выбираем тариф и останавливаемся
            $price_per_day = $cur_range_price; 
            break;
        }
        $cur_range_price = $price;
    }
    

    return $price_per_day * $days;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = $_POST;


    // Получаем данные для селектора продуктов
    $products = $dbh->make_query("SELECT ID, NAME, PRICE, TARIFF FROM a25_products");

    //file_put_contents(__DIR__.'/received-data', print_r($data, true));
    $product_id = intval($data['product']);
    $days = intval($data['days']);
    $additional_services = isset($data['services']) ? $data['services'] : [];

    // Получаем информацию о выбранном продукте
    $product_query = $dbh->make_query("SELECT PRICE, TARIFF FROM a25_products WHERE ID=$product_id");
    $product_price = $product_query[0]['PRICE'];
    $product_tariffs = unserialize($product_query[0]['TARIFF']);

    // Вычисляем стоимость продукта
    $total_price = calculateProductPrice($product_price, $product_tariffs, $days);

    // Добавляем стоимость дополнительных услуг
    foreach ($additional_services as $service_cost) {
        $total_price += intval($service_cost) * $days;
    }

    // Выводим итоговую стоимость
    echo "Итоговая стоимость: $total_price руб.";
}
?>