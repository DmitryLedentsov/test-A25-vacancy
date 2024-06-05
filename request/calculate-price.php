<?php
// Подключаем бд
require_once '../backend/calc.php';
require_once '../backend/sdbh.php';
$dbh = new sdbh();

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