<?php

namespace Webmasters\Doctrine\ORM;

/**
 * Class EntityValidator
 * @package Webmasters\Doctrine\ORM
 */
class EntityValidator {
    protected $em;
    protected $entity;
    protected $errors = [];

    /**
     * EntityValidator constructor.
     * @param $em
     * @param $entity
     * @throws \Exception
     */
    public function __construct($em, $entity) {
        $this->em = $em;
        $this->entity = $entity;

        if (!($em instanceof \Doctrine\ORM\EntityManager)) {
            throw new \Exception(
                sprintf(
                    'First constructor parameter of %s should be instanceof Doctrine\ORM\EntityManager',
                    get_class($this)
                )
            );
        }

        if (!is_object($entity)) {
            throw new \Exception(
                sprintf(
                    'Second constructor parameter of %s should be the object to validate',
                    get_class($this)
                )
            );
        }

        $this->executeValidation();
    }

    /**
     * @return mixed
     */
    public function getEntityManager(){
        return $this->em;
    }

    /**
     * @param null $class
     * @return mixed
     */
    public function getRepository($class = null){
        if (empty($class)) {
            $class = get_class($this->entity);
        }

        return $this->getEntityManager()->getRepository($class);
    }

    /**
     * @return mixed
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * @param $error
     */
    public function addError($error) {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return empty($this->errors);
    }

    /**
     * @throws \Exception
     */
    protected function executeValidation() {
        $requiredMethod = 'mapToArray'; // i.e. Trait ArrayMappable
        if (!method_exists($this->entity, $requiredMethod)) {
            throw new \Exception(sprintf('Method %s missing in entity', $requiredMethod));
        }
        
        $data = $this->entity->$requiredMethod(false, false);
        foreach ($data as $key => $val) {
            $validate = 'validate' . ucfirst($key);
            if (method_exists($this, $validate)) {
                $this->$validate($val);
            }
        }
    }
}
