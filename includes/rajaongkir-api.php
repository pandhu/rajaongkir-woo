<?php
/**
* Raja Ongkir API
*
* This is a module, handles calculate and settings asuransi JNE Shipping
*
* @author Pandhuha
* @package RajaOngkir
* @since 8.0.0
*/

if ( !class_exists( 'RajaOngkir' ) ):

    class RajaOngkir {
        const TARGET_URL = "http://pro.rajaongkir.com/api/";
        const TARGET_API_KEY = "";

        public function getProvince(){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::TARGET_URL."province",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: ".self::TARGET_API_KEY
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                return json_decode($response);
            }

        }

        public function getCityByProvince($idProvince){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::TARGET_URL."city?province=".$idProvince,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: ".self::TARGET_API_KEY
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                return json_decode($response);
            }
        }

        public function getDistrictByCity($idCity){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::TARGET_URL."subdistrict?city=".$idCity,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: ".self::TARGET_API_KEY
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                return json_decode($response);
            }
        }

        public function calculateDeliveryCost($origin, $destination, $weight, $courier){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://pro.rajaongkir.com/api/cost",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                //CURLOPT_POSTFIELDS => "origin=".$origin."&originType=subdistrict&destination=".$destination."&destinationType=subdistrict&weight=".$weight."&courier=".$courier,
                CURLOPT_POSTFIELDS => "origin=".$origin."&originType=city&destination=".$destination."&destinationType=city&weight=".$weight."&courier=".$courier,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key: ".self::TARGET_API_KEY
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                return json_decode($response);
            }
        }
    }
endif;
