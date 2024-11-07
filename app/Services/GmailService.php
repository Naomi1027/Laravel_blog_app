<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class GmailService
{
    protected $client;
    protected $gmail;

    public function __construct()
    {
        // Google APIクライアントの初期設定
        $this->client = new Client();

        // 認証情報を設定
        $this->client->setAuthConfig(storage_path('credentials.json'));

        // Gmail APIのスコープを設定
        $this->client->addScope(Gmail::MAIL_GOOGLE_COM);

        // オフラインアクセスを有効にしてリフレッシュトークンを使用
        $this->client->setAccessType('offline');
        $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

        // Gmailサービスのインスタンスを作成
        $this->gmail = new Gmail($this->client);
    }

    /**
     * メールを送信するメソッド
     * @param string $to 送信先メールアドレス
     * @param string $subject メールの件名
     * @param string $body メールの本文（HTML形式）
     * @return \Google\Service\Gmail\Message
     */
    public function sendEmail($to, $subject, $body)
    {
        $message = new \Google\Service\Gmail\Message();

        // メールのヘッダーと本文を作成
        $rawMessage = "To: $to\r\n";
        $rawMessage .= "Subject: $subject\r\n";
        $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawMessage .= $body;

        // メッセージをエンコードして設定
        $encodedMessage = base64_encode($rawMessage);
        $message->setRaw(str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage));

        // メールを送信
        return $this->gmail->users_messages->send('me', $message);
    }
}
