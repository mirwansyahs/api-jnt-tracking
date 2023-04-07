<?php 

        
    /**
     * Api
     */
    class Api {
        protected $key_auth = "";
        protected $base_url = "https://jet.co.id/index/router/index.html";
        protected $resi     = "";
        protected $lang     = "id";

        function Api($key_auth = '', $resi = '')
        {
            $this->key_auth = $key_auth;
            $this->resi = $resi;
        }

        function tracking()
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->base_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "method=query/findTrack".
                "&data[billcode]=".$this->resi.
                "&data[source]=3".
                "&pId=f724b5cfd25e077849d48c2b86343cc3".
                "&pst=441117ce128608191685af00cf6ff8cc".
                "&data[lang]=".$this->lang,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));

            $json = curl_exec($curl);
            echo $json;
        }
    }

    header("Content-type: application/json");
    if (@$_GET['api_key'] != null && @$_GET['waybill'] != null){
        $Api = new Api($_GET['api_key'], $_GET['waybill']);
        $json = json_encode($Api->tracking());
    }else{
        $arr = array(
            'status'    => false,
            'message'   => "Api key dan Waybill harus diisi"
        );

        echo json_encode($arr);
    }
?>