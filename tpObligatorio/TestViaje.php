<?php

include_once 'BaseDatos.php';
include_once 'Empresa.php';
include_once 'Pasajero.php';
include_once 'ResponsableV.php';
include_once 'Viaje.php';
include_once 'TestViaje.php';
require_once 'PasajeroViaje.php';

$seguir = true;

while ($seguir){
    do{
        //Mostrar menu
        echo menu();
        $opcion = trim(fgets(STDIN));

        switch ($opcion){
            case 1:
                //Listar empresas
                $empresas = Empresa::listar();
                if (count($empresas) > 0) {
                    foreach ($empresas as $empresa) {
                        echo $empresa;
                    }
                }else{
                    echo "No se encontraron empresas.\n";
                }
                break;
            case 2:
                //Ingresar empresa
                echo "Ingrese el nombre de la empresa:";
                $nombre = trim(fgets(STDIN));
                echo "\nIngrese la direccion de la empresa:";
                $direccion = trim(fgets(STDIN));
                $empresa = new Empresa($nombre, $direccion);
                try {
                    $empresa->insertar();
                }catch (Exception $e){
                    echo "Error al ingresar empresa: ".$e->getMessage()."\n";
                }

                break;
            case 3:
                //Modificar empresa
                echo "Ingrese el id de la empresa que desea modificar: \n";
                    $idEmpresa = trim(fgets(STDIN));
                $empresa = new Empresa('','');
                if($empresa->buscar($idEmpresa)) {
                    $empresa->setIdEmpresa($idEmpresa);
                echo "Que desea modificar: \n".
                    "1) Nombre.\n".
                    "2) Direccion.\n".
                    "3) Ambos.\n".
                    "4) Cancelar.\n".
                    "Eleccion: ";
                $opcion = trim(fgets(STDIN));
                if ($opcion == 1 || $opcion == 3){
                    echo "Ingrese el nuevo nombre: ";
                    $nombre = trim(fgets(STDIN));
                    $empresa->setNombre($nombre);
                }
                if ($opcion == 2 || $opcion == 3){
                    echo "Ingrese la nueva direccion: ";
                    $direccion = trim(fgets(STDIN));
                    $empresa->setDireccion($direccion);
                }

                $empresa->modificar();
                }else{
                    echo "Empresa no encontrada.\n";
                }
                break;
            case 4:
                //Eliminar empresa
                echo "Ingrese el id de la empresa que desea eliminar: \n";
                $idEmpresa = trim(fgets(STDIN));
                $empresa = new Empresa('','');

                try {

                    if ($empresa->buscar($idEmpresa)) {
                        $empresa->eliminar();
                        echo "Empresa eliminada con exito.\n";
                    } else {
                        echo "Empresa no encontrada.\n";
                    }
                }catch(Exception $e){
                    echo "Error al elimnar empresa: " .$e->getMessage()."\n";
                }
                break;
            case 5:
                //Listar responsables
                $responsables = ResponsableV::listar();
                if (count($responsables) > 0) {
                    foreach ($responsables as $responsable) {
                        echo $responsable;
                    }
                } else {
                    echo "No se encontraron responsables.\n";
                }
                break;
            case 6:
                //Ingresar responsable
                echo "Ingrese el nombre del responsable: \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese el apellido: \n";
                $apellido = trim(fgets(STDIN));
                echo "Ingrese el numero de su licencia: \n";
                $numLicencia = trim(fgets(STDIN));
                $responsable = new ResponsableV($numLicencia, $nombre, $apellido);
                $responsable->insertar();
                break;
            case 7:
                //Modificar Responsable
                echo "Ingrese el numero de empleado del responsable que desea modificar: \n";
                $numEmpleado = trim(fgets(STDIN));
                $responsable = new ResponsableV('','','');
                if ($responsable->buscar($numEmpleado)) {
                    $responsable->setNumeroEmpleado($numEmpleado);
                    echo "Que desea modificar: \n".
                        "1) Numero licencia.\n".
                        "2) Nombre.\n".
                        "3) Apellido.\n".
                        "4) Todo.\n".
                        "5) Cancelar.\n".
                        "Eleccion: ";
                    $opcion = trim(fgets(STDIN));
                    if ($opcion == 2 || $opcion == 4){
                        echo "Ingrese el nuevo nombre: ";
                        $nombre = trim(fgets(STDIN));
                        $responsable->setNombre($nombre);
                    }
                    if ($opcion == 1 || $opcion == 4){
                        echo "Ingrese el nuevo numero de licencia: ";
                        $numLicencia = trim(fgets(STDIN));
                        $responsable->setNumeroLicencia($numLicencia);
                    }
                    if ($opcion == 3 || $opcion == 4){
                        echo "Ingrese el nuevo apellido: ";
                        $apellido = trim(fgets(STDIN));
                        $responsable->setApellido($apellido);
                    }

                    $responsable->modificar();
                }else{
                    echo "Responsable no encontrado.\n";
                }
                break;
            case 8:
                //Eliminar responsable
                echo "Ingrese el numero de empleado del responsable que desea eliminar: \n";
                $numEmpleado = trim(fgets(STDIN));
                $responsable = new ResponsableV('','','');

                try {

                    if ($responsable->buscar($numEmpleado)) {
                        $responsable->setNumeroEmpleado($numEmpleado);
                        $responsable->eliminar();
                        echo "Responsable eliminado con exito.\n";
                    } else {
                        echo "Responsable no encontrado.\n";
                    }
                }catch (Exception $e){
                    echo "Error al eliminar responsable: ".$e->getMessage()."\n";
                }
                break;
            case 9:
                //Listar Viajes
                $viajes = Viaje::listar();
                if (count($viajes) > 0) {
                    foreach ($viajes as $viaje) {
                        echo $viaje."\n";
                        $viaje->cargarPasajeros();
                        if ($pasajeros = $viaje->getColPasajeros()){
                            echo "------PASAJEROS------\n";
                            foreach ($pasajeros as $pasajero){
                                echo $pasajero;
                            }
                        }else{
                            echo "No se encontraron pasajeros en el viaje.\n";
                        }


                    }
                } else {
                    echo "No se encontraron viajes.\n".
                        "---------------------------\n";
                }
                break;
            case 10:
                //Ingresar viaje
                echo "Ingrese el destino del viaje: \n";
                $destino = trim(fgets(STDIN));
                echo "Ingrese la cantidad máxima de pasajeros para el viaje: \n";
                $cantMaxPasajeros = trim(fgets(STDIN));
                echo "Ingrese el importe del viaje: \n";
                $importe = trim(fgets(STDIN));
                echo "Ingrese el ID de la empresa: \n";
                $idEmpresa = trim(fgets(STDIN));
                echo "Ingrese el número de empleado: \n";
                $numeroEmpleado = trim(fgets(STDIN));

                //Obtener objeto responsable
                $responsable = new ResponsableV('','','');


                //Obtener objeto empresa
                $empresa = new Empresa('','');

                if ($empresa->buscar($idEmpresa)) {
                    if ($responsable->buscar($numeroEmpleado)){
                    $viaje = new Viaje($destino, $cantMaxPasajeros, $empresa, $responsable, $importe);
                    try {
                        $viaje->insertar();
                    } catch (Exception $e) {
                        echo "Error al ingresar viaje: " . $e->getMessage() . "\n";
                    }
                        }else{
                        echo "No se encontro el responsable.\n";
                    }
                }else{
                    echo "No se encontro la empresa.\n";
                }

                break;
            case 11:
                // Modificar Viaje
                echo "Ingrese el ID del viaje que desea modificar: \n";
                $idViaje = trim(fgets(STDIN));
                $responsable = new ResponsableV('','','');
                $empresa = new Empresa('','');
                $viaje = new Viaje('', '', $empresa, $responsable, '');
                $error = false;

                if ($viaje->buscar($idViaje)) {

                    echo "Que desea modificar: \n" .
                        "1) Destino\n" .
                        "2) Cantidad máxima de pasajeros\n" .
                        "3) ID Empresa\n" .
                        "4) Número de empleado responsable\n" .
                        "5) Importe\n" .
                        "6) Todo\n" .
                        "7) Cancelar\n" .
                        "Elección: ";

                    $opcion = trim(fgets(STDIN));

                    if ($opcion == 1 || $opcion == 6) {
                        echo "Ingrese el nuevo destino: ";
                        $destino = trim(fgets(STDIN));
                        $viaje->setDestino($destino);
                    }
                    if ($opcion == 2 || $opcion == 6) {
                        echo "Ingrese la nueva cantidad máxima de pasajeros: ";
                        $cant = trim(fgets(STDIN));
                        $viaje->setCantMaxPasajeros($cant);
                    }
                    if ($opcion == 3 || $opcion == 6) {
                        echo "Ingrese el nuevo ID de la empresa: ";
                        $idEmpresa = trim(fgets(STDIN));
                        if ($empresa->buscar($idEmpresa)) {
                            $viaje->setEmpresa($empresa);
                        }else{
                            echo "Empresa no encontrada.\n";
                            $error = true;
                        }
                    }
                    if ($opcion == 4 || $opcion == 6 && !$error) {
                        echo "Ingrese el nuevo número de empleado responsable: ";
                        $numResponsable = trim(fgets(STDIN));
                        if ($responsable->buscar($numResponsable)) {
                            $viaje->setResponsable($responsable);
                        }else{
                            echo "Responsable no encontrado.\n";
                            $error = true;
                        }

                    }
                    if ($opcion == 5 || $opcion == 6 && !$error) {
                        echo "Ingrese el nuevo importe: ";
                        $importe = trim(fgets(STDIN));
                        $viaje->setImporte($importe);
                    }

                    if ($opcion != 7 && !$error) {
                        try {
                            if ($viaje->modificar()) {
                                echo "Viaje modificado correctamente.\n";
                            }
                        } catch (Exception $e) {
                            echo "Error al modificar el viaje: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "Modificación cancelada.\n";
                    }
                } else {
                    echo "Viaje no encontrado.\n";
                }
                break;
            case 12:
                //Eliminar viaje
                echo "Ingrese el ID del viaje que desea eliminar: \n";
                $idViaje = trim(fgets(STDIN));
                $responsable = new ResponsableV('','','');
                $empresa = new Empresa('','');
                $viaje = new Viaje('','',$empresa,$responsable,'');

                try {

                    if ($viaje->buscar($idViaje)) {
                        $viaje->eliminar();
                        echo("El viaje se elimino con exito.\n");
                    } else {
                        echo("El viaje con ID: $idViaje no se encontro.\n");
                    }
                }catch (Exception $e){
                    echo "Error al eliminar el viaje: ".$e->getMessage()."\n";
                }
                break;
            case 13:
                //Listar pasajeros
                $pasajeros = Pasajero::listar();

                if (count($pasajeros) > 0) {
                    foreach ($pasajeros as $pasajero) {
                        echo $pasajero;
                    }
                } else {
                    echo "No se encontraron pasajeros.\n";
                }
                break;
            case 14:
                //Ingresar pasajero
                echo "Ingrese el nombre del pasajero: \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese el apellido: \n";
                $apellido = trim(fgets(STDIN));
                echo "Ingrese el numero de documento: \n";
                $numDocumento = trim(fgets(STDIN));
                echo "Ingrese el numero de telefono: \n";
                $telefono = trim(fgets(STDIN));


                    $pasajero = new Pasajero($numDocumento,$nombre,$apellido,$telefono);

                    if ($pasajero->insertar()) {
                        echo "Se ingreso el pasajero\n";
                    }
                break;
            case 15:
                // Modificar Pasajero
                echo "Ingrese el número de documento del pasajero que desea modificar: \n";
                $numDoc = trim(fgets(STDIN));

                $pasajero = new Pasajero('', '', '', '');
                if ($pasajero->buscar($numDoc)) {

                    echo "¿Qué desea modificar?: \n" .
                        "1) Nombre\n" .
                        "2) Apellido\n" .
                        "3) Teléfono\n" .
                        "5) Todo\n" .
                        "6) Cancelar\n" .
                        "Elección: ";
                    $opcion = trim(fgets(STDIN));

                    if ($opcion == 1 || $opcion == 5) {
                        echo "Ingrese el nuevo nombre: ";
                        $nuevoNombre = trim(fgets(STDIN));
                        $pasajero->setNombre($nuevoNombre);
                    }

                    if ($opcion == 2 || $opcion == 5) {
                        echo "Ingrese el nuevo apellido: ";
                        $nuevoApellido = trim(fgets(STDIN));
                        $pasajero->setApellido($nuevoApellido);
                    }

                    if ($opcion == 3 || $opcion == 5) {
                        echo "Ingrese el nuevo teléfono: ";
                        $nuevoTelefono = trim(fgets(STDIN));
                        $pasajero->setTelefono($nuevoTelefono);
                    }

                    if ($opcion != 6) {
                        try {
                            if ($pasajero->modificar()) {
                                echo "Pasajero modificado correctamente.\n";
                            } else {
                                echo "No se pudo modificar el pasajero.\n";
                            }
                        } catch (Exception $e) {
                            echo "Error al modificar el pasajero: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "Modificación cancelada.\n";
                    }
                } else {
                    echo "Pasajero no encontrado.\n";
                }

                break;
            case 16:
                //Eliminar pasajero
                echo "Ingrese el numero de documento del pasajero que desea eliminar: \n";
                $numDoc = trim(fgets(STDIN));
                $pasajero = new Pasajero($numDoc,'','','');
                if ($pasajero->buscar($numDoc)) {
                    $pasajero->eliminar();
                    echo("El pasajero se elimino con exito.\n");
                } else {
                    echo("El pasajero no se encontro.\n");
                }
                break;
            case 17:
                //Ingresar un viaje pasajero
                $empresa = new Empresa('','');
                $responsable = new ResponsableV('','','');
                $pasajero = new Pasajero('','','','');
                $viaje = new Viaje('','',$empresa,$responsable,'');

                echo "Ingrese el numero de documento del pasajero: \n";
                $numDocumento = trim(fgets(STDIN));
                echo "Ingrese el numero de viaje: \n";
                $idViaje = trim(fgets(STDIN));

                if ($pasajero->buscar($numDocumento)){
                    if($viaje->buscar($idViaje)){
                        $pasajeroViaje = new PasajeroViaje($viaje,$pasajero);
                        if (!$pasajeroViaje->yaExiste()){
                            $cantMaxPasajeros = $viaje->getCantMaxPasajeros();
                            $pasajerosEnViaje = $pasajeroViaje->cantPasajerosEnViaje();
                            if ($pasajerosEnViaje < $cantMaxPasajeros){
                                $pasajeroViaje->insertar();
                                echo "Pasajero ingresado al viaje.\n";
                            }else{
                                echo "Error: Cantidad maxima de pasajeros alcanzada.\n";
                            }
                        }else{
                            echo "El pasajero ya se encuentra cargado en el viaje.";
                        }

                    }else{
                        echo "No se encontro el viaje.\n";
                    }
                }else{
                    echo "No se encontro al pasajero.\n";
                }
                break;
            case 18:
                $seguir = false;
                break;
            default:
                echo "\nLa opcion ingresada es invalida.\n";
                break;
        }
    }while($opcion > 18 || $opcion == 0);
}
function menu()
{

    $cadena = (
        "----------------------------------------------------------------------------------------------------".
        "\n                                            MENU                                            \n" .
        "----------------------------------------------------------------------------------------------------\n".
        "1- Listar Empresas.\n" .
        "2- Ingresar Empresa.\n" .
        "3- Modificar Empresa.\n" .
        "4- Eliminar Empresa.\n" .
        "5- Listar Responsables.\n" .
        "6- Ingresar ResponsableV.\n" .
        "7- Modificar ResponsableV.\n" .
        "8- Eliminar ResponsableV.\n" .
        "9- Ver Viajes.\n" .
        "10- Ingresar Viaje.\n" .
        "11- Modificar Viaje.\n" .
        "12- Eliminar Viaje.\n" .
        "13- Ver Pasajeros.\n" .
        "14- Ingresar Pasajero.\n" .
        "15- Modificar Pasajero.\n" .
        "16- Eliminar Pasajero.\n" .
        "17- Ingresar pasajero en viaje\n".
        "18- Terminar\n".
        "----------------------------------------------------------------------------------------------------\n".
        "Opcion: "
    );
    return $cadena;
}