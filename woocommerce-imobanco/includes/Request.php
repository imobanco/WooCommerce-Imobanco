<?php
namespace WoocommerceImobanco;

class Request {

    private function request($method, $path, $params = []) {
        $ch = curl_init();
        if ('test' == WOO_IMOPAY_ENVIRONMENT) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $url = WOO_IMOPAY_API_URL . '/' . $path;
        if (substr($path, -1) != '/') {
            $url .= '/';
        }

        $method = strtolower($method);
        if ('post' == $method || 'put' == $method) {
            $postfields = json_encode($params);

            if ('post' == $method) curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Length: '.strlen($postfields)]);
        } else {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Api-Key '.WOO_IMOPAY_API_KEY, 'Accept:application/json', 'Content-Type: application/json']);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        try {
            return json_decode($response);
        } catch (\Exception $e) {
            return $response;
        }
    }

    public function post( $path, $params )
    {
        return $this->request('POST', $path, $params);
    }

    public function get( $path, $params = [] )
    {
        return $this->request('GET', $path, $params);
    }

    public function put( $path, $params )
    {
        return $this->request('PUT', $path, $params);
    }
}