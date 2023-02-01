<?php

namespace Proxies\__CG__\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class AsSalones extends \Entities\AsSalones implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'sal_Codigo', 'sede', 'sal_Bloque', 'sal_Piso', 'sal_Capacidad', 'sal_Largo', 'sal_Ancho', 'sal_Alias', 'sal_Nombre', 'tipo', 'sal_Activo', 'sal_Medios', 'sal_Descripcion');
        }

        return array('__isInitialized__', 'sal_Codigo', 'sede', 'sal_Bloque', 'sal_Piso', 'sal_Capacidad', 'sal_Largo', 'sal_Ancho', 'sal_Alias', 'sal_Nombre', 'tipo', 'sal_Activo', 'sal_Medios', 'sal_Descripcion');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (AsSalones $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getSal_Codigo()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getSal_Codigo();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Codigo', array());

        return parent::getSal_Codigo();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Codigo($sal_Codigo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Codigo', array($sal_Codigo));

        return parent::setSal_Codigo($sal_Codigo);
    }

    /**
     * {@inheritDoc}
     */
    public function getSede()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSede', array());

        return parent::getSede();
    }

    /**
     * {@inheritDoc}
     */
    public function setSede($sede)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSede', array($sede));

        return parent::setSede($sede);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Bloque()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Bloque', array());

        return parent::getSal_Bloque();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Bloque($sal_Bloque)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Bloque', array($sal_Bloque));

        return parent::setSal_Bloque($sal_Bloque);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Piso()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Piso', array());

        return parent::getSal_Piso();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Piso($sal_Piso)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Piso', array($sal_Piso));

        return parent::setSal_Piso($sal_Piso);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Capacidad()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Capacidad', array());

        return parent::getSal_Capacidad();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Capacidad($sal_Capacidad)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Capacidad', array($sal_Capacidad));

        return parent::setSal_Capacidad($sal_Capacidad);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Largo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Largo', array());

        return parent::getSal_Largo();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Largo($sal_Largo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Largo', array($sal_Largo));

        return parent::setSal_Largo($sal_Largo);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Ancho()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Ancho', array());

        return parent::getSal_Ancho();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Ancho($sal_Ancho)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Ancho', array($sal_Ancho));

        return parent::setSal_Ancho($sal_Ancho);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Alias()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Alias', array());

        return parent::getSal_Alias();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Alias($sal_Alias)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Alias', array($sal_Alias));

        return parent::setSal_Alias($sal_Alias);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Nombre()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Nombre', array());

        return parent::getSal_Nombre();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Nombre($sal_Nombre)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Nombre', array($sal_Nombre));

        return parent::setSal_Nombre($sal_Nombre);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Activo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Activo', array());

        return parent::getSal_Activo();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Activo($sal_Activo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Activo', array($sal_Activo));

        return parent::setSal_Activo($sal_Activo);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Medios()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Medios', array());

        return parent::getSal_Medios();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Medios($sal_Medios)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Medios', array($sal_Medios));

        return parent::setSal_Medios($sal_Medios);
    }

    /**
     * {@inheritDoc}
     */
    public function getSal_Descripcion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSal_Descripcion', array());

        return parent::getSal_Descripcion();
    }

    /**
     * {@inheritDoc}
     */
    public function setSal_Descripcion($sal_Descripcion)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSal_Descripcion', array($sal_Descripcion));

        return parent::setSal_Descripcion($sal_Descripcion);
    }

    /**
     * {@inheritDoc}
     */
    public function getTipo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTipo', array());

        return parent::getTipo();
    }

    /**
     * {@inheritDoc}
     */
    public function setTipo($tipo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTipo', array($tipo));

        return parent::setTipo($tipo);
    }

}
