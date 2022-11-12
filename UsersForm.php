<?php
require('./Texto.php');
require('./PostalCode.php');
require('./Correo.php');
require('./DniNie.php');
require('./Phone.php');
require('./BirthDate.php');

    class UsersForm {
        private $nombre;
        private $apellido;
        private $dniNie;
        private $correo;
        private $telefono;
        private $fechaNac;
        private $sexo;
        private $comunidad;
        private $provincia;
        private $codPostal;
        private $domicilio;
        private $descripcion;
        public function __construct(array $array) {
            if(($array) && count($array) > 1){
                $this->nombre = new Texto($array['nombre']);
                $this->apellido = new Texto($array['apellido']);
                $this->dniNie = new DniNie($array['dniNie']);
                $this->correo = new Correo($array['correo']);
                $this->telefono = new Phone($array['telefono']);
                $this->fechaNac = new BirthDate($array['fechaNac']);
                $this->sexo = new Texto($array['sexo']);
                $this->comunidad = new Texto($array['comunidad']);
                $this->provincia = new Texto($array['provincia']);
                $this->codPostal = new PostalCode($array['codPostal']);
                $this->domicilio = new Texto($array['domicilio']);
                $this->descripcion = new Texto($array['descripcion']);
            }else{
                $this->nombre = new Texto();
                $this->apellido = new Texto();
                $this->dniNie = new DniNie();
                $this->correo = new Correo();
                $this->telefono = new Phone();
                $this->fechaNac = new BirthDate();
                $this->sexo = new Texto();
                $this->comunidad = new Texto();
                $this->provincia = new Texto();
                $this->codPostal = new PostalCode();
                $this->domicilio = new Texto();
                $this->descripcion = new Texto();
            }
        }
        public function getAttributes(){
            $thisObject= new ReflectionClass('UsersForm');
            $inputObjects = $thisObject->getProperties();
            return (array_map(fn($inp)=>$inp->name,$inputObjects));
        }
        public function isValid()
        {
            $result=false;
            $counter=0;
            $inputs = $this->getAttributes();
            do{
                $result = $this->{$inputs[$counter]}->isValid()['outcome'];
                $counter++;
            }
            while($result && $counter < count($inputs));
            return $result;
        }
        public function getFailureMessages()
        {
            if(!($this->nombre->isValid()['outcome'] && $this->apellido->isValid()['outcome'] && $this->dniNie->isValid()['outcome'] && $this->correo->isValid()['outcome'] && $this->telefono->isValid()['outcome'] && $this->fechaNac->isValid()['outcome'] && $this->sexo->isValid()['outcome'] && $this->comunidad->isValid()['outcome'] && $this->provincia->isValid()['outcome'] && $this->codPostal->isValid()['outcome'] && $this->domicilio->isValid()['outcome'] && $this->descripcion->isValid()['outcome'])){
                return $this->nombre->isValid()['message'] .'<br>'. $this->apellido->isValid()['message'] .'<br>'. $this->dniNie->isValid()['message'] .'<br>'. $this->correo->isValid()['message'] .'<br>'. $this->telefono->isValid()['message'] .'<br>'. $this->fechaNac->isValid()['message'] .'<br>'. $this->sexo->isValid()['message'] .'<br>'. $this->comunidad->isValid()['message'] .'<br>'. $this->provincia->isValid()['message'] .'<br>'. $this->codPostal->isValid()['message'] .'<br>'. $this->domicilio->isValid()['message'] .'<br>'. $this->descripcion->isValid()['message'];
            }
        }
        public function printData()
        {
            $labels = $this->getAttributes();
            if(!($_POST)){
                print("<form method='post'>");
                foreach ($labels as $key => $label) {
                    print("<label for='$label'> $label </label><br>");
                    print("<input type='text' id='$label' name='$label' value='".$this->$label->getValue()."'><br>");
                }
                print("<input type='submit' value='enviar'></form>");
            }else{
                print("<form method='post'>");
                foreach ($labels as $key => $label) {
                    print("<label for='$label'> $label </label><br>");
                    print("<input type='text' id='$label' name='$label' value='".$this->$label->getValue()."'>");
                    print("<p> <b>".$this->$label->isValid()['message']."</b> </p>");
                }
                print("<input type='submit' value='enviar' name='enviar'></form>");
            }
        }
    
    }
?>