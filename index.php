<?php
function getList($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response, 1);
    }
}

$as = isset($_ENV['PHP_SERVICE_AS']) ? $_ENV['PHP_SERVICE_AS'] : 'orders';

$services = [
    "products" => [
        "name" => "Products",
        "getList" => function () {
            return getList("https://fakestoreapi.com/products?limit=5");
        }
    ],
    "orders" => [
        "name" => "Orders",
        "getList" => function () {
            $orders = [];
            foreach ([3, 4] as $p) {
                $order = getList("https://fakestoreapi.com/products/" . $p);
                $order['quantity'] = ceil($p / 2);
                $order['total'] = $order['price'] * $order['quantity'];
                $orders[] = $order;
            }
            return $orders;
        }
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?= $services[$as]['name'] ?> - Microservices | SentinelFox</title>
</head>

<body class="w-full">
    <div class="px-10 w-full xl:max-w-screen-xl mx-auto">
        <h3 class="text-4xl font-bold my-6"><?= $services[$as]['name'] ?></h3>
        <div class="flex flex-col space-y-4 overflow-y-auto">
            <?php foreach ($services[$as]['getList']() ?? [] as $product) { ?>
                <div class="flex p-4 shadow bg-green-100 space-x-4">
                    <div class="w-32 bg-white shadow rounded">
                        <img src="<?= $product['image'] ?>" class="max-h-32 p-4 mx-auto" alt="<?= $product['title'] ?>">
                    </div>
                    <div class="flex flex-col justify-between w-full ">
                        <h4 class="text-xl font-bold"><?= $product['title'] ?></h4>
                        <div class="flex justify-between items-end">
                            <div>
                                <label class="text-xs">Prize</label>
                                <h4 class="text-3xl font-bold">$<?= $product['price'] ?></h4>
                            </div>
                            <?php if ($as == 'orders') { ?>
                                <div class="text-end">
                                    <h4 class="text-2xl font-bold"><span class="text-xs font-normal">Quantity:</span> <?= $product['quantity'] ?></h4>
                                    <h4 class="text-4xl font-bold"><span class="text-xs font-normal">Total:</span> $<?= $product['total'] ?></h4>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</body>

</html>