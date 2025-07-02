<?php
class DataBase{

    private $HOSTNAME;
    private $BASEDATOS;
    private $USUARIO;
    private $CLAVE;
    private $CONEXION;
    private $QUERY;
    private $RESULT;
    private $ERROR;


    /**
     * Constructor de la clase que inicia ls variables instancias de la clase
     * vinculadas a la coneccion con el Servidor de BD
     */
    public function __construct(){
        $this->HOSTNAME = "localhost";
        $this->BASEDATOS = "dbviajes";
        $this->USUARIO = "root";
        $this->CLAVE="";
        $this->RESULT=0;
        $this->QUERY="";
        $this->ERROR="";
    }


    /**
     * Funcion que retorna una cadena
     * con una pequeña descripcion del error si lo hubiera
     * @return string
     */
    public function getError(){
        return "\n".$this->ERROR;

    }


    /**
     * Inicia la coneccion con el Servidor y la  Base Datos Mysql.
     * Retorna true si la coneccion con el servidor se pudo establecer y false en caso contrario
     *
     * @return boolean
     */
    public  function iniciar(): bool
    {
        $resp  = false;
        $conexion = mysqli_connect($this->HOSTNAME,$this->USUARIO,$this->CLAVE,$this->BASEDATOS);
        if ($conexion){
            if (mysqli_select_db($conexion,$this->BASEDATOS)){
                $this->CONEXION = $conexion;
                unset($this->QUERY);
                unset($this->ERROR);
                $resp = true;
            }  else {
                $this->ERROR = mysqli_errno($conexion) . ": " .mysqli_error($conexion);
            }
        }else{
            $this->ERROR =  mysqli_errno($conexion) . ": " .mysqli_error($conexion);
        }
        return $resp;
    }
    /**
     * Ejecuta una consulta en la Base de Datos.
     * Recibe la consulta en una cadena enviada por parametro.
     *
     * @param string $consulta
     * @return boolean
     */
    public function ejecutar(string $consulta): bool
    {
        $resp  = false;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if($this->RESULT = mysqli_query( $this->CONEXION,$consulta)){
            $resp = true;
        } else {
            $this->ERROR =mysqli_errno( $this->CONEXION).": ". mysqli_error( $this->CONEXION);
        }
        return $resp;
    }

    /**
     * Devuelve una fila del resultado de la última consulta SELECT ejecutada.
     * Cada vez que se llama, avanza al siguiente registro.
     * Si ya no quedan filas, devuelve null.
     * Útil para recorrer los resultados de una consulta con un while.
     * @return array
     */
    public function registro() {
        $resp = null;
        if ($this->RESULT){
            unset($this->ERROR);
            if($temp = mysqli_fetch_assoc($this->RESULT)){
                $resp = $temp;
            }else{
                mysqli_free_result($this->RESULT);
            }
        }else{
            $this->ERROR = mysqli_errno($this->CONEXION) . ": " . mysqli_error($this->CONEXION);
        }
        return $resp ;
    }

    /**
     * Devuelve el id de un campo autoincrement utilizado como clave de una tabla
     * Retorna el id numerico del registro insertado, devuelve null en caso que la ejecucion de la consulta falle
     *
     * @param string $consulta
     * @return int id de la tupla insertada, null en caso de error.
     */
    public function devuelveIDInsercion($consulta){
        $resp = null;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if ($this->RESULT = mysqli_query($this->CONEXION,$consulta)){
            $resp = mysqli_insert_id($this->CONEXION);
        } else {
            $this->ERROR =mysqli_errno( $this->CONEXION) . ": " . mysqli_error( $this->CONEXION);
        }
        return $resp;
    }

    public function cerrarConexion() {
        if ($this->RESULT) {
            mysqli_free_result($this->RESULT); // Libera la memoria del resultado
            $this->RESULT = null;
        }

        if ($this->CONEXION) {
            mysqli_close($this->CONEXION); // Cierra la conexión a la base de datos
            $this->CONEXION = null;
        }
    }

}