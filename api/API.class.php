<?php
namespace mep {
    abstract class API
    {
        //welche HTTP-Methode?
        /**
         * Property: method
         * The HTTP method this request was made in, either GET, POST, PUT or DELETE
         */
        protected $method = '';

        //welcher Endpoint?
        /**
         * Property: endpoint
         * The Model requested in the URI. eg: /files
         */
        protected $endpoint = '';

        //zusaetzliche Beschreibung ueber Basismethode hinaus
        /**
         * Property: verb
         * An optional additional descriptor about the endpoint, used for things that can
         * not be handled by the basic methods. eg: /files/process
         */
        protected $verb = '';

        //zusaetzliche URI-Bestandteile
        /**
         * Property: args
         * Any additional URI components after the endpoint and verb have been removed, in our
         * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
         * or /<endpoint>/<arg0>
         */
        protected $args = Array();

        //zu speichernde Datei (PUT)
        /**
         * Property: file
         * Stores the input of the PUT request
         */
        protected $file = NULL;

        /**
         * Constructor: __construct
         * Vorverarbeitung der Daten
         */
        public function __construct($request) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: *");
            header("Content-Type: application/json");

            $this->args = explode('/', rtrim($request, '/'));
            $this->endpoint = array_shift($this->args);

            if (array_key_exists(0, $this->args)&&!is_numeric($this->args[0])) {    #URI-Elemente werden im args-Array gespeichert und hier weiterverteilt!!
                #$this->verb = array_shift($this->args);
            }

            $this->method = $_SERVER['REQUEST_METHOD'];     #Anforderungsmethode wird gelesen, wenn diese POST ist, wird sie noch genauer ausgelesen (ff)
            if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                switch($_SERVER['HTTP_X_HTTP_METHOD']):
                    case 'DELETE':
                        $this->method = 'DELETE';
                        break;
                    case 'PUT':
                        $this->method = 'PUT';
                        break;
                    default:
                        throw new \Exception("Unexpected Header");
                        break;
                endswitch;
            }

            switch($this->method):
                case 'DELETE':                                                      //alle uebergebenen Requests werden in das request-Array verteilt.
                case 'POST':
                    $this->request = $this->_cleanInputs($_POST);
                    break;
                case 'GET':
                    $this->request = $this->_cleanInputs($_GET);
                    break;
                case 'PUT':
                    $this->request = $this->_cleanInputs($_GET);
                    $this->file = file_get_contents("php://input");         //PUT-File wird gelesen
                    break;
                default:
                    $this->_response('Invalid Method', 405);
                    break;
            endswitch;
        }

        public function processAPI() {                                                          //Anforderung wird umgesetzt, wenn sie existiert
            if((int)method_exists($this, $this->endpoint)>0) {
                return $this->_response($this->{$this->endpoint}($this->args));
            }
            return $this->_response("No Endpoint: $this->endpoint", 404);
        }

        private function _response($data, $status = 200) {
            header("HTTP/1.1 ". $status . " " . $this->_requestStatus($status));
            return json_encode($data);
        }

        private function _cleanInputs($data) {                                  //Eingaben werden getrimt und wenn noetig in Array verteilt
            $clean_input = Array();
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $clean_input[$k] = $this->_cleanInputs($v);
                }
            } else {
                $clean_input = trim(strip_tags($data));
            }
            return $clean_input;
        }

        private function _requestStatus($code) {            //Fehlermeldungen definiert.
            switch($code):
                case 200:
                    return "OK";
                case 404:
                    return "Not found";
                case 405:
                    return "Method not allowed";
                default:
                    return "Internal Server Error";
            endswitch;
        }
    }
}