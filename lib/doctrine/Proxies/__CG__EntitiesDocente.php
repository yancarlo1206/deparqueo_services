<?php

namespace Proxies\__CG__\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Docente extends \Entities\Docente implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'codDocente', 'persona', 'password', 'fechaRegistro', 'estado', 'relDocentes');
        }

        return array('__isInitialized__', 'codDocente', 'persona', 'password', 'fechaRegistro', 'estado', 'relDocentes');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Docente $proxy) {
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
    public function getCodDocente()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getCodDocente();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCodDocente', array());

        return parent::getCodDocente();
    }

    /**
     * {@inheritDoc}
     */
    public function setCodDocente($codDocente)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCodDocente', array($codDocente));

        return parent::setCodDocente($codDocente);
    }

    /**
     * {@inheritDoc}
     */
    public function getPersona()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPersona', array());

        return parent::getPersona();
    }

    /**
     * {@inheritDoc}
     */
    public function setPersona(\Entities\Persona $persona)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPersona', array($persona));

        return parent::setPersona($persona);
    }

    /**
     * {@inheritDoc}
     */
    public function getClave()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClave', array());

        return parent::getClave();
    }

    /**
     * {@inheritDoc}
     */
    public function setClave($password)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClave', array($password));

        return parent::setClave($password);
    }

    /**
     * {@inheritDoc}
     */
    public function getEstado()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEstado', array());

        return parent::getEstado();
    }

    /**
     * {@inheritDoc}
     */
    public function setEstado($estado)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEstado', array($estado));

        return parent::setEstado($estado);
    }

    /**
     * {@inheritDoc}
     */
    public function getRelDocentes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRelDocentes', array());

        return parent::getRelDocentes();
    }

    /**
     * {@inheritDoc}
     */
    public function setRelDocentes($relDocentes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRelDocentes', array($relDocentes));

        return parent::setRelDocentes($relDocentes);
    }

    /**
     * {@inheritDoc}
     */
    public function getProgramas()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProgramas', array());

        return parent::getProgramas();
    }

    /**
     * {@inheritDoc}
     */
    public function setProgramas($programas)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProgramas', array($programas));

        return parent::setProgramas($programas);
    }

    /**
     * {@inheritDoc}
     */
    public function getCodigoYNombre()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCodigoYNombre', array());

        return parent::getCodigoYNombre();
    }

    /**
     * {@inheritDoc}
     */
    public function getNombreCompleto()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNombreCompleto', array());

        return parent::getNombreCompleto();
    }

}
