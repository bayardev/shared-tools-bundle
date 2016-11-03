<?php

/*
 * This file is part of the Bayard PIM.
 * Powered by Akeneo PIM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * and the LICENSE file distributed with akeneo/pim-community-dev package
 */

namespace Bayard\Bundle\SharedToolsBundle\Inspector;

use Doctrine\Common\Annotations\AnnotationReader;
use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as BayardException;

/**
 * Abstract Class Mapper for Custom Entities (Pim Reference Data)
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
abstract class AbstractEntityInspector
{
    /**
     * @var null | (Doctrine Entity)
     */
    protected $entity = null;

    /**
     * @var null | AnnotationReader
     */
    protected $docReader = null;

    /**
     * @var null | ReflectionClass
     */
    protected $reflect = null;

    /**
     * @var array
     */
    protected $inspectionTools = ['docReader', 'reflect'];

    /**
     * method setEntity must set the entity namespac in the inherit class
     * @example : $this->entity = Acme\Bundle\Entity\MyEntity;
     */
    abstract protected function setEntity();

    /**
     * AbstractReferenceDataMapper Constructor
     */
    public function __construct()
    {
        $this->setEntity();
    }

    /**
     * get AnnotationReader
     * @return AnnotationReader
     */
    protected function getDocReader()
    {
        if (is_null($this->docReader)) {
            $this->docReader = new AnnotationReader();
        }

        return $this->docReader;
    }

    /**
     * get ReflectionClass of Entity
     * @return ReflectionClass
     */
    protected function getReflect()
    {
        if (is_null($this->reflect)) {
            $this->reflect = new \ReflectionClass($this->entity);
        }

        return $this->reflect;
    }

    /**
     * set protected properties $docReader and $reflect
     * if there are not setted yet
     * @return null
     */
    protected function getInspectionTools($filter = "")
    {
        if (!empty($filter) && in_array($filter, $this->inspectionTools, true)) {
            call_user_func(array($this, 'get'.ucfirst($filter)));
        } else {
            foreach ($this->inspectionTools as $tool) {
                call_user_func(array($this, 'get'.ucfirst($tool)));
            }
        }
    }

    /**
     * get the Type of $this->entity property by his name
     * @param  string $property
     * @return string  the ptoperty's type
     */
    public function getDoctrinePropertyType($property)
    {
        $this->getInspectionTools();

        if (!$this->reflect->hasProperty($property)) {
            throw BayardException::entityPropertyNotFound($this->entity, $property);
        }

        $docInfos = $this->docReader->getPropertyAnnotations($this->reflect->getProperty($property));

        return $docInfos[0]->type;
    }

    /**
     * get Types of a list of $this->entity properties
     * @param  Array $properties : list of properties names
     * @return Array $properties_types : array with properies names as keys and types as values
     */
    public function getDoctrinePropertiesTypes(Array $properties = array())
    {
        $properties_types = array();

        foreach ($properties as $property) {
            $properties_types[$property] = $this->getDoctrinePropertyType($property);
        }

        return $properties_types;
    }

    /**
     * Get type of property from property declaration
     *
     * @param \ReflectionProperty $property
     *
     * @return null|string
     */
    public function getPropertyType(\ReflectionProperty $property)
    {
        $doc = $property->getDocComment();
        preg_match_all('#@(.*?)\*#s', $doc, $annotations);
        if (isset($annotations[1])) {
            foreach ($annotations[1] as $annotation) {
                preg_match_all('#\s*(.*?)\s+#s', $annotation, $parts);
                if (!isset($parts[1])) {
                    continue;
                }
                $declaration = $parts[1];
                if (isset($declaration[0]) && $declaration[0] === 'var') {
                    if (isset($declaration[1])) {
                        if (substr($declaration[1], 0, 1) === '$') {
                            return null;
                        }
                        else {
                            return $declaration[1];
                        }
                    }
                }
            }
            return null;
        }
        return $doc;
    }

    public function getPropertiesTypes(Array $properties = array())
    {
        $this->getInspectionTools();
        $properties_types = array();

        foreach ($properties as $property) {
            if (!$this->reflect->hasProperty($property)) {
                throw BayardException::entityPropertyNotFound($this->entity, $property);
            }

            $properties_types[$property] = $this->getPropertyType($this->reflect->getProperty($property));
        }

        return $properties_types;
    }

    /**
     * [getPropertiesList description]
     * @return [type] [description]
     */
    public function getPropertiesList()
    {
        $reflect_properties = $this->getReflect()->getProperties();

        $properties_names = array();
        foreach ($reflect_properties as $ref_property) {
            $properties_names[] = $ref_property->getName();
        }

        return $properties_names;
    }

    /**
     * [processPHPDoc description]
     * @param  ReflectionMethod $reflect [description]
     * @return [type]                    [description]
     */
    protected function processMethodPHPDoc(ReflectionMethod $reflect)
    {
        $phpDoc = array('params' => array(), 'return' => null);
        $docComment = $reflect->getDocComment();
        if (trim($docComment) == '') {
            return null;
        }
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment = ltrim($docComment, "\r\n");
        $parsedDocComment = $docComment;
        $lineNumber = $firstBlandLineEncountered = 0;
        while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
            $lineNumber++;
            $line = substr($parsedDocComment, 0, $newlinePos);

            $matches = array();
            if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
                $tagDocblockLine = $matches[1];
                $matches2 = array();

                if (!preg_match('#^@(\w+)(\s|$)#', $tagDocblockLine, $matches2)) {
                    break;
                }
                $matches3 = array();
                if (!preg_match('#^@(\w+)\s+([\w|\\\]+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $tagDocblockLine, $matches3)) {
                    break;
                }
                if ($matches3[1] != 'param') {
                    if (strtolower($matches3[1]) == 'return') {
                        $phpDoc['return'] = array('type' => $matches3[2]);
                    }
                } else {
                    $phpDoc['params'][] = array('name' => $matches3[3], 'type' => $matches3[2]);
                }

                $parsedDocComment = str_replace($matches[1] . $matches[2], '', $parsedDocComment);
            }
        }
        return $phpDoc;
    }

}
