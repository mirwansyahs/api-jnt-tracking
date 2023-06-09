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
            // echo $json;
            $return = json_decode($json);
            if (count($return->data->details) > 0){
                $i = 0;
                $delivered = false;
                while ($i < count($return->data->details)) {
                    $histori[$i]['time']        =  $return->data->details[$i]->scantime;
                    if ($return->data->details[$i]->scanscode == "1" || $return->data->details[$i]->scanscode == "3"){
                        $position = $return->data->details[$i]->city;
                    }else if($return->data->details[$i]->scanscode == "2"){
                        $position = $return->data->details[$i]->city . " - " . $return->data->details[$i]->nextSite;
                    }else if($return->data->details[$i]->scanscode == "4"){
                        $position = " oleh " . $return->data->details[$i]->deliveryName . " - " . $return->data->details[$i]->city;
                    }else if($return->data->details[$i]->scanscode == "5"){
                        $position = " dan diterima oleh " .$return->data->details[$i]->signer . " - " . $return->data->details[$i]->city;
                    }else if($return->data->details[$i]->scanscode == "6"){
                        $position = " ".$return->data->details[$i]->desc . " karena " . $return->data->details[$i]->reason;
                    }else{
                        $position = $return->data->details[$i]->desc . " - " . $return->data->details[$i]->city;
                    }
                    $histori[$i]['desc']        =  $return->data->details[$i]->scanstatus;
                    $histori[$i]['deliveryName']=  $return->data->details[$i]->deliveryName; 
                    $histori[$i]['deliveryTel'] =  $return->data->details[$i]->deliveryTel;
                    $histori[$i]['position']    =  $position; 
                    if ($return->data->details[$i]->scanstatus == "Terkirim" || $return->data->details[$i]->scanstatus == "Delivered"){
                        $delivered = 'DELIVERED';
                    }
                    $i++;
                }
                $arr = array(
                    'info'      => 200,
                    'resi_id'   => $this->resi,
                    'deskripsi' => 'No resi ditemukan.',
                    'status'    => $delivered,
                    'histori'   => $histori,
                    'createdBy' => 'Solid Project'
                );
            }else{
                $arr = array(
                    'info'      => 403,
                    'resi_id'   => $this->resi,
                    'deskripsi' => 'No resi tidak ditemukan.',
                    'createdBy' => 'Solid Project'
                );
            }
            echo json_encode($arr);
        }
    }

    header("Content-type: application/json");
    if (@$_GET['waybill'] != null){
        $Api = new Api($_GET['api_key'], $_GET['waybill']);
        $json = $Api->tracking();
    }else{
        $arr = array(
            'status'    => false,
            'message'   => "Api key dan Waybill harus diisi"
        );

        echo json_encode($arr);
    }
?>