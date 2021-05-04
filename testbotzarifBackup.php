<?php

include('image_hash/autoload.php');

use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
//use Jenssegers\ImageHash\Implementations\AverageHash;


$servername = "localhost";
$username = "ordebpxy_admin";
$password = 'Zc5i$E(PnOUH';
$dbname = "ordebpxy_orderbot";



$hubVerifyToken = 'testbotZARIF';
$accessToken = "EAAWOrcpwjdcBANn5MPNe8xPYZAV0I9lT8T78ZAFwQWNQ6sWB0dUwcertmz62erbjSIVP9Mp6pPX5nhu04sMwwAzjyDDZB9MjZBmuQCRUJVKNVni4m0cvqgk0HrSzsGr0gSB1VV138SQH8zZBS4JVE3HuZAixpQ3w0TUcf0zR2IQwZDZD";




if (isset($_GET['confirm'])) {
  $confirmid = (string) $_GET['confirm'];
  $orderID = (string) $_GET['serial'];

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }






  $sql = "UPDATE delivery SET confirmed='1' WHERE id=$orderID";


  if ($conn->query($sql) !== TRUE) {
    echo "Error updating record: " . $conn->error;
  }



  $conn->close();




  $answer = "Your order has been confirmed\nThank you for ordering.\nOur deliveryman will call you soon\n\nYour order id : " . $orderID;



  $response = [
    'recipient' => ['id' => $confirmid],
    'message' => ['text' => $answer]
  ];



  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_exec($ch);

  curl_close($ch);
?>
  <script>
    window.location.replace('dashboard.php')
  </script>
<?php

  exit;
} else if (isset($_GET['cancel'])) {
  $confirmid = (string) $_GET['cancel'];
  $orderID = (string) $_GET['serial'];

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }






  $sql = "UPDATE delivery SET confirmed='2' WHERE id=$orderID";


  if ($conn->query($sql) !== TRUE) {
    echo "Error updating record: " . $conn->error;
  }



  $conn->close();




  $answer = "Sorry,your order has been cancelled";



  $response = [
    'recipient' => ['id' => $confirmid],
    'message' => ['text' => $answer]
  ];



  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_exec($ch);

  curl_close($ch);
?>
  <script>
    window.location.replace('dashboard.php')
  </script>
<?php

  exit;
  // echo  "<script type='text/javascript'>";
  // echo "window.close();";
  // echo "</script>";


}





/// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}





$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}





$showCart = 0;
$cartConfirmed = 0;
$botstatus = 1;

$raw = file_get_contents('php://input');
// $raw='{"object":"page","entry":[{"id":"105679800933261","time":1580648025632,"messaging":[{"sender":{"id":"2870423626351639"},"recipient":{"id":"105679800933261"},"timestamp":1580647911453,"message":{"mid":"m_o-ux4zE-PUO7SAt8jL_gqsfnJoig1rUgHoSM7A8l5pKsGwfJarVb72KdSSIgZU6Zkj5_vGRQmLXN5gZsbCs3DQ","text":"Tanzim"}}]}]}';

$input = json_decode($raw, true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$postback = $input['entry'][0]['messaging'][0]['postback']['payload'];
$quickPostback = $input['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
$is_Attachment = !empty($input['entry'][0]['messaging'][0]['message']['attachments']);
$attachmentCount = count($input['entry'][0]['messaging'][0]['message']['attachments']);


$attachmentLink = [];

for ($a = 0; $a <= $attachmentCount; $a++) {

  $attachmentLink[$a] = $input['entry'][0]['messaging'][0]['message']['attachments'][$a]['payload']['url'];
}





$fbid =  (string) $senderId;


$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, 'https://graph.facebook.com/' . $senderId . '?fields=first_name,last_name,profile_pic&access_token=' . $accessToken);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$userProfile = curl_exec($cURLConnection);
curl_close($cURLConnection);

$uProfile = json_decode($userProfile, true);

$userName = $uProfile['first_name'];

$fullName = $uProfile['first_name'] . " " . $uProfile['last_name'];


$totalPrice = 0;
//$userOrderArray = array("pizza1medium" => "0", "pizza1large" => "0", "pizza2large" => "0", "pizza2medium" => "0",);



$cartConfirmed = 0;
$phoneNumber = 0;

$deliveryLocation = "n/a";



$botactive = 1;



$sql = "SELECT jsonmenu,cardmenu  FROM customer WHERE id=4";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while ($row = $result->fetch_assoc()) {

    $userOrderArray = json_decode($row["jsonmenu"], true);
    $carouselmenu = json_decode($row["cardmenu"], true);
  }
}

$sql = "SELECT userorderjson,botstatus,deliveryaddress,phonenumber,cartconfirmed FROM ordertesting WHERE fbid=$senderId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while ($row = $result->fetch_assoc()) {

    $orderinput = json_decode($row["userorderjson"], true);


    $itemsize = count($orderinput);

    // array keys
    $itemkeys = array_keys($orderinput);


    for ($c = 1; $c <= $itemsize; $c++) {

      $userOrderArray['item' . $c]["quantity"] = $orderinput['item' . $c]["quantity"];
    }





    $cartConfirmed = $row["cartconfirmed"];
    $deliveryLocation = $row["deliveryaddress"];
    $phoneNumber = $row["phonenumber"];
    $botactive = $row["botstatus"];
  }
} else {
  $stmt = $conn->prepare("INSERT INTO ordertesting ( name, fbid) VALUES (?, ?)");

  $stmt->bind_param('ss', $fullName, $senderId);
  $stmt->execute();

  $stmt->close();
}



if ($is_Attachment) {

  $flag = 0;
  $answer = [
    "attachment" => [
      "type" => "template",
      "payload" => [

        "template_type" => "generic",
        "image_aspect_ratio" => "square",
        "elements" => []
      ]
    ]
  ];

  for ($im = 0; $im < $attachmentCount; $im++) {

    // $Xanswer =  "img" . $im;

    // XMessage($Xanswer, $senderId, $accessToken, $input);

    $sentImage = $attachmentLink[$im];


    $hasher = new ImageHash(new DifferenceHash());
    $hash1 = $hasher->hash($sentImage);



    for ($c = 1; $c <= count($carouselmenu); $c++) {

      $hash2 = $hasher->hash($carouselmenu['card' . $c]['image']);


      $distance = $hasher->distance($hash1, $hash2);
      // $Xanswer =  $distance;

      // XMessage($Xanswer, $senderId, $accessToken, $input);


      if ($distance <= 10) {
        $flag = 1;
        // $Xanswer =  $distance;

        // XMessage($Xanswer, $senderId, $accessToken, $input);

        $answer['attachment']['payload']['elements'][$im] = array(
          "title" => $carouselmenu['card' . $c]['name'],
          "item_url" => $carouselmenu['card' . $c]['image'],
          "image_url" => $carouselmenu['card' . $c]['image'],
          "subtitle" => "Price : Taka " . $userOrderArray['item' . ($c)]["price"],

        );

        break;
      }
    }
    if ($flag != 1) {

      // $Xanswer =  "cropped";

      // XMessage($Xanswer, $senderId, $accessToken, $input);
      $croppedimg = cropImage($sentImage);

      $hash1 = $hasher->hash($croppedimg);

      //  i/p 1 img

      for ($c = 1; $c <= count($carouselmenu); $c++) {

        $hash2 = $hasher->hash($carouselmenu['card' . $c]['image']);


        $distance = $hasher->distance($hash1, $hash2);


        if ($distance <= 10) {
          $flag = 0;
          // $Xanswer =  $distance;

          // XMessage($Xanswer, $senderId, $accessToken, $input);

          $answer['attachment']['payload']['elements'][$im] = array(
            "title" => $carouselmenu['card' . $c]['name'],
            "item_url" => $carouselmenu['card' . $c]['image'],
            "image_url" => $carouselmenu['card' . $c]['image'],
            "subtitle" => "Price : Taka " . $userOrderArray['item' . ($c)]["price"],

          );

          break;
        }
      }
    }
  }

  if (count($answer['attachment']['payload']['elements']) != 0) {


    ZMessage($answer, $senderId, $accessToken, $input);
  }
  // $Xanswer =  $distance;

  // XMessage($Xanswer, $senderId, $accessToken, $input);
}










$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if (!empty($input)) {
  $result = curl_exec($ch);
}
curl_close($ch);





///// Starting of user defined functions
function ZMessage($msgZ, $senderId, $accessToken, $input)
{
  $Zanswer = $msgZ;





  $Zresponse = [
    'recipient' => ['id' => $senderId],
    'message' => $Zanswer
  ];

  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Zresponse));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  if (!empty($input)) {
    $result = curl_exec($ch);
  }
  curl_close($ch);
}
function deleteUserPersistent($senderId, $accessToken)
{



  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v8.0/me/custom_user_settings?psid=' . $senderId . '&params=[%22persistent_menu%22]&access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);
}

function chaton($senderId, $accessToken)
{


  $resp = ' {
    
        "psid": "' . $senderId . '",
        "persistent_menu": [
          {
            "locale": "default",
            "composer_input_disabled": false,
            "call_to_actions": [
             
                {
                    "type": "postback",
                    "title": "Main Menu",
                    "payload": "clear"
                },
                
                  {
                    "type": "postback",
                    "title": "Main Menu 2",
                    "payload": "clear"
                },
            
           
    
              
            ]
          }
        ]
      }';


  $ch = curl_init('https://graph.facebook.com/v8.0/me/custom_user_settings?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $resp);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

  curl_exec($ch);

  curl_close($ch);
}

function chatoff($senderId, $accessToken)
{


  $resp = ' {
    
        "psid": "' . $senderId . '",
        "persistent_menu": [
          {
            "locale": "default",
            "composer_input_disabled": true,
            "call_to_actions": [
                {
                    "type": "postback",
                    "title": "Order More Items",
                    "payload": "order"
                },
             
                {
                    "type": "postback",
                    "title": "View Cart",
                    "payload": "viewcart"
                },
                
                  {
                    "type": "postback",
                    "title": "Edit Cart",
                    "payload": "editcart"
                },

            
           
    
              
            ]
          }
        ]
      }';


  $ch = curl_init('https://graph.facebook.com/v8.0/me/custom_user_settings?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $resp);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

  curl_exec($ch);

  curl_close($ch);
}



function XMessage($msgX, $senderId, $accessToken, $input)
{
  $Xanswer = $msgX;
  $Xresponse = [
    'recipient' => ['id' => $senderId],
    'message' => ['text' => $Xanswer]
  ];

  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Xresponse));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  if (!empty($input)) {
    $result = curl_exec($ch);
  }
  curl_close($ch);
}






function replyimage_viaID($attahmentid)
{
  ///  sample attachment ID {"attachment_id":"706995740095830"}

  $accessToken = $GLOBALS["accessToken"];
  $input = $GLOBALS["input"];
  $senderId = $GLOBALS["senderId"];


  $resp = '{
  "recipient":{
    "id":"' . $senderId . '"
  },
  "message":{
    "attachment":{
      "type":"image", 
      "payload":{
        "attachment_id": "' . $attahmentid . '"
      }
    }
  }
}';


  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $resp);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  if (!empty($input)) {
    $result = curl_exec($ch);
  }
  curl_close($ch);
}


function typingON($senderId, $accessToken)
{




  $resp = '{
        "recipient":{
          "id":"' . $senderId . '"
        },
        "sender_action":"typing_on"
      }';

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $resp);

  $headers = array();
  $headers[] = 'Content-Type: application/json';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);
}
function typingOFF($senderId, $accessToken)
{




  $resp = '{
        "recipient":{
          "id":"' . $senderId . '"
        },
        "sender_action":"typing_off"
      }';

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $resp);

  $headers = array();
  $headers[] = 'Content-Type: application/json';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);
}

function textButton($msgX, $senderId, $accessToken, $input)

{
  $Xanswer = $msgX;
  $Xresponse = [
    'recipient' => ['id' => $senderId],
    'message' => $Xanswer
  ];

  $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Xresponse));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  if (!empty($input)) {
    $result = curl_exec($ch);
  }
  curl_close($ch);
}

function cropImage($sentImage)
{



  $finalImage = imagecreatefromjpeg($sentImage);



  if (imagesy($finalImage) > 2.5 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.4, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.3]);
  } else if (imagesy($finalImage) > 2 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.3, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.4]);
  } else if (imagesy($finalImage) > 1.6 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.2, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.6]);
  } else if (imagesy($finalImage) > 1.4 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.15, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.7]);
  } else if (imagesy($finalImage) > 1.2 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.05, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);
  } else $im2 = $finalImage;


  $cropped_img_black = imagecropauto($im2, IMG_CROP_THRESHOLD, 1, 0);
  $cropped_img_white = imagecropauto($cropped_img_black, IMG_CROP_THRESHOLD, 1, 16777215);



  //$cropped_img_white = imagecropauto($cropped_img_black, IMG_CROP_THRESHOLD, 1, 16777215);
  //$cropped_img_black = imagecropauto($cropped_img_white, IMG_CROP_THRESHOLD, 1, 0);

  // header('Content-Type: image/png');
  imagepng($cropped_img_white, 'converted.png');



  return 'converted.png';
}
function uploadimage($imageurl)
{
  //588908092054728

  $accessToken = $GLOBALS["accessToken"];
  $senderId = $GLOBALS["senderId"];



  $image = '{
  "message":{
    "attachment":{
      "type":"image", 
      "payload":{
        "is_reusable": false,
        "url":"' . $imageurl . '"
      }
    }
  }
}';

  $ch = curl_init('https://graph.facebook.com/v7.0/me/message_attachments?access_token=' . $accessToken);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $image);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);





  $result = curl_exec($ch);


  $return = json_decode($result, true);
  echo $attachment_id = $return["attachment_id"];
}
//      plain text
/*
$answer = 'Hello '.$userName.', Would you like to order?';
	


$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];*/



//    Carausel

/*["attachment"=>[
      "type"=>"template",
      "payload"=>[
        "template_type"=>"generic",
        "elements"=>[
          [
            "title"=>"Welcome to Peter\'s Hats",
            "item_url"=>"https://www.cloudways.com/blog/migrate-symfony-from-cpanel-to-cloud-hosting/",
            "image_url"=>"https://www.cloudways.com/blog/wp-content/uploads/Migrating-Your-Symfony-Website-To-Cloudways-Banner.jpg",
            "subtitle"=>"We\'ve got the right hat for everyone.",
            "buttons"=>[
              [
                "type"=>"web_url",
                "url"=>"https://petersfancybrownhats.com",
                "title"=>"View Website"
              ],
              [
                "type"=>"postback",
                "title"=>"Start Chatting",
                "payload"=>"DEVELOPER_DEFINED_PAYLOAD"
              ]              
            ]
          ],
          
          
        ]
      ]
    ]]*/


//        LIST
/*
    curl -X POST -H "Content-Type: application/json" -d '{
  "recipient":{
    "id":"RECIPIENT_ID"
  }, 
  "message": {
    "attachment": {
      "type": "template",
      "payload": {
        "template_type": "list",
        "top_element_style": "compact",
        "elements": [
          {
            "title": "Classic T-Shirt Collection",
            "subtitle": "See all our colors",
            "image_url": "https://peterssendreceiveapp.ngrok.io/img/collection.png",          
            "buttons": [
              {
                "title": "View",
                "type": "web_url",
                "url": "https://peterssendreceiveapp.ngrok.io/collection",
                "messenger_extensions": true,
                "webview_height_ratio": "tall",
                "fallback_url": "https://peterssendreceiveapp.ngrok.io/"            
              }
            ]
          },
          {
            "title": "Classic White T-Shirt",
            "subtitle": "See all our colors",
            "default_action": {
              "type": "web_url",
              "url": "https://peterssendreceiveapp.ngrok.io/view?item=100",
              "messenger_extensions": false,
              "webview_height_ratio": "tall"
            }
          },
          {
            "title": "Classic Blue T-Shirt",
            "image_url": "https://peterssendreceiveapp.ngrok.io/img/blue-t-shirt.png",
            "subtitle": "100% Cotton, 200% Comfortable",
            "default_action": {
              "type": "web_url",
              "url": "https://peterssendreceiveapp.ngrok.io/view?item=101",
              "messenger_extensions": true,
              "webview_height_ratio": "tall",
              "fallback_url": "https://peterssendreceiveapp.ngrok.io/"
            },
            "buttons": [
              {
                "title": "Shop Now",
                "type": "web_url",
                "url": "https://peterssendreceiveapp.ngrok.io/shop?item=101",
                "messenger_extensions": true,
                "webview_height_ratio": "tall",
                "fallback_url": "https://peterssendreceiveapp.ngrok.io/"            
              }
            ]        
          }
        ],
         "buttons": [
          {
            "title": "View More",
            "type": "postback",
            "payload": "payload"            
          }
        ]  
      }
    }
  }
}' "https://graph.facebook.com/me/messages?access_token=PAGE_ACCESS_TOKEN"*/




// text button
/*
$answer = ["attachment" => [
  "type" => "template",
  "payload" => [
    "template_type" => "button",
    "text" => "kire",

    "buttons" => [

      [
        "type" => "postback",
        "title" => "ORDER AGAIN",
        "payload" => "order"
      ],

    ]
  ]
]];

$response = [
  'recipient' => ['id' => $senderId],
  'message' => $answer
];

*/
?>