<?php
// Подключаем класс sdbh
include('sdbh.php');
$dbh = new sdbh();

// Получаем данные для селектора продуктов
$products = $dbh->make_query("SELECT ID, NAME, PRICE, TARIFF FROM a25_products");

// Получаем данные для дополнительных услуг
$services_result = $dbh->make_query("SELECT set_value FROM a25_settings WHERE set_key='services'");
$services = unserialize($dbh->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id')[0]['set_value']);

// Функция для вычисления стоимости продукта с учетом тарифа
function calculateProductPrice($price, $tariffs, $days) {
    if (!$tariffs) {
        return $price * $days;
    }

    $price_per_day = $price;
    $tarif = json_decode($tariffs, true);
    
    // Determine the price based on the number of days
    if ($tarif) {
        foreach ($tarif as $days_range => $price) {
            list($min_days, $max_days) = explode('-', $days_range);
            if ($days >= $min_days && $days <= $max_days) {
                break;
            }
        }
    }

    return $price_per_day * $days;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = $_POST;


    file_put_contents(__DIR__.'/received-data', print_r($data, true));
    $product_id = intval($data['product']);
    $days = intval($data['days']);
    $additional_services = isset($data['services']) ? $data['services'] : [];

    // Получаем информацию о выбранном продукте
    $product_query = $dbh->make_query("SELECT PRICE, TARIFF FROM a25_products WHERE ID=$product_id");
    $product_price = $product_query[0]['PRICE'];
    $product_tariffs = $product_query[0]['TARIFF'];

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