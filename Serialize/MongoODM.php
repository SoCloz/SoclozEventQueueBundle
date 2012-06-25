<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\EventQueueBundle\Serialize;

/**
 * Description of Reflexion
 *
 * @author jfb
 */
class MongoODM implements SerializeInterface
{

    protected $dm;
    
    public function __construct($dm)
    {
        $this->dm = $dm;
    }

    public function serialize($event)
    {
        $reflect = new \ReflectionObject($event);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
        
        $scalarData = $objData = array();
        foreach ($props as $property) {
            $property->setAccessible(true);
            $attribute = $property->getName();
            $value = $property->getValue($event);
            if (is_object($value)) {
                if (method_exists($value, 'getId')) {
                    $objData[$attribute] = array(get_class($value), (string) $value->getId());
                }
            } else if (is_scalar($value)) {
                $scalarData[$attribute] = $value;
            }
        }
        return array(
            "class" => get_class($event),
            "scalar" => $scalarData,
            "objects" => $objData
        );
    }
    
    public function deserialize($data)
    {
        if (!is_array($data)) {
            return null;
        }
        $objData = $data['scalar'];
        foreach ($data['objects'] as $key => $value) {
            list($class, $id) = $value;
            $objData[$key] = $this->dm->find($class, new \MongoId($id));
        }
        
        $class = $data['class'];
        $refMethod = new \ReflectionMethod($class,  '__construct'); 
        $params = $refMethod->getParameters(); 
        $args = array(); 
    
        foreach($params as $param) { 
            $key = $param->getName();
            if (!isset($objData[$key])) {
                return null;
            }
            if ($param->isPassedByReference()){ 
                $args[$key] = &$objData[$key]; 
            } else { 
                $args[$key] = $objData[$key]; 
            } 
        } 
    
        $refClass = new \ReflectionClass($class); 
        return $refClass->newInstanceArgs((array) $args); 
    }
}
