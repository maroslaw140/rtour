<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Walidacja danych wejściowych
    $requiredFields = ['name', 'email', 'phone', 'direction-type', 'airport', 'passengers', 'address-info', 'date'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Wypełnij wszystkie wymagane pola']);
            exit;
        }
    }

    // Przygotowanie danych
    $data = [
        'name' => htmlspecialchars($_POST['name']),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars($_POST['phone']),
        'direction' => htmlspecialchars($_POST['direction-type']),
        'airport' => htmlspecialchars($_POST['airport']),
        'passengers' => htmlspecialchars($_POST['passengers']),
        'address' => htmlspecialchars($_POST['address-info']),
        'date' => htmlspecialchars($_POST['date'])
    ];

    // Utwórz PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Konfiguracja SMTP z .env
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURITY'];
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $message = '
            <!DOCTYPE html>
            <html lang="pl">
            <head>
            <meta charset="UTF-8">
            <style>
            body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            }
            .container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .section {
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            }
            .section:last-child {
            border-bottom: none;
            }
            .label {
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
            display: block;
            }
            .value {
            color: #555;
            }
            .footer {
            font-size: 12px;
            color: #aaa;
            text-align: center;
            margin-top: 30px;
            }
            a {
            color: #007bff;
            text-decoration: none;
            }
            </style>
            </head>
            <body>
            <div class="container">
            <div class="section">
            <span class="label">Przylot / wylot</span>
            <span class="value">' . $data['direction'] . '</span>
            </div>

            <div class="section">
            <span class="label">Lotnisko</span>
            <span class="value">' . $data['airport'] . '</span>
            </div>

            <div class="section">
            <span class="label">Ilu pasażerów</span>
            <span class="value">' . $data['passengers'] . '</span>
            </div>

            <div class="section">
            <span class="label">Imię</span>
            <span class="value">' . $data['name'] . '</span>
            </div>

            <div class="section">
            <span class="label">Adres e-mail</span>
            <span class="value"><a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a></span>
            </div>

            <div class="section">
            <span class="label">Numer telefonu</span>
            <span class="value">' . $data['phone'] . '</span>
            </div>

            <div class="section">
            <span class="label">Adres wyjazdu / przyjazdu</span>
            <span class="value">' . nl2br($data['address']) . '</span>
            </div>

            <div class="section">
            <span class="label">Data</span>
            <span class="value">' . $data['date'] . '</span>
            </div>
            </div>
            <div class="footer">
            Wysłano z witryny <a href="https://reniatour.pl">ReniaTour komfortowy przewóz osób – Opole</a>
            </div>
            </body>
            </html>';


        // Nadawca i odbiorcy
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($_ENV['MAIL_TO']);
        
        if (!empty($_ENV['MAIL_BCC'])) {
            $mail->addBCC($_ENV['MAIL_BCC']);
        }

        $mail->addReplyTo($data['email'], $data['name']);

        // Treść emaila
        $mail->isHTML(true);
        $mail->Subject = 'Nowa rezerwacja';
        
        $mail->Body = sprintf($message);

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Wiadomość wysłana pomyślnie']);
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Błąd podczas wysyłania wiadomości']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowa metoda żądania']);
}