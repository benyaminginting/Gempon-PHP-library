<?php
    include ('gempon.php');
    class api extends Gempon {
        private $param;

        function __construct(){
            parent::__construct();
            $this->param = $this->get_param();
        }
		  
        private function parse_json_encoding($data){
            return $data;
            //return substr($data,13);
        }
        
        public function get_user_id(){
            return $this->param['user_id'];
        }
        
        public function get_user_facebook_id(){
            return $this->get_user_data()->facebook_id;
        }
        
		  
        public function get_user_data(){
            /* 
             * Output:
             * first_name,last_name,username,profile_pic,
             * status_message,gender,birthday,location,facebook_id
             * 
             * Cara panggil: $user->first_name, $user->last_name, ...
             */
            $info = $this->get_user_info($this->param['user_name'],'json');            
            $data = json_decode($this->parse_json_encoding($info));
            return $data;
        }
        
        public function get_user_friends_data(){
            /*
             * masih belum jelas isinya apa, blm ada friend soalnya..
             */
            
            $info  = $this->get_user_friends($this->param['user_name'],'json');
            return json_decode($this->parse_json_encoding($info));
        }
        
        public function get_random_user_data(){
            /*
             * Output:
             * username,first_name,last_name,profile_pic,
             * location,status_message,gender,birthday
             */
            
            $info = $this->get_random_user('json');
            return json_decode ($this->parse_json_encoding($info));
        }
        
        public function get_user_current_maja_data(){
            /*
             * Output: current_maja 
             */
            $info = $this->get_user_current_maja($this->param['user_name'],'json');
            return json_decode($this->parse_json_encoding($info));
        }
        
        public function transaction_maja($amount,$description,$format='json'){
            /*
             * Output: 
             *  - status -> "ok", "fail"
             *  - error -> "", "<error_message>"
             */
            
            $info = $this->payment_maja($this->param['user_name'],$amount,$description,$format);
            return json_decode($this->parse_json_encoding($info));
        }

        public function create_notification($message,$link = '',$format='json'){
            /*
             * Output: 
             *  - status -> "ok", "fail"
             *  - notif -> "", "<notif var>"
             */
            $info = $this->send_notification($this->param['user_name'],$message,$link,$format);
            return json_decode($this->parse_json_encoding($info));
        }
    }
?>
