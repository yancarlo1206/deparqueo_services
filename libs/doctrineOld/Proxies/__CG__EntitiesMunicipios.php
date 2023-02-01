<?php

namespace Proxies\__CG__\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Municipios extends \Entities\Municipios implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'muncoddep', 'muncodmun', 'muncodcen', 'nombre', 'centro');
        }

        return array('__isInitialized__', 'muncoddep', 'muncodmun', 'muncodcen', 'nombre', 'centro');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Municipios $proxy) {
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
    public function getMuncodcen()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMuncodcen', array());

        return parent::getMuncodcen();
    }

    /**
     * {@inheritDoc}
     */
    public function setMuncodcen($muncodcen)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMuncodcen', array($muncodcen));

        return parent::setMuncodcen($muncodcen);
    }

    /**
     * {@inheritDoc}
     */
    public function getMuncoddep()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getMuncoddep();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMuncoddep', array());

        return parent::getMuncoddep();
    }

    /**
     * {@inheritDoc}
     */
    public function setMuncoddep($muncoddep)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMuncoddep', array($muncoddep));

        return parent::setMuncoddep($muncoddep);
    }

    /**
     * {@inheritDoc}
     */
    public function getMuncodmun()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getMuncodmun();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMuncodmun', array());

        return parent::getMuncodmun();
    }

    /**
     * {@inheritDoc}
     */
    public function setMuncodmun($muncodmun)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMuncodmun', array($muncodmun));

        return parent::setMuncodmun($muncodmun);
    }

    /**
     * {@inheritDoc}
     */
    public function getNombre()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNombre', array());

        return parent::getNombre();
    }

    /**
     * {@inheritDoc}
     */
    public function setNombre($nombre)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNombre', array($nombre));

        return parent::setNombre($nombre);
    }

    /**
     * {@inheritDoc}
     */
    public function getCentro()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCentro', array());

        return parent::getCentro();
    }

    /**
     * {@inheritDoc}
     */
    public function setCentro($centro)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCentro', array($centro));

        return parent::setCentro($centro);
    }

}