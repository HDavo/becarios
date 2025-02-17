<?php
require('./Texto.php');
require('./PostalCode.php');
require('./Correo.php');
require('./Nif.php');
require('./Phone.php');
require('./BirthDate.php');
require('./Name.php');
require('./Parrafo.php');

    class UsersForm {
        private $nombre;
        private $apellido;
        private $nif;
        private $correo;
        private $telefono;
        private $fechaNac;
        private $genero;
        private $localidad;
        private $provincia;
        private $codPostal;
        private $domicilio;
        private $descripcion;
            //distinguimos el tipo de objeto generado 
        public function __construct(array $array) {
            if(!empty($array) && count($array) > 1){
                $this->nombre = new Name($array['nombre']);
                $this->apellido = new Name($array['apellido']);
                $this->nif = new Nif($array['nif']);
                $this->correo = new Correo($array['correo']);
                $this->telefono = new Phone($array['telefono']);
                $this->fechaNac = new BirthDate($array['fechaNac']);
                $this->genero = new Texto($array['genero']);
                $this->localidad = new Texto($array['localidad']);
                $this->provincia = new Texto($array['provincia']);
                $this->codPostal = new PostalCode($array['codPostal']);
                $this->domicilio = new Texto($array['domicilio']);
                $this->descripcion = new Parrafo($array['descripcion']);
            }else{
                $this->nombre = new Name();
                $this->apellido = new Name();
                $this->nif = new Nif();
                $this->correo = new Correo();
                $this->telefono = new Phone();
                $this->fechaNac = new BirthDate();
                $this->genero = new Texto();
                $this->localidad = new Texto(); 
                $this->provincia = new Texto();
                $this->codPostal = new PostalCode();
                $this->domicilio = new Texto();
                $this->descripcion = new Parrafo();
            }
        }
        /* Busca una clase coincidente con el string y crea un objeto reflection class, en donde se "copian" las propiedades de ese objeto
        (obtiene los métodos y atributos de la clase sobre la que se aplica, en forma de objetos)*/
        public function getAttributes(){
            $thisObject= new ReflectionClass('UsersForm');
            $inputObjects = $thisObject->getProperties();
            //métodos y atributos capturados (en forma de objeto)
            return (array_map(fn($inp)=>$inp->name,$inputObjects)); //se guardan los atributos y métodos recuperados con ReflectionClass
        }
        public function isValid()
        {
            $result=false;
            $counter=0;
            $inputs = $this->getAttributes(); //uso de los atributos capturados arriba
            do{ //validación automática de cada tipo de input
                $result = $this->{$inputs[$counter]}->isValid()['outcome']; //se guarda el resultado de la validación, posición a posición
                $counter++;
            }
            while($result && $counter < count($inputs)); //el bucle si hay algo no válido
            return $result;
        }
        public function printFormData()
        {
            $labels = $this->getAttributes(); //uso de los atributos capturados arriba
            $head = <<< EOF
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel='stylesheet' href='./extraFiles/styles.css'>
                    <script src='./extraFiles/index.js' type='text/javascript'></script>
                    <title>Formulario de alta de becarios</title>
                </head>
                <body>
                    <div class='Titulo'>Formulario <br> de registro de Becarios</div>
                    <form method='post' class='insercionBecarios'>
            EOF;  //la parte intermedia del formulario se hace en función de $_POST
            $foot = <<< EOF
                        <div class='enviar'>
                            <input type='submit' value='ENVIAR' id='enviar' name='enviar'>
                        <div>
                    </form>
                </body>
            </html>
            EOF;
            $generos = [['id'=>'Indefinido','value'=>'indefinido'],['id'=>'Femenino','value'=>'femenino'],['id'=>'Masculino','value'=>'masculino']];
            $provincias = ['-- Seleccione provincia --','Albacete','Alicante','Almería','Álava','Asturias','Ávila','Badajoz','Islas Baleares','Barcelona','Bizkaia','Burgos','Cáceres','Cádiz','Cantabria','Castellón','Ciudad Real','Córdoba','A Coruña','Cuenca','Gipuzkoa','Girona','Granada','Guadalajara','Huelva','Huesca','Jaén','León','Lleida','Lugo','Madrid','Málaga','Murcia','Navarra','Ourense','Palencia','Las Palmas','Pontevedra','La Rioja','Salamanca','Santa Cruz de Tenerife','Segovia','Sevilla','Soria','Tarragona','Teruel','Toledo','Valencia','Valladolid','Zamora','Zaragoza','Ceuta','Melilla'];

            if(empty($_POST)){ //Impresión del formulario vacío
                print($head);
                foreach ($labels as $key => $label) {
                    print("<div class='$label'>");
                    print("<label for='$label'>".(($label == 'fechaNac')?strtoupper('fecha de nacimiento'):(($label == 'codPostal')?strtoupper('codigo postal'):(strtoupper($label))))."</label>");
                    switch($label){
                        case 'codPostal':
                            print("<input type='number' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'telefono':
                            print("<input type='number' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'correo':
                            print("<input type='email' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'fechaNac':
                            print("<input type='date' min='1962-01-01' max='2004-12-31' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'genero':
                            foreach($generos as $genero){
                                print("<div><input type='radio' id='".$genero['value']."' name='$label' value='".$genero['value']."' ".(($genero['value'] == 'indefinido')?'checked':'')."><label for='".$genero['value']."'>".$genero['id']."</label></div>");
                                //busca indefinido para ponerlo como valor inicial
                            }
                        break;
                        case 'provincia':
                            print("<select id='".$label."' name=".$label.">");
                            foreach ($provincias as $key => $provincia) {
                                print("<option value='".(($provincia != '-- Seleccione provincia --')?strtolower($provincia):'')."' ".(($this->{$label}->getValue() == strtolower($provincia))?'selected':'').">".$provincia."</option>");
                            }
                            print('</select>');
                        break;
                        case 'descripcion':
                            print("<textarea id='$label' name='$label' value='".$this->{$label}->getValue()."'></textarea>");
                        break;
                        default: //usamos default para los inputs no específicos (correspondientes a la clase Texto)
                        print("<input type='text' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                    }
                    print("</div>");
                }
                print($foot);
            }else{ //para imprimir el formulario una vez se han metido datos
                print($head);
                foreach ($labels as $key => $label) {
                    print("<div class='$label'>");
                    print("<label for='$label'>".(($label == 'fechaNac')?strtoupper('fecha de nacimiento'):(($label == 'codPostal')?strtoupper('codigo postal'):(strtoupper($label))))."</label>");
                    switch($label){
                        case 'codPostal':
                            print("<input type='number' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'telefono':
                            print("<input type='number' id='$label' name='$label' value='".$this->{$label}->getValue()."' maxlength='9'>");
                        break;
                        case 'correo':
                            print("<input type='email' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                        case 'fechaNac':
                            print("<input type='date' min='1962-01-01' max='2004-12-31' id='$label' name='$label' value='".$this->$label->getValue()."'>");
                        break;
                        case 'genero':
                            foreach($generos as $genero){
                                print("<div><input type='radio' id='".$genero['value']."' name='$label' value='".$genero['value']."' ".(($this->{$label}->getValue() == $genero['value'])?'checked':'')."><label for='".$genero['value']."'>".$genero['id']."</label></div>");
                            }
                            //coincidencia entre valores enviado y el que se imprime genera la selección
                        break;
                        case 'provincia':
                            print("<select id='".$label."' name=".$label.">");
                            foreach ($provincias as $key => $provincia) {
                                print("<option value='".(($provincia != '-- Seleccione provincia --')?strtolower($provincia):'')."' ".(($this->{$label}->getValue() == strtolower($provincia))?'selected':'').">".$provincia."</option>");
                            }
                            print('</select>');
                        break;
                        case 'descripcion':
                            print("<textarea id='$label' name='$label'>".$this->{$label}->getValue()."</textarea>");
                        break;
                        default: //para inputs no específicos
                                print("<input type='text' id='$label' name='$label' value='".$this->{$label}->getValue()."'>");
                        break;
                    }
                    ($this->{$label}->isValid()['outcome'])?print("<p> <b>".$this->{$label}->isValid()['message']."</b> </p> </div>"):print("<p> <i>".$this->{$label}->isValid()['message']."</i> </p> </div>");
                }
                print($foot);
            }
        }
        public function saveBecario()
        {
            $inputs = $this->getAttributes();
            $totalInputs = count($inputs);
            //estructura en plantillas
            $head = <<< EOF
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel='stylesheet' href='./extraFiles/morestyles.css'>
                    <title>Formulario de alta de becarios</title>
                </head>
                <body>
                    <div class='added' id='added'>
                        <div class='addedTitle'>BECARIO AÑADIDO EXITOSAMENTE!</div>
                        <div class='addedCont' id='addedCont'>

            EOF;
            //se pone el último becario introducido
            $midle = <<< EOF
                            <div class='input12'>
                                <input type='button' id='listaB' class='nuevoBec' value='Listado de becarios'>
                            </div>
                        </div>
                        </div>
                    <div id='listadoDeBecarios'>
            EOF;
            //se ponen los becarios ya existentes
            $foot = <<< EOF
                        <a href='./index.php'>
                            <input type='button' id='nuevoB' class='nuevoBec' value='Añadir otro becario'>
                        </a>
                    </div>
                </body>
                <script>
                let listaB = document.getElementById('listaB'),
                    contListaB = document.getElementById('listadoDeBecarios'),
                    added = document.getElementById('added');;
                    listaB.addEventListener('click',()=>{
                        contListaB.style.display = 'block';
                        added.style.display = 'none';
                    });
                </script>
            </html>
            EOF;

            $fichero = './listadoBecarios/becarios.csv'; // Abre el fichero para obtener el contenido existente
            $contenido = file_get_contents($fichero); // Añade una nueva persona al fichero
            foreach ($inputs as $key => $input) {
                $contenido .= $this->{$input}->getValue().(($key != ($totalInputs - 1))?";":"\n");
            }
            file_put_contents($fichero, $contenido); // Escribe el contenido al fichero

            print($head);
            foreach($inputs as $key => $input){
                print("<p class=input".$key."><b>".(strtoupper($input)).":</b> <i>". (strtoupper($this->{$input}->getValue()))."</i></p>");
            }
            print($midle);
            $this->readThisFile($fichero);
            print($foot);

            unset($_POST);
        }

        public function readThisFile ($path){
            $inputs = $this->getAttributes();
            $aperturaFichero = fopen($path, 'r');
                print("<table>");
                foreach($inputs as $inp){
                    print("<th>".$inp."</th>");
                }
                while(!feof($aperturaFichero)){
                    $obtener = fgets($aperturaFichero); // Leyendo, extrayendo y almacenando una linea
                    print("<tr>");
                    foreach(explode(';',$obtener) as $key => $campo){
                        if($campo != '' && $campo != ' ' && $campo != '\n'){
                            print("<td ".(($key%2 !=0)?"class='greyTd'":"class='lightGreyTd'").">".((strlen($campo) > 100)?substr($campo,0,25).'...':$campo)."</td>");
                        } // Imprimiendo cada campo de la linea
                    }
                    print("</tr>");
                }
                fclose($aperturaFichero); // Cerrando el archivo
                print("</table>");
        }
    
    }
?>