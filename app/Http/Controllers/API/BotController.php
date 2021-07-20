<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BotController extends Controller
{
    private $botToken = "1892115740:AAG89JNh4hmgYvG_RjcOoQFkuzL_HN_yP_s";
    private $apiUrl = "https://api.telegram.org/bot";

    public function index()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        file_put_contents(__DIR__ . '/message.txt', print_r($data, true));

        // получили продукты
        $products =  json_decode(file_get_contents('https://bot.lemekha.com.ua/api/products'), 1);

        // пришел message
        if (isset($data['message'])) {
            $chatID = $data['message']['chat']['id'];
            // если команда старт
            if ($data['message']['text'] == "/start") {

                // Новый ордер
                $newOrderBot = new Orders();
                $newOrderBot->chat_id = $data['message']['message_id'];
                $newOrderBot->name = $data['message']['chat']['first_name'];
                $newOrderBot->save();
                $orders = $newOrderBot->id;

                // первый продукт
                $count = '1' . '/' . count($products);
                $buttons = $this->getButtons($count, $orders);
                $nameProduct = $products[0]['name'];
                $priceProduct = $products[0]['price'];

                // отправка в телеграм
                $text = "*$nameProduct* \nЦена: $priceProduct грн. \nhttps://images.unsplash.com/photo-1621569901036-f3733e72d312?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80";

                $content = [
                    'chat_id' => $chatID,
                    'text' => $text,
                    'reply_markup' => $buttons,
                    'parse_mode'=> 'Markdown'
                ];

                $this->sendMessage($content);

            } else {
                $this->sendMessage(['chat_id' => 488797716, 'text' => "Нет такой команды. Попробуйте /start"]);
            }
        }

        // пришел callback_query
        if (isset($data['callback_query'])) {
            $callbackChatID = $data['callback_query']['message']['chat']['id'];
            $callback_query = $data['callback_query']['data'];
            $paginate = stristr($data['callback_query']['message']['reply_markup']['inline_keyboard'][0][1]['text'], '/', true);

            $orderNumber = $data['callback_query']['message']['reply_markup']['inline_keyboard'][1][0]['text'];
            preg_match('#\((.*?)\)#', $orderNumber, $match);
            $orders = $match[1];

            switch ($callback_query) {
                case "pagination_prev":
                    $count = $paginate - 1 . '/' . count($products);

                    $buttons = $this->getButtons($count, $orders);
                    $nameProduct = $products[$paginate - 1]['name'];
                    $priceProduct = $products[$paginate - 1]['price'];

                    // отправка в телеграм
                    $text = "*$nameProduct* \nЦена: $priceProduct грн. \nhttps://images.unsplash.com/photo-1621569901036-f3733e72d312?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80";

                    $content = [
                        'chat_id' => $callbackChatID,
                        'message_id' => $data['callback_query']['message']['message_id'],
                        'reply_markup' => $buttons,
                        'text' =>  $text, // текст который отправили
                        'parse_mode'=> 'Markdown'
                    ];
                    // редактируем сообщение
                    $this->requestToTelegram($content, "editMessageText");
                    break;
                case "pagination_next":
                    $count = $paginate + 1 . '/' . count($products);

                    $buttons = $this->getButtons($count, $orders);
                    $nameProduct = $products[$paginate + 1]['name'];
                    $priceProduct = $products[$paginate + 1]['price'];

                    // отправка в телеграм
                    $text = "*$nameProduct* \nЦена: $priceProduct грн. \nhttps://images.unsplash.com/photo-1621569901036-f3733e72d312?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80";

                    $content = [
                        'chat_id' => $callbackChatID,
                        'message_id' => $data['callback_query']['message']['message_id'],
                        'reply_markup' => $buttons,
                        'text' =>  $text, // текст который отправили
                        'parse_mode'=> 'Markdown'
                    ];
                    // редактируем сообщение
                    $this->requestToTelegram($content, "editMessageText");
                    break;

                case "add_cart":
                    $product = Products::where('availability', 1)->orderBy('created_at', 'desc')->get();
                    $nameProduct = $product[$paginate]->name;

                    // сохранение заказа
                    $orderUpdate = Orders::find($orders);
                    $orderUpdate->products = $product[$paginate]->id;
                    $orderUpdate->save();

                    $this->sendMessage(['chat_id' => $callbackChatID, 'text' => "Товар ($nameProduct) - добавлен в корзину!"]);
                    break;

                case "add_order":
                    $orderUpdate = Orders::find($orders);
                    if($orderUpdate->products !== null) {
                        $this->sendMessage(['chat_id' => $callbackChatID, 'text' => "Заказ оформлен"]);
                    } else {
                        $this->sendMessage(['chat_id' => $callbackChatID, 'text' => "Нет товаров"]);
                    }
                    break;
            }
        }
    }

    /** Получаем кнопки
     * @param $count
     * @param string $type
     * @return mixed
     */
    private function getButtons($count, $orders) {
        return json_encode([
            'inline_keyboard' => [
                // первый ряд
                [
                    ['text' => '<', 'callback_data' => 'pagination_prev'],
                    ['text' => $count, 'callback_data' => 'pagination'],
                    ['text' => '>', 'callback_data' => 'pagination_next'],
                ],
                // второй ряд
                [
                    ['text' => "➕ Добавить в заказ ($orders)", 'callback_data' => 'add_cart'],
                    ['text' => "✅ Оформить заказ", 'callback_data' => 'add_order']
                ],

            ],
        ], true);
    }

    /** Передаем метод
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function sendMessage($data)
    {
        $this->requestToTelegram($data, "sendMessage");
    }

    /** Отправляем запрос
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->botToken . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
}
