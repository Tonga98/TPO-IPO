<?php
class Empresa{
    // Atributos
    private int $idEmpresa;
    private string $nombre;
    private string $direccion;

    // Metodos
    public function __construct(string $nombre, string $direccion ){
        $this->nombre = $nombre;
        $this->direccion = $direccion;
    }
    // GETTERS
    public function getIdEmpresa(): int{
        return $this->idEmpresa;
    }

    public function getNombre(): string{
        return $this->nombre;
    }

    public function getDireccion(): string{
        return $this->direccion;
    }

    // SETTERS
    public function setIdEmpresa(int $idEmpresa): void{
        $this->idEmpresa = $idEmpresa;
    }

    public function setNombre(string $nombre): void{
        $this->nombre = $nombre;
    }

    public function setDireccion(string $direccion): void{
        $this->direccion = $direccion;
    }

    public function __toString(): string{
        $numEmpresa = $this->getIdEmpresa();
        $nombre = $this->getNombre();
        $direccion = $this->getDireccion();

        return
            "Número empresa: $numEmpresa\n".
            "Nombre: $nombre\n".
            "Dirección: $direccion\n".
            "-----------------------------\n";
    }

    /**
     * Función para buscar empresa segun idEmpresa.
     * Retorna true si la encuentra, falso caso contrario.
     * @param int $idEmpresa
     * @return bool
     * @throws Exception
     */
    public function buscar(int $idEmpresa): bool{
        $dataBase = new DataBase();
        $consulta = "SELECT * FROM empresa WHERE idEmpresa = '" . $idEmpresa . "'";
        $respuesta = false;

        if($dataBase->iniciar()) {
            if($dataBase->ejecutar($consulta)){
                // Mientras $fila tenga valor el if se ejecuta
                if ($fila = $dataBase->registro()) {
                    $this->setIdEmpresa($idEmpresa);
                    $this->setNombre($fila['nombre']);
                    $this->setDireccion($fila['direccion']);

                    $respuesta = true;
                }
            }else{
                throw new Exception ($dataBase->getError());
            }
        }else{
            throw new Exception ($dataBase->getError());
        }

        return $respuesta;
    }

    /**
     * Función para listar toda la tabla Empresa
     * @return array
     * @throws Exception
     */
    public static function listar(): array{
        $arrayEmpresa = [];
        $dataBase = new DataBase();
        $consulta = "SELECT * FROM empresa";

        if($dataBase->iniciar()) {
            if($dataBase->ejecutar($consulta)){
                // Mientras $fila tenga valor el if se ejecuta
                while ($fila = $dataBase->registro()) {
                    $objEmpresa = new Empresa(
                        $fila['nombre'],
                        $fila['direccion']
                    );
                    $objEmpresa->setIdEmpresa($fila['idEmpresa']);

                    $arrayEmpresa[] = $objEmpresa;
                }
            }else{
                throw new Exception ($dataBase->getError());
            }
        }else{
            throw new Exception ($dataBase->getError());
        }

        return $arrayEmpresa;
    }

    /**
     * Función para insertar registro de Empresa.
     * llama la funcion devuelveIDInsercion() que ejecuta la consulta, no hace falta llamar ejecutar()
     * Retorna true en caso de éxito
     *
     * @return bool
     * @throws Exception
     */
    public function insertar(): bool{
        $dataBase = new DataBase();
        $respuesta = false;
        $consulta = "INSERT INTO empresa(nombre,direccion) VALUES (
                                              '".$this->getNombre()."', '".$this->getDireccion()."'
                                              )";
        if($dataBase->iniciar()) {
            $idInsertado = $dataBase->devuelveIDInsercion($consulta);

            if($idInsertado !== null){
               // $this->setIdEmpresa($idInsertado);
                $respuesta = true;
            }else{
                throw new Exception ($dataBase->getError());
            }
        }else{
            throw new Exception ($dataBase->getError());
        }

        return $respuesta;
    }

    /**
     * Función para modificar datos de Empresa.
     * Retorna true en caso de éxito
     * @param string $nombre
     * @param string $direccion
     * @return bool
     * @throws Exception
     */
    public function modificar(): bool{
        $dataBase = new DataBase();
        $nombre = $this->getNombre();
        $direccion = $this->getDireccion();
        $idEmpresa = $this->getIdEmpresa();

        if ($direccion == "" && $nombre != ""){
            $consulta = "UPDATE empresa
        SET nombre = '".$nombre."'";
        }else if($nombre == "" && $direccion != ""){
        $consulta = "UPDATE empresa
        SET direccion = '".$direccion."'";
        }else{
            $consulta = "UPDATE empresa
        SET direccion = '".$direccion."', nombre = '".$nombre."'";
        }
        $consulta .= " WHERE idEmpresa = '".$idEmpresa."'";


        if($dataBase->iniciar()) {
            if($dataBase->ejecutar($consulta)){
                $respuesta = true;
            }else{
                throw new Exception ($dataBase->getError());
            }
        }else{
            throw new Exception ($dataBase->getError());
        }

        return $respuesta;
    }

    /**
     * Función para eliminar registro de Empresa.
     * Retorna true en caso de éxito
     *
     * @return bool
     * @throws Exception
     */
    public function eliminar(): bool{
        $respuesta = false;
        $dataBase = new DataBase();

        try{
        if($dataBase->iniciar()) {
            $consulta =
                "DELETE FROM empresa
                WHERE idEmpresa = ".$this->getIdEmpresa().";"
            ;

            if($dataBase->ejecutar($consulta)){
                $respuesta = true;
            }else{
                throw new Exception ($dataBase->getError());
            }
        }else{
            throw new Exception($dataBase->getError());
        }
        }catch(mysqli_sql_exception $e){
            throw new Exception("Error de restricción: no se puede eliminar porque está siendo usado por un viaje.");
        }

        return $respuesta;
    }
}
