<?php
/**
 * Created by PhpStorm.
 * User: DIAZ
 * Date: 24/07/2015
 * Time: 11:12 PM
 */
include_once 'C:\xampp\htdocs\T4rio\back-end\FabricaDao.php';
include_once 'C:\xampp\htdocs\T4rio\back-end\FabricaEntidad.php';
class DaoUsuario {

    private $Usuario;

    const STRING_SQL = ' ( nombre, apellido, cedula, correo, tipo, clave )';

    const STRING_PARAMETROS = '( :nombre, :apellido, :cedula, :correo, :tipo, :clave )';

    const STRING_MODIFICA = '
     nombre	= :nombre,
     apellido = :apellido,
     cedula = :cedula,
     correo = :correo,
     tipo = :tipo,
     clave = :clave ';

    const TABLA = ' usuario ';


    function __construct( Usuario $usuario)
    {
        $this->Usuario = $usuario;
    }


    public function agregar()
    {
        $conexion = FabricaEntidad::Conexion();;
        if($this->objetoCompleto()){
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA.self::STRING_SQL . ' VALUE ' . self::STRING_PARAMETROS);
            $consulta->bindParam(':nombre', $this->Usuario->getNombre());
            $consulta->bindParam(':apellido', $this->Usuario->getApellido());
            $consulta->bindParam(':cedula', $this->Usuario->getCedula());
            $consulta->bindParam(':correo', $this->Usuario->getCorreo());
            $consulta->bindParam(':tipo', $this->Usuario->getTipo());
            $consulta->bindParam(':clave', $this->Usuario->getClave());
            $consulta->execute();
            $this->Usuario->setId( $conexion->lastInsertId() );
            $conexion = null;
            if($this->Usuario->getId() != null && $this->Usuario->getId() != 0){
                return $this->Usuario;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function modificar()
    {
        $conexion = FabricaEntidad::Conexion();;
        if($this->objetoCompleto() && $this->Usuario->getId() != null &&  $this->Usuario->getId() != 0 ){
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA .' SET '.self::STRING_MODIFICA.' WHERE id = :id');

            $consulta->bindParam(':nombre', $this->Usuario->getNombre());
            $consulta->bindParam(':apellido', $this->Usuario->getApellido());
            $consulta->bindParam(':cedula', $this->Usuario->getCedula());
            $consulta->bindParam(':correo', $this->Usuario->getCorreo());
            $consulta->bindParam(':tipo', $this->Usuario->getTipo());
            $consulta->bindParam(':clave', $this->Usuario->getClave());
            $consulta->bindParam(':id', $this->Usuario->getId());
            $consulta->execute();
            $conexion = null;
            if($this->Usuario->getId() != null){
                return $this->Usuario;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function eliminar()
    {
        $conexion = FabricaEntidad::Conexion();;
        if($this->Usuario->getId() != 0 && $this->Usuario->getId() != null )
        {
            try{
                $consulta = $conexion->prepare( 'DELETE FROM ' . self::TABLA .' WHERE id = :parametro');
                $consulta->bindParam(':parametro', $this->Usuario->getId());
                $resultado = $consulta->execute();
                $conexion = null;
                return $resultado;

            }catch (Exception $e){
                $conexion = null;
                return false;
            }
        }else{
            return false;
        }
    }

    public function consultarXid()
    {
        $conexion = FabricaEntidad::Conexion();;
        if ( $this->Usuario->getId() != null )
        {
            $consulta = $conexion->prepare('SELECT * FROM '. self::TABLA . ' WHERE id = :id');
            $consulta->bindParam(':id', $this->Usuario->getId());
            $consulta->execute();
            $registro = $consulta->fetch();
            if($registro){
                $this->armarObjeto($registro);
            }else{
                $this->Usuario = null;
            }
        }else{
            $this->Usuario = null;
        }
        $conexion = null;
        return $this->Usuario;
    }

    public function consultarXLogin()
    {
        $conexion = FabricaEntidad::Conexion();;
        if ( $this->Usuario->getCorreo() != null )
        {
            $consulta = $conexion->prepare('SELECT * FROM '. self::TABLA . ' WHERE correo = :correo');
            $consulta->bindParam(':correo', $this->Usuario->getCorreo());
            $consulta->execute();
            $registro = $consulta->fetch();
            if($registro){
                $this->armarObjeto($registro);
            }else{
                $this->Usuario = null;
            }
        }else{
            $this->Usuario = null;
        }
        $conexion = null;
        return $this->Usuario;
    }

    public function obtenerTodos()
    {
        $conexion = FabricaEntidad::Conexion();;
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA );
        $consulta->execute();
        $Revisiones = $consulta->fetchAll();
        $conexion = null;
        $objetos = $this->armarListaObjetos($Revisiones);
        return $objetos;
    }

    public function obtenerTodoDesc()
    {
        $conexion = FabricaEntidad::Conexion();;
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA .' order by 1 desc ');
        $consulta->execute();
        $Revisiones = $consulta->fetchAll();
        $conexion = null;
        $objetos = $this->armarListaObjetos($Revisiones);
        return $objetos;
    }


    public function obtenerTodoAsc()
    {
        $conexion = FabricaEntidad::Conexion();;
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA );
        $consulta->execute();
        $Revisiones = $consulta->fetchAll();
        $conexion = null;
        $objetos = $this->armarListaObjetos($Revisiones);
        return $objetos;
    }


    public function consultarXParametro($parametro, $valor)
    {
        $conexion = FabricaEntidad::Conexion();;

        $consulta = $conexion->prepare('SELECT * FROM '. self::TABLA . ' WHERE '.$parametro.' = :id');
        $consulta->bindParam(':id', $valor);
        $consulta->execute();
        $registro = $consulta->fetch();
        if($registro){
            $this->armarObjeto($registro);
        }else{
            $this->Usuario = null;
        }
        $conexion = null;
        return $this->Usuario;

    }


    public function obtenerListaXParametro($parametro, $valor)
    {
        $conexion = FabricaEntidad::Conexion();;
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA .' WHERE '.$parametro.' = :id');
        $consulta->bindParam(':id', $valor);
        $consulta->execute();
        $Revisiones = $consulta->fetchAll();
        $conexion = null;
        $objetos = $this->armarListaObjetos($Revisiones);
        return $objetos;
    }



    private function objetoCompleto()
    {
        if( ($this->Usuario->getNombre() != null && $this->Usuario->getNombre() != '' )
            && ($this->Usuario->getApellido() != null && $this->Usuario->getApellido() != '' )
            && ($this->Usuario->getCedula() != null && $this->Usuario->getCedula() != '' )
            && ($this->Usuario->getCorreo() != null && $this->Usuario->getCorreo() != '' )
            && ($this->Usuario->getTipo() != null && $this->Usuario->getTipo() != '' )
            && ($this->Usuario->getClave() != null && $this->Usuario->getClave() != '' ))
        {
            return true;
        }else{
            return false;
        }
    }


    private function armarObjeto($registro)
    {
        $this->Usuario->setId($registro['id']);
        $this->Usuario->setNombre($registro['nombre']);
        $this->Usuario->setApellido($registro['apellido']);
        $this->Usuario->setCedula($registro['cedula']);
        $this->Usuario->setCorreo($registro['correo']);
        $this->Usuario->setTipo($registro['tipo']);
        $this->Usuario->setClave($registro['clave']);
    }

    private function armarListaObjetos($Revisiones)
    {
        $pila = array();
        for($i=0;$i<count($Revisiones);$i++){
            $objeto = FabricaEntidad::Usuario();

            $objeto->setId($Revisiones[$i]['id']);

            $objeto->setNombre($Revisiones[$i]['nombre']);
            $objeto->setApellido($Revisiones[$i]['apellido']);
            $objeto->setCedula($Revisiones[$i]['cedula']);
            $objeto->setCorreo($Revisiones[$i]['correo']);
            $objeto->setTipo($Revisiones[$i]['tipo']);
            $objeto->setClave($Revisiones[$i]['clave']);
            array_push($pila, $objeto);
        }

        return $pila;
    }

    private function idValidoIdObjeto($objeto)
    {
        if($objeto != null){
            return $objeto->getId();
        }else{
            return null;
        }
    }

}