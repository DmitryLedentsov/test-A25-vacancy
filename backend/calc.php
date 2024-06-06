<?php
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
        
        if ($days_range > $days) { //перевалили за данное кол-во дней -> выбираем тариф и останавливаемся
            $price_per_day = $cur_range_price; 
            break;
        }
        $cur_range_price = $price;
    }
    

    return $price_per_day * $days;
}

?>