<?php
require_once 'backend/sdbh.php';
$dbh = new sdbh();

?>
<html>
    <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/style_form.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row row-header">
                <div class="col-12">
                    <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
                    <h1>Прокат</h1>
                </div>
            </div>
            <div class="row row-body">
                <h4>Форма рассчета:</h4>
                <div class="col-12">
        
                    <?php
                        // Получение списка продуктов
                        $products = $dbh->make_query("SELECT `ID`, `NAME`, `PRICE`, `TARIFF` FROM `a25_products`");
                        // Получение списка дополнительных услуг
                        $services = unserialize($dbh->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id')[0]['set_value']);
                    ?>
                    <form action="" method="post" id="form">
                        <label class="form-label" for="product">Выберите продукт:</label>
                        <select class="form-select" name="product" id="product" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['ID'] ?>"><?= $product['NAME'] ?> за <?= $product['PRICE'] ?></option>
                            <?php endforeach; ?>
                        </select>
                            
                        <label for="days" class="form-label mt-3">Количество дней:</label>
                        <input type="number" class="form-control" name="days" id="days" min="1" max="30" required>
                            
                        <label class="form-label mt-3">Дополнительно:</label>
                        <?php foreach ($services as $name=>$cost): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="<?= $cost ?>" id="service-<?= $name  ?>">
                                <label class="form-check-label" for="service-<?= $name  ?>">
                                    <?= $name ?> за <?= $cost ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary mt-3">Рассчитать</button>
                    </form>
                </div>
                <!-- сюда пишем результат -->
                <h4 id="result" hidden></h4>
            </div> 
        </div> 
    </body>
</html>