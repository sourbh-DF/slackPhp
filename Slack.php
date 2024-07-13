<?php


class Slack
{

    public $token;

    public $baseUrl = "https://slack.com/api/";
    public $headers = [];
    public function __construct($token)
    {
        $this->token = $token;
        $this->headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/x-www-form-urlencoded'
        ];
    }
    /*
     * getting users form workspace
     *
     */
    function getMembers()
    {
        $url = $this->baseUrl . "users.list";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  $this->headers);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        // return ($data);
        if ($data['ok']) {
            return ['status' => true, 'message' => 'Success', 'data' => $data['members']];
        } else {
            return ['status' => false, 'message' => 'empty.', 'data' => $data];
        }
    }

    function getChannels()
    {
        $url = $this->baseUrl . "conversations.list";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        // return ($data);
        if ($data['ok'] == 1) {
            return ['status' => true, 'message' => 'Success', 'data' => $data['channels']];
        } else {
            return ['status' => false, 'message' => 'empty.', 'data' => $data];
        }
    }
    function sendMessage($channelId, $message, string $scheduleTime = null, string $sendAs = null, $blocks = null, $attachments = null)
    {
        if ($scheduleTime == null) {
            $ch = curl_init($this->baseUrl . "chat.postMessage");
        } else {
            $ch = curl_init($this->baseUrl . "chat.scheduleMessage");

            $post_data["post_at"] = $scheduleTime;
        }
        $post_data["token"] = $this->token;
        $post_data["channel"] = $channelId;
        if ($sendAs) {

            $post_data["as_user"] = true;
            $post_data['username'] =  $sendAs;
        }
        $post_data["text"] = $message;
        if ($blocks !== null) {
            $post_data['blocks'] = $blocks; // array, message content in Slack block kit format
        }

        if ($attachments !== null) {
            $post_data['attachments'] = $attachments; // array, message attachments
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // print_r($result);
        $data = json_decode($result, true);
        return ($data);

        if (isset($data['$data'])) {
            header('Location: form.php?error=' . urlencode($data['error']));
        }
        if ($data['ok']) {
            print_r(['status' => true, 'data' => $data]);
        } else {
            // return ['status' => false, 'message' => 'empty.', 'data' => $data,''];
        }
    }

    // function fileUpload($file, $channel)
    // {


    //     ////////////////////////

    //     $data = [
    //         "channels" => $channel,
    //         "filetype" => "auto", // Specify file type, e.g., "auto", "png", "jpg", etc.
    //         "filename" => $file['name'], // Name of the file
    //         "length" => $file['size'],
    //     ];
    //     // return $data;

    //     $ch = curl_init("https://slack.com/api/files.getUploadURLExternal");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $upload_url_response = json_decode($response, true);
    //     return ($upload_url_response);
    //     if (!$upload_url_response['ok']) {
    //         echo "Error getting upload URL: " . $upload_url_response['error'];
    //         exit;
    //     }

    //     $upload_url = $upload_url_response['upload_url'];
    //     $file_id = $upload_url_response['file_id'];
    //     // exit;
    //     /////////////////////

    //     $file_path =  $file['tmp_name'];

    //     $ch = curl_init($upload_url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_PUT, true);
    //     curl_setopt($ch, CURLOPT_INFILE, fopen($file_path, 'r'));
    //     curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file_path));

    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     echo 'line 148';
    //     // return ($response);
    //     if (!$response) {
    //         return "Error uploading file.$response";
    //     }

    //     ////////////////////////////////
    //     // $checkCompleteUrl = 'https://slack.com/api/files.completeUploadExternal';



    //     // Endpoint URL
    //     $checkCompleteUrl = 'https://slack.com/api/files.completeUploadExternal';

    //     // Data to be sent in the request
    //     $data = [
    //         'files' => json_encode([
    //             [
    //                 'id' => $file_id,
    //                 // 'title' => 'YOUR_FILE_TITLE'
    //             ]
    //         ])
    //     ];

    //     // Initialize curl
    //     $ch = curl_init($checkCompleteUrl);

    //     // Set curl options
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->token
    //     ]);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    //     // Execute curl request
    //     $response = curl_exec($ch);

    //     // Close curl
    //     curl_close($ch);

    //     // Handle response
    //     $responseData = json_decode($response, true);
    //     return $responseData;
    //     if ($responseData['ok']) {
    //         echo "File upload completed successfully.";
    //     } else {
    //         echo "Error: " . $responseData['error'];
    //     }


    //     // return $response."File uploaded successfully!";



    // }
}
