<?php
/**
 * Created by PhpStorm.
 * User: a.dubrovskii
 * Date: 20.11.2018
 * Time: 14:03
 */

class Tester{
    private $token = "ea669f66dbf94bff896c128080b59ca64a3cdc7c40884bc8adfbf55c4c5c3355";
    private $url = "https://sentry.io/api/0/projects/alfaleads/affise/events/?";
    private $cursor ="";
    private $previous_cursor = "";
    private $count = 1;

    private $ch;

    function getSentry()
    {
        do{
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1 );


        $headers = array();
        $headers[0] = ("Authorization: Bearer ". $this->token);
        $headers[1] = "-i";
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);



//            echo $this->url . $this->cursor;
//            echo "<br>";
            curl_setopt($this->ch, CURLOPT_URL, $this->url.$this->cursor);
            $response = curl_exec($this->ch);
            $this->previous_cursor = $this->cursor;
            $this->cursor = "";


            $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            echo "<br>";

            curl_close($this->ch);

            $count = 1;
            $body_res = json_decode($body);
            foreach ($body_res as $item){
                echo $this->count . " " . $item->message . " | " . $item->id . " | " . $item->dateCreated;
                echo "<br>";
                $this->count++;
            }

            $pos = strpos($header, "next");
            $start_cursor_pos = strpos($header, "cursor", $pos);
            $end_cursor_pos = strpos($header, "Allow", $start_cursor_pos);
            $pagination_endlink = substr($header, $start_cursor_pos, $end_cursor_pos - $start_cursor_pos - 2);
            $pagination_endlink = str_replace('"', "", $pagination_endlink);
            $this->cursor = "&" . $pagination_endlink;

            echo "<br>";
            echo $this->cursor;
            echo "----------------------------------NEXT PAGE-------------------------";
            echo "<br>";

            //конец
            if($this->cursor == $this->previous_cursor){
                echo "<br>";
                break;
            }


//            echo "<br>";
//            echo $this->cursor;
//            echo "<br>";
            $this->count++;
        }
        while(true);


    }


}

$test = new Tester();
$test->getSentry();

