<?php


namespace App\Services\impl;

use App\Services\TranslationService;
use http\Exception\RuntimeException;


/**
 * Class GoogleTranslatorService
 * @package App\Services\impl
 */
class GoogleTranslatorService implements TranslationService
{

    /**
     * @param $data
     * @return array|mixed
     */
    public function translate($data)
    {
        $curl = curl_init();
        $translatingQuote = $data["expression"];
        $targetLanguage = $data["language"];
        $translatingQuote = urlencode($translatingQuote);
        $googleHost = env("GOOGLE_TRANSLATION_HOST");
        $googleKey = env("GOOGLE_TRANSLATION_KEY");
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('GOOGLE_TRANSLATION_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "source=en&q=".$translatingQuote."&target=".$targetLanguage,
            CURLOPT_HTTPHEADER => array(
                "accept-encoding: application/json",
                "content-type: application/x-www-form-urlencoded",
                "x-rapidapi-host: $googleHost",
                "x-rapidapi-key: $googleKey"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           throw new RuntimeException($err);
        }

        $resArr = json_decode($response);
        if (isset($resArr->error)) {
           return [
               'code' => $resArr->error->code,
               'error' =>  $resArr->error->message
           ];
        } else {
            return $resArr->data->translations[0];
        }
    }
}
